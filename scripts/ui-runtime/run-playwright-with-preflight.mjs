#!/usr/bin/env node

import { existsSync, readdirSync } from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { spawnSync } from 'node:child_process';

const DEFAULT_PLAYWRIGHT_ARGS = ['test', '-c', 'playwright.config.mjs'];
const DOCTOR_FLAG = '--doctor';

function parseBrowserRevision(name) {
  const [, revision] = name.split('-');
  return Number.parseInt(revision ?? '0', 10) || 0;
}

function resolvePlaywrightCacheRoot(env) {
  if (env.PLAYWRIGHT_BROWSERS_PATH && env.PLAYWRIGHT_BROWSERS_PATH !== '0') {
    return env.PLAYWRIGHT_BROWSERS_PATH;
  }

  return path.join(os.homedir(), '.cache', 'ms-playwright');
}

function findInstalledChromiumExecutable(cacheRoot) {
  if (!existsSync(cacheRoot)) {
    return null;
  }

  const browserDirs = readdirSync(cacheRoot, { withFileTypes: true })
    .filter((entry) => entry.isDirectory())
    .map((entry) => entry.name);

  const chromiumHeadlessShell = browserDirs
    .filter((name) => name.startsWith('chromium_headless_shell-'))
    .sort((left, right) => parseBrowserRevision(right) - parseBrowserRevision(left));

  for (const dir of chromiumHeadlessShell) {
    const executable = path.join(
      cacheRoot,
      dir,
      'chrome-headless-shell-linux64',
      'chrome-headless-shell',
    );
    if (existsSync(executable)) {
      return executable;
    }
  }

  const chromiumBrowser = browserDirs
    .filter((name) => name.startsWith('chromium-'))
    .sort((left, right) => parseBrowserRevision(right) - parseBrowserRevision(left));

  for (const dir of chromiumBrowser) {
    const executable = path.join(cacheRoot, dir, 'chrome-linux', 'chrome');
    if (existsSync(executable)) {
      return executable;
    }
  }

  return null;
}

function collectMissingDynamicLibraries(executablePath, env) {
  const ldd = spawnSync('ldd', [executablePath], {
    encoding: 'utf8',
    env,
  });

  if (ldd.error) {
    return [];
  }

  const output = `${ldd.stdout ?? ''}\n${ldd.stderr ?? ''}`;
  const lines = output.split('\n').map((line) => line.trim());

  return lines
    .filter((line) => line.includes('=> not found'))
    .map((line) => line.split('=>')[0]?.trim())
    .filter(Boolean);
}

function printLinuxDependencyHelp(missingLibraries) {
  const aptHints = new Map([
    ['libnspr4.so', 'libnspr4'],
    ['libnss3.so', 'libnss3'],
    ['libnssutil3.so', 'libnss3'],
    ['libasound.so.2', 'libasound2'],
  ]);

  const suggestedPackages = [...new Set(
    missingLibraries
      .map((name) => aptHints.get(name))
      .filter(Boolean),
  )];

  console.error('[e2e-preflight] Dependency OS browser belum lengkap untuk Playwright Chromium.');
  console.error(`[e2e-preflight] Missing shared libraries: ${missingLibraries.join(', ')}`);
  if (suggestedPackages.length > 0) {
    console.error(`[e2e-preflight] Install package (Ubuntu/Debian): sudo apt-get install -y ${suggestedPackages.join(' ')}`);
  }
  console.error('[e2e-preflight] Status: dependency-missing (preflight).');
  console.error('[e2e-preflight] Setelah install, ulangi: npm run test:e2e');
}

function runLinuxPreflight(env) {
  const cacheRoot = resolvePlaywrightCacheRoot(env);
  const chromiumExecutable = findInstalledChromiumExecutable(cacheRoot);

  if (!chromiumExecutable) {
    console.error('[e2e-preflight] Browser Playwright Chromium belum terdeteksi.');
    console.error('[e2e-preflight] Jalankan dulu: npm run test:e2e:install');
    return false;
  }

  const missingLibraries = collectMissingDynamicLibraries(chromiumExecutable, env);
  if (missingLibraries.length === 0) {
    return true;
  }

  printLinuxDependencyHelp(missingLibraries);
  return false;
}

function main() {
  const passthroughArgs = process.argv.slice(2);
  const doctorMode = passthroughArgs.includes(DOCTOR_FLAG);
  const forwardedArgs = passthroughArgs.filter((arg) => arg !== DOCTOR_FLAG);

  const env = { ...process.env };
  if (process.platform === 'linux') {
    env.TMPDIR = env.TMPDIR || '/tmp';
    env.TEMP = env.TEMP || '/tmp';
    env.TMP = env.TMP || '/tmp';

    const preflightOk = runLinuxPreflight(env);
    if (!preflightOk) {
      process.exit(1);
    }
  }

  if (doctorMode) {
    console.log('[e2e-preflight] OK: Playwright browser dependencies siap.');
    process.exit(0);
  }

  console.log('[e2e-preflight] Preflight OK: menjalankan Playwright.');
  const playwrightArgs = (() => {
    if (forwardedArgs.length === 0) {
      return DEFAULT_PLAYWRIGHT_ARGS;
    }

    if (forwardedArgs[0].startsWith('-')) {
      return [...DEFAULT_PLAYWRIGHT_ARGS, ...forwardedArgs];
    }

    return forwardedArgs;
  })();
  const run = spawnSync('npx', ['playwright', ...playwrightArgs], {
    stdio: 'inherit',
    env,
  });

  if (run.error) {
    console.error('[e2e-preflight] Gagal menjalankan Playwright CLI (tooling).');
    console.error(`[e2e-preflight] Detail: ${run.error.message}`);
    console.error('[e2e-preflight] Status: tooling-error (bukan test).');
    process.exit(1);
  }

  if (typeof run.status === 'number') {
    if (run.status !== 0) {
      console.error('[e2e-preflight] Status: test-failure (preflight ok).');
    }
    process.exit(run.status);
  }

  console.error('[e2e-preflight] Status: tooling-error (unknown).');
  process.exit(1);
}

main();
