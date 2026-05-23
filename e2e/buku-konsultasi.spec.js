import { expect, test } from '@playwright/test';

const normalizeCredential = (value) => String(value ?? '').trim();
const requireAuthRuntime = process.env.E2E_REQUIRE_AUTH === '1';

const roleCredentials = {
  kecamatan: {
    email: normalizeCredential(process.env.E2E_KECAMATAN_EMAIL),
    password: normalizeCredential(process.env.E2E_KECAMATAN_PASSWORD),
  },
};

const login = async (page, role, expectedPathPattern) => {
  const credentials = roleCredentials[role];
  await page.goto('/login');
  await page.locator('#email').fill(credentials.email);
  await page.locator('#password').fill(credentials.password);
  await page.locator('button[type="submit"]').click();
  await page.waitForURL(expectedPathPattern, { timeout: 30000, waitUntil: 'domcontentloaded' });
};

const uniqueToken = () => `${Date.now()}-${Math.floor(Math.random() * 10000)}`;

test.describe('Buku Konsultasi CRUD', () => {
  test.skip(
    !requireAuthRuntime || normalizeCredential(process.env.E2E_KECAMATAN_EMAIL) === '',
    'Set E2E_REQUIRE_AUTH=1 and E2E_KECAMATAN_EMAIL to execute Buku Konsultasi CRUD tests.',
  );

  test('@buku-konsultasi kecamatan: create + delete', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'CRUD tests are enforced on desktop project only.');

    const token = uniqueToken();
    const deskripsi = `E2E Konsultasi ${token}`;
    const disposisi = `Disposisi E2E ${token}`;

    await login(page, 'kecamatan', /\/dashboard(\?.*)?$/);

    await page.goto('/kecamatan/buku-konsultasi');
    await expect(page).toHaveURL(/\/kecamatan\/buku-konsultasi(\?.*)?$/);

    await page.getByRole('link', { name: '+ Tambah Catatan' }).click();
    await expect(page).toHaveURL(/\/kecamatan\/buku-konsultasi\/create$/);

    await page.locator('label:has-text("Hari / Tanggal") + input').fill('2026-05-23');
    await page.locator('label:has-text("Uraian Kegiatan") + textarea').fill(deskripsi);
    await page.locator('label:has-text("Disposisi") + textarea').fill(disposisi);

    await page.getByRole('button', { name: 'Simpan' }).click();

    await expect(page).toHaveURL(/\/kecamatan\/buku-konsultasi(\?.*)?$/);
    await expect(page.getByText('Buku konsultasi berhasil ditambahkan')).toBeVisible();
    await expect(page.getByText(deskripsi)).toBeVisible();

    const row = page.locator('tbody tr').filter({ hasText: deskripsi }).first();
    await row.getByRole('link', { name: 'Lihat' }).click();
    await expect(page.getByText(deskripsi)).toBeVisible();
    await expect(page.getByText(disposisi)).toBeVisible();

    await page.getByRole('link', { name: 'Kembali' }).click();
    await expect(page).toHaveURL(/\/kecamatan\/buku-konsultasi(\?.*)?$/);

    await row.getByRole('button', { name: 'Hapus' }).click();
    await expect(page.getByText('Konfirmasi Hapus')).toBeVisible();
    await page.getByRole('button', { name: 'Ya, Hapus' }).click();
    await expect(page.getByText('Buku konsultasi berhasil dihapus')).toBeVisible();
    await expect(page.locator('tbody tr').filter({ hasText: deskripsi })).toHaveCount(0);
  });
});
