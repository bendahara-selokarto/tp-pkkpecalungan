import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

const e2eEmail = process.env.E2E_EMAIL ?? '';
const e2ePassword = process.env.E2E_PASSWORD ?? '';
const hasAuthCredentials = e2eEmail !== '' && e2ePassword !== '';

const isActionableConsoleError = (message) => {
  if (typeof message !== 'string' || message.trim() === '') {
    return false;
  }

  const normalized = message.toLowerCase();
  return !normalized.includes('favicon.ico');
};

test('@smoke login page renders form controls', async ({ page }) => {
  await page.goto('/login');

  await expect(page.locator('#email')).toBeVisible();
  await expect(page.locator('#password')).toBeVisible();
  await expect(page.locator('button[type="submit"]')).toBeVisible();
});

test('@a11y login page has no serious or critical axe violations', async ({ page }) => {
  await page.goto('/login');

  const accessibilityScan = await new AxeBuilder({ page })
    .disableRules(['color-contrast'])
    .analyze();

  const seriousViolations = accessibilityScan.violations.filter((violation) =>
    ['serious', 'critical'].includes(String(violation.impact ?? ''))
  );

  expect(seriousViolations).toEqual([]);
});

test.describe('authenticated runtime smoke', () => {
  test.skip(!hasAuthCredentials, 'Set E2E_EMAIL and E2E_PASSWORD to enable authenticated smoke.');

  test('@smoke logs in and reaches app shell without runtime JS errors', async ({ page }) => {
    const runtimeErrors = [];

    page.on('pageerror', (error) => {
      runtimeErrors.push(`pageerror: ${error.message}`);
    });

    page.on('console', (message) => {
      if (message.type() === 'error' && isActionableConsoleError(message.text())) {
        runtimeErrors.push(`console: ${message.text()}`);
      }
    });

    await page.goto('/login');
    await page.locator('#email').fill(e2eEmail);
    await page.locator('#password').fill(e2ePassword);
    await page.locator('button[type="submit"]').click();

    await expect(page).toHaveURL(/\/(dashboard|super-admin\/users)(\?.*)?$/);
    await expect(page.getByRole('button', { name: 'Keluar' })).toBeVisible();
    await expect(page.getByText('Terjadi gangguan antarmuka karena error JavaScript.')).toBeHidden();

    if (page.url().includes('/dashboard')) {
      await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();
      await expect(page.getByRole('button', { name: 'Terapkan Filter Chart' })).toBeVisible();
      await page.getByRole('button', { name: 'Terapkan Filter Chart' }).click();
      await expect(page).toHaveURL(/\/dashboard(\?.*)?$/);
      const pdfLink = page.getByRole('link', { name: 'Cetak Chart PDF' });
      await expect(pdfLink).toBeVisible();
      await expect(pdfLink).toHaveAttribute('href', /\/dashboard\/charts\/report\/pdf(\?.*)?$/);
    }

    if (page.url().includes('/super-admin/users')) {
      await expect(page.getByRole('link', { name: 'Manajemen User' })).toBeVisible();
      await page.getByRole('link', { name: 'Manajemen User' }).click();
      await expect(page).toHaveURL(/\/super-admin\/users(\?.*)?$/);
    }

    await page.getByRole('button', { name: 'Keluar' }).click();
    await expect(page).toHaveURL(/\/login$/);
    expect(runtimeErrors).toEqual([]);
  });

  test('@a11y authenticated app shell has no serious or critical axe violations', async ({ page }) => {
    await page.goto('/login');
    await page.locator('#email').fill(e2eEmail);
    await page.locator('#password').fill(e2ePassword);
    await page.locator('button[type="submit"]').click();
    await expect(page).toHaveURL(/\/(dashboard|super-admin\/users)(\?.*)?$/);

    const accessibilityScan = await new AxeBuilder({ page })
      .disableRules(['color-contrast'])
      .analyze();

    const seriousViolations = accessibilityScan.violations.filter((violation) =>
      ['serious', 'critical'].includes(String(violation.impact ?? ''))
    );

    expect(seriousViolations).toEqual([]);
  });
});
