import { expect, test } from '@playwright/test';

const normalizeCredential = (value) => String(value ?? '').trim();
const requireAuthRuntime = process.env.E2E_REQUIRE_AUTH === '1';

const roleCredentials = {
  kecamatan: {
    email: normalizeCredential(process.env.E2E_KECAMATAN_EMAIL),
    password: normalizeCredential(process.env.E2E_KECAMATAN_PASSWORD),
  },
  'super-admin': {
    email: normalizeCredential(process.env.E2E_SUPERADMIN_EMAIL || process.env.E2E_EMAIL),
    password: normalizeCredential(process.env.E2E_SUPERADMIN_PASSWORD || process.env.E2E_PASSWORD),
  },
};

const hasCredentialsForRole = (role) => {
  const credentials = roleCredentials[role];
  return credentials.email !== '' && credentials.password !== '';
};

const assertCredentialsForRole = (role) => {
  if (!requireAuthRuntime) {
    return;
  }

  if (hasCredentialsForRole(role)) {
    return;
  }

  throw new Error(`E2E credentials are required for role ${role} when E2E_REQUIRE_AUTH=1.`);
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

test.describe('runtime smoke crud priorities', () => {
  test.describe.configure({ mode: 'serial' });

  test.beforeAll(() => {
    assertCredentialsForRole('kecamatan');
    assertCredentialsForRole('super-admin');
  });

  test.skip(
    !requireAuthRuntime || !hasCredentialsForRole('kecamatan') || !hasCredentialsForRole('super-admin'),
    'Set E2E_REQUIRE_AUTH=1 and required role credentials to execute CRUD smoke lane.',
  );

  test('@smoke crud activities kecamatan: create + filter + pagination + delete guard', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'CRUD smoke lane is enforced on desktop project only.');

    const token = uniqueToken();
    const namaPetugas = `E2E Kec Activity ${token}`;
    const judul = `Judul Activity ${token}`;

    await login(page, 'kecamatan', /\/dashboard(\?.*)?$/);

    await page.goto('/kecamatan/activities');
    await expect(page).toHaveURL(/\/kecamatan\/activities(\?.*)?$/);

    const desaMonitoringRadio = page.getByRole('radio', { name: 'Desa (Monitoring)' });
    const kecamatanRadio = page.getByRole('radio', { name: 'Kecamatan' });

    if (await desaMonitoringRadio.isVisible().catch(() => false)) {
      await desaMonitoringRadio.check();
      await expect(desaMonitoringRadio).toBeChecked();

      try {
        await expect(page).toHaveURL(/\/kecamatan\/desa-activities(\?.*)?$/);
        await kecamatanRadio.check();
        await expect(page).toHaveURL(/\/kecamatan\/activities(\?.*)?$/);
      } catch (_error) {
        await page.goto('/kecamatan/activities');
        await expect(page).toHaveURL(/\/kecamatan\/activities(\?.*)?$/);
      }
    }

    const activitiesPerPageSelect = page.locator('label:has-text("Per halaman") select');
    await activitiesPerPageSelect.selectOption({ index: 1 });
    const selectedActivitiesPerPage = await activitiesPerPageSelect.inputValue();
    await expect(page).toHaveURL(new RegExp(`/kecamatan/activities\\?per_page=${selectedActivitiesPerPage}$`));

    await page.goto('/kecamatan/activities/create');
    await expect(page).toHaveURL(/\/kecamatan\/activities\/create$/);

    await page.locator('label:has-text("Judul Kegiatan") + input').fill(judul);
    await page.locator('label:has-text("Nama Bertugas") + input').fill(namaPetugas);
    await page.locator('label:has-text("Tanggal Kegiatan") + input').fill('2026-03-03');
    await page.getByRole('button', { name: 'Simpan' }).click();

    await expect(page).toHaveURL(/\/kecamatan\/activities(\?.*)?$/);
    await expect(page.getByText(namaPetugas)).toBeVisible();

    const row = page.locator('tbody tr').filter({ hasText: namaPetugas }).first();
    await row.getByRole('button', { name: 'Hapus' }).click();
    await expect(page.getByText('Konfirmasi Hapus')).toBeVisible();
    await page.getByRole('button', { name: 'Batal' }).click();
    await expect(row).toContainText(namaPetugas);

    await row.getByRole('button', { name: 'Hapus' }).click();
    await page.getByRole('button', { name: 'Ya, Hapus' }).click();
    await expect(page.locator('tbody tr').filter({ hasText: namaPetugas })).toHaveCount(0);
  });

  test('@smoke crud agenda surat kecamatan: create + pagination + delete guard', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'CRUD smoke lane is enforced on desktop project only.');

    const token = uniqueToken();
    const nomorSurat = `E2E/AGS/${token}`;
    const perihal = `Perihal E2E ${token}`;

    await login(page, 'kecamatan', /\/dashboard(\?.*)?$/);

    await page.goto('/kecamatan/agenda-surat');
    await expect(page).toHaveURL(/\/kecamatan\/agenda-surat(\?.*)?$/);

    const agendaPerPageSelect = page.locator('label:has-text("Per halaman") select');
    await agendaPerPageSelect.selectOption({ index: 1 });
    const selectedAgendaPerPage = await agendaPerPageSelect.inputValue();
    await expect(page).toHaveURL(new RegExp(`/kecamatan/agenda-surat\\?per_page=${selectedAgendaPerPage}$`));

    await page.goto('/kecamatan/agenda-surat/create');
    await expect(page).toHaveURL(/\/kecamatan\/agenda-surat\/create$/);

    await page.locator('label:has-text("Nomor Surat") + input').fill(nomorSurat);
    await page.locator('label:has-text("Tanggal Surat") + input').fill('2026-03-03');
    await page.locator('label:has-text("Tanggal Terima") + input').fill('2026-03-03');
    await page.locator('label:has-text("Asal Surat") + input').fill('Sekretariat E2E');
    await page.locator('label:has-text("Dari") + input').fill('Pengirim E2E');
    await page.locator('label:has-text("Perihal") + input').fill(perihal);
    await page.getByRole('button', { name: 'Simpan' }).click();

    await expect(page.getByText('Agenda surat berhasil dibuat')).toBeVisible();
    await expect(page.locator('tbody tr').filter({ hasText: nomorSurat }).first()).toBeVisible();

    const row = page.locator('tbody tr').filter({ hasText: nomorSurat }).first();
    await row.getByRole('button', { name: 'Hapus' }).click();
    await expect(page.getByText('Konfirmasi Hapus')).toBeVisible();
    await page.getByRole('button', { name: 'Batal' }).click();
    await expect(row).toContainText(nomorSurat);

    await row.getByRole('button', { name: 'Hapus' }).click();
    await page.getByRole('button', { name: 'Ya, Hapus' }).click();
    await expect(page.locator('tbody tr').filter({ hasText: nomorSurat })).toHaveCount(0);
  });

  test('@smoke crud arsip super-admin: create + pagination + delete guard', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'CRUD smoke lane is enforced on desktop project only.');

    const token = uniqueToken();
    const title = `E2E Arsip ${token}`;

    await login(page, 'super-admin', /\/super-admin\/users(\?.*)?$/);

    await page.goto('/super-admin/arsip');
    await expect(page).toHaveURL(/\/super-admin\/arsip(\?.*)?$/);

    const arsipPerPageSelect = page.locator('label:has-text("Per halaman") select');
    await arsipPerPageSelect.selectOption({ index: 1 });
    const selectedArsipPerPage = await arsipPerPageSelect.inputValue();
    await expect(page).toHaveURL(new RegExp(`/super-admin/arsip\\?page=1&per_page=${selectedArsipPerPage}$`));

    await page.goto('/super-admin/arsip/create');
    await expect(page).toHaveURL(/\/super-admin\/arsip\/create$/);

    await page.locator('label:has-text("Judul") + input').fill(title);
    await page.locator('label:has-text("Deskripsi (opsional)") + textarea').fill('Dokumen uji smoke CRUD runtime');
    await page.locator('label:has-text("File Dokumen") + input[type="file"]').setInputFiles({
      name: 'e2e-smoke.pdf',
      mimeType: 'application/pdf',
      buffer: Buffer.from('%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF'),
    });
    await page.getByRole('button', { name: 'Simpan' }).click();

    await expect(page).toHaveURL(/\/super-admin\/arsip(\?.*)?$/);
    const row = page.locator('tbody tr').filter({ hasText: title }).first();
    await expect(row).toBeVisible();
    await row.getByRole('button', { name: 'Hapus' }).click();
    await expect(page.getByText('Konfirmasi Hapus')).toBeVisible();
    await page.getByRole('button', { name: 'Batal' }).click();
    await expect(row).toContainText(title);

    await row.getByRole('button', { name: 'Hapus' }).click();
    await page.getByRole('button', { name: 'Ya, Hapus' }).click();
    await expect(page.locator('tbody tr').filter({ hasText: title })).toHaveCount(0);
  });
});
