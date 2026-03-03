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

const capturePerformanceSnapshot = async (page) => {
  await page.waitForLoadState('networkidle');

  return page.evaluate(() => {
    const navEntry = performance.getEntriesByType('navigation')[0];
    const fcpEntry = performance.getEntriesByName('first-contentful-paint')[0];

    return {
      url: window.location.pathname + window.location.search,
      navigation: navEntry
        ? {
            domContentLoadedMs: Math.round(navEntry.domContentLoadedEventEnd),
            loadEventMs: Math.round(navEntry.loadEventEnd),
            responseStartMs: Math.round(navEntry.responseStart),
          }
        : null,
      firstContentfulPaintMs: fcpEntry ? Math.round(fcpEntry.startTime) : null,
    };
  });
};

const assertPerformanceBudget = (snapshot) => {
  expect(snapshot.navigation).not.toBeNull();

  expect(snapshot.navigation.responseStartMs).toBeLessThan(4_000);
  expect(snapshot.navigation.domContentLoadedMs).toBeLessThan(12_000);
  expect(snapshot.navigation.loadEventMs).toBeLessThan(20_000);

  if (snapshot.firstContentfulPaintMs !== null) {
    expect(snapshot.firstContentfulPaintMs).toBeLessThan(8_000);
  }
};

test.describe('runtime performance baseline', () => {
  test.describe.configure({ mode: 'serial' });

  test('@perf login page baseline budget', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'Performance baseline is enforced on desktop project only.');

    await page.goto('/login');
    const snapshot = await capturePerformanceSnapshot(page);
    assertPerformanceBudget(snapshot);

    await testInfo.attach('perf-login.json', {
      body: Buffer.from(JSON.stringify(snapshot, null, 2), 'utf8'),
      contentType: 'application/json',
    });
  });

  test('@perf dashboard page baseline budget (desa)', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'Performance baseline is enforced on desktop project only.');
    test.skip(!hasCredentialsForRole('desa'), 'Set desa credentials to execute dashboard performance baseline.');

    await login(page, 'desa', /\/dashboard(\?.*)?$/);
    const snapshot = await capturePerformanceSnapshot(page);
    assertPerformanceBudget(snapshot);

    await testInfo.attach('perf-dashboard-desa.json', {
      body: Buffer.from(JSON.stringify(snapshot, null, 2), 'utf8'),
      contentType: 'application/json',
    });
  });

  test('@perf super-admin users page baseline budget', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name.includes('mobile'), 'Performance baseline is enforced on desktop project only.');
    test.skip(!hasCredentialsForRole('super-admin'), 'Set super-admin credentials to execute users performance baseline.');

    await login(page, 'super-admin', /\/super-admin\/users(\?.*)?$/);
    const snapshot = await capturePerformanceSnapshot(page);
    assertPerformanceBudget(snapshot);

    await testInfo.attach('perf-superadmin-users.json', {
      body: Buffer.from(JSON.stringify(snapshot, null, 2), 'utf8'),
      contentType: 'application/json',
    });
  });
});
