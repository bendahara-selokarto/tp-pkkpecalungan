import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

const normalizeCredential = (value) => String(value ?? '').trim();
const requireAuthRuntime = process.env.E2E_REQUIRE_AUTH === '1';
const requireAuthA11yRuntime = process.env.E2E_REQUIRE_AUTH_A11Y === '1';
const excludeNprogressInA11y = process.env.E2E_A11Y_EXCLUDE_NPROGRESS !== '0';
const disableColorContrastRule = process.env.E2E_A11Y_DISABLE_COLOR_CONTRAST !== '0';

const buildA11yScan = async (page) => {
  let builder = new AxeBuilder({ page });

  if (excludeNprogressInA11y) {
    builder = builder.exclude('#nprogress');
  }

  if (disableColorContrastRule) {
    builder = builder.disableRules(['color-contrast']);
  }

  return builder.analyze();
};

const roleCredentials = {
  desa: {
    email: normalizeCredential(process.env.E2E_DESA_EMAIL),
    password: normalizeCredential(process.env.E2E_DESA_PASSWORD),
  },
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

const isActionableConsoleError = (message) => {
  if (typeof message !== 'string' || message.trim() === '') {
    return false;
  }

  return !message.toLowerCase().includes('favicon.ico');
};

const captureRuntimeErrors = (page) => {
  const runtimeErrors = [];

  page.on('pageerror', (error) => {
    runtimeErrors.push(`pageerror: ${error.message}`);
  });

  page.on('console', (message) => {
    if (message.type() === 'error' && isActionableConsoleError(message.text())) {
      runtimeErrors.push(`console: ${message.text()}`);
    }
  });

  return runtimeErrors;
};

const login = async (page, role, expectedPath) => {
  const credentials = roleCredentials[role];
  const maxAttempts = 2;

  for (let attempt = 1; attempt <= maxAttempts; attempt += 1) {
    await page.goto('/login');
    await page.locator('#email').fill(credentials.email);
    await page.locator('#password').fill(credentials.password);
    await page.locator('button[type="submit"]').click();

    try {
      await page.waitForURL(expectedPath, { timeout: 15000 });
      return;
    } catch (error) {
      if (attempt === maxAttempts) {
        throw error;
      }
    }
  }
};

test('@smoke login page renders form controls', async ({ page }) => {
  await page.goto('/login');

  await expect(page.locator('#email')).toBeVisible();
  await expect(page.locator('#password')).toBeVisible();
  await expect(page.locator('button[type="submit"]')).toBeVisible();
});

test('@a11y login page has no serious or critical axe violations', async ({ page }) => {
  await page.goto('/login');

  const accessibilityScan = await buildA11yScan(page);

  const seriousViolations = accessibilityScan.violations.filter((violation) =>
    ['serious', 'critical'].includes(String(violation.impact ?? ''))
  );

  expect(seriousViolations).toEqual([]);
});

const roleMatrix = [
  {
    role: 'desa',
    expectedPath: /\/dashboard(\?.*)?$/,
    shellAssertion: async (page) => {
      await expect(page.getByRole('heading', { name: 'Dashboard', exact: true })).toBeVisible();
      await expect(page.getByRole('button', { name: 'Terapkan Filter Chart' })).toBeVisible();
      await page.getByRole('button', { name: 'Terapkan Filter Chart' }).click();
      await expect(page).toHaveURL(/\/dashboard(\?.*)?$/);
      await expect(page.getByRole('link', { name: 'Cetak Chart PDF' })).toBeVisible();
    },
  },
  {
    role: 'kecamatan',
    expectedPath: /\/dashboard(\?.*)?$/,
    shellAssertion: async (page) => {
      await expect(page.getByRole('heading', { name: 'Dashboard', exact: true })).toBeVisible();
      await expect(page.getByRole('button', { name: 'Terapkan Filter Chart' })).toBeVisible();
      await page.getByRole('button', { name: 'Terapkan Filter Chart' }).click();
      await expect(page).toHaveURL(/\/dashboard(\?.*)?$/);
      await expect(page.getByRole('link', { name: 'Cetak Chart PDF' })).toHaveAttribute(
        'href',
        /\/dashboard\/charts\/report\/pdf(\?.*)?$/,
      );
    },
  },
  {
    role: 'super-admin',
    expectedPath: /\/super-admin\/users(\?.*)?$/,
    shellAssertion: async (page) => {
      await expect(page.getByRole('link', { name: 'Manajemen User' })).toBeVisible();
      await expect(page.getByRole('link', { name: 'Management Ijin Akses' })).toBeVisible();
      await expect(page.getByRole('link', { name: 'Management Arsip' })).toBeVisible();
    },
  },
];

for (const roleConfig of roleMatrix) {
  test.describe(`authenticated runtime smoke (${roleConfig.role})`, () => {
    test.describe.configure({ mode: 'serial' });

    test.beforeAll(() => {
      assertCredentialsForRole(roleConfig.role);
    });

    test.skip(
      !requireAuthRuntime && !hasCredentialsForRole(roleConfig.role),
      `Set E2E credentials for ${roleConfig.role} role to enable authenticated smoke.`,
    );

    test(`@smoke ${roleConfig.role} app shell is reachable and stable`, async ({ page }) => {
      const runtimeErrors = captureRuntimeErrors(page);

      await login(page, roleConfig.role, roleConfig.expectedPath);

      await expect(page).toHaveURL(roleConfig.expectedPath);
      await expect(page.getByRole('button', { name: 'Keluar' })).toBeVisible();
      await expect(page.getByText('Terjadi gangguan antarmuka karena error JavaScript.')).toBeHidden();
      await roleConfig.shellAssertion(page);

      await page.getByRole('button', { name: 'Keluar' }).click();
      await expect(page).toHaveURL(/\/login$/);
      expect(runtimeErrors).toEqual([]);
    });

    test(`@a11y ${roleConfig.role} app shell has no serious or critical axe violations`, async ({ page }) => {
      test.skip(!requireAuthA11yRuntime, 'Set E2E_REQUIRE_AUTH_A11Y=1 to enforce authenticated a11y runtime checks.');

      await login(page, roleConfig.role, roleConfig.expectedPath);
      await expect(page).toHaveURL(roleConfig.expectedPath);
      await roleConfig.shellAssertion(page);

      const accessibilityScan = await buildA11yScan(page);

      const seriousViolations = accessibilityScan.violations.filter((violation) =>
        ['serious', 'critical'].includes(String(violation.impact ?? ''))
      );

      expect(seriousViolations).toEqual([]);
    });
  });
}
