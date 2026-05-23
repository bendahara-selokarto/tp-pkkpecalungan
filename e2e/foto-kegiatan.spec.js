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

test.describe('Foto Kegiatan CRUD', () => {
  test.skip(
    !requireAuthRuntime || normalizeCredential(process.env.E2E_KECAMATAN_EMAIL) === '',
    'Set E2E_REQUIRE_AUTH=1 and E2E_KECAMATAN_EMAIL to execute Foto Kegiatan CRUD tests.',
  );

  test('@foto-kegiatan kecamatan: create + delete', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'CRUD tests are enforced on desktop project only.');

    const token = uniqueToken();
    const judul = `E2E Foto Kegiatan ${token}`;
    const deskripsi = `Deskripsi E2E Foto Kegiatan ${token}`;

    await login(page, 'kecamatan', /\/dashboard(\?.*)?$/);

    await page.goto('/kecamatan/foto-kegiatan');
    await expect(page).toHaveURL(/\/kecamatan\/foto-kegiatan(\?.*)?$/);

    await page.getByRole('link', { name: '+ Unggah Foto' }).click();
    await expect(page).toHaveURL(/\/kecamatan\/foto-kegiatan\/create$/);

    await page.locator('label:has-text("Judul Kegiatan") + input').fill(judul);
    await page.locator('label:has-text("Tanggal Kegiatan") + input').fill('2026-05-23');
    await page.locator('label:has-text("Keterangan (Opsional)") + textarea').fill(deskripsi);
    
    // Upload a mock image
    await page.locator('input[type="file"]').setInputFiles({
      name: 'test-image.png',
      mimeType: 'image/png',
      buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==', 'base64'),
    });

    await page.getByRole('button', { name: 'Simpan' }).click();

    await expect(page).toHaveURL(/\/kecamatan\/foto-kegiatan(\?.*)?$/);
    await expect(page.getByText('Foto kegiatan berhasil diunggah')).toBeVisible();
    await expect(page.getByText(judul)).toBeVisible();

    const row = page.locator('tbody tr').filter({ hasText: judul }).first();
    await row.getByRole('link', { name: 'Lihat' }).click();
    await expect(page.getByText(judul)).toBeVisible();
    await expect(page.getByText(deskripsi)).toBeVisible();

    await page.getByRole('link', { name: 'Kembali' }).click();
    await expect(page).toHaveURL(/\/kecamatan\/foto-kegiatan(\?.*)?$/);

    await row.getByRole('button', { name: 'Hapus' }).click();
    await expect(page.getByText('Konfirmasi Hapus')).toBeVisible();
    await page.getByRole('button', { name: 'Ya, Hapus' }).click();
    await expect(page.getByText('Foto kegiatan berhasil dihapus')).toBeVisible();
    await expect(page.locator('tbody tr').filter({ hasText: judul })).toHaveCount(0);
  });
});
