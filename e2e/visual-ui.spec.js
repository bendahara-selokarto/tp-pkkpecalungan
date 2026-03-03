import { expect, test } from '@playwright/test';

const normalizeCredential = (value) => String(value ?? '').trim();
const roleCredentials = {
  desa: {
    email: normalizeCredential(process.env.E2E_DESA_EMAIL),
    password: normalizeCredential(process.env.E2E_DESA_PASSWORD),
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

const login = async (page, role, expectedPath) => {
  const credentials = roleCredentials[role];
  await page.goto('/login');
  await page.locator('#email').fill(credentials.email);
  await page.locator('#password').fill(credentials.password);
  await page.locator('button[type="submit"]').click();
  await page.waitForURL(expectedPath, { timeout: 20_000 });
};

const waitForStableUi = async (page) => {
  await page.waitForLoadState('networkidle');
  const progressBar = page.locator('#nprogress .bar');
  try {
    await expect(progressBar).toBeHidden({ timeout: 2_000 });
  } catch (_error) {
    // Ignore if progress bar node is not rendered; networkidle is the primary stability gate.
  }
};

test.describe('runtime visual baseline', () => {
  test('@visual login page baseline', async ({ page }) => {
    await page.goto('/login');
    await waitForStableUi(page);

    await expect(page.locator('form').first()).toHaveScreenshot('login-form.png', {
      animations: 'disabled',
      caret: 'hide',
      scale: 'css',
    });
  });

  test('@visual dashboard baseline (desa)', async ({ page }) => {
    test.skip(!hasCredentialsForRole('desa'), 'Set desa credentials to capture dashboard visual baseline.');

    await login(page, 'desa', /\/dashboard(\?.*)?$/);
    await waitForStableUi(page);

    await expect(page.locator('main')).toHaveScreenshot('dashboard-desa-page.png', {
      animations: 'disabled',
      caret: 'hide',
      scale: 'css',
      maxDiffPixelRatio: 0.02,
    });
  });

  test('@visual super-admin users baseline', async ({ page }) => {
    test.skip(!hasCredentialsForRole('super-admin'), 'Set super-admin credentials to capture users visual baseline.');

    await login(page, 'super-admin', /\/super-admin\/users(\?.*)?$/);
    await waitForStableUi(page);

    await expect(page.locator('main')).toHaveScreenshot('superadmin-users-page.png', {
      animations: 'disabled',
      caret: 'hide',
      scale: 'css',
      maxDiffPixelRatio: 0.02,
    });
  });
});
