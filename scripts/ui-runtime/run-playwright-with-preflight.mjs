#!/usr/bin/env node

import { existsSync, readdirSync, readFileSync } from 'node:fs';
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

function loadLocalEnv(env) {
  const envPath = path.join(process.cwd(), '.env.e2e.local');
  if (!existsSync(envPath)) {
    return;
  }

  const lines = readFileSync(envPath, 'utf8').split(/\r?\n/);
  for (const rawLine of lines) {
    const line = rawLine.trim();
    if (line === '' || line.startsWith('#')) {
      continue;
    }
    const separatorIndex = line.indexOf('=');
    if (separatorIndex <= 0) {
      continue;
    }
    const key = line.slice(0, separatorIndex).trim();
    let value = line.slice(separatorIndex + 1).trim();
    if (value.startsWith('"') && value.endsWith('"')) {
      value = value.slice(1, -1);
    }
    if (env[key] === undefined) {
      env[key] = value;
    }
  }
}

function main() {
  const passthroughArgs = process.argv.slice(2);
  const doctorMode = passthroughArgs.includes(DOCTOR_FLAG);
  const forwardedArgs = passthroughArgs.filter((arg) => arg !== DOCTOR_FLAG);

  const env = { ...process.env };
  loadLocalEnv(env);
  if (process.platform === 'linux') {
    const runtimeTmp = env.E2E_TMPDIR || '/tmp';
    env.TMPDIR = runtimeTmp;
    env.TEMP = runtimeTmp;
    env.TMP = runtimeTmp;

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
  const isWindows = process.platform === 'win32';
  const localBin = path.join(
    process.cwd(),
    'node_modules',
    '.bin',
    isWindows ? 'playwright.cmd' : 'playwright',
  );
  const commandOptions = [
    { command: isWindows ? 'npx.cmd' : 'npx', args: ['playwright', ...playwrightArgs] },
    { command: isWindows ? 'npm.cmd' : 'npm', args: ['exec', '--', 'playwright', ...playwrightArgs] },
    ...(existsSync(localBin) ? [{ command: localBin, args: playwrightArgs }] : []),
  ];
  const spawnOption = (option) => spawnSync(option.command, option.args, {
    stdio: 'inherit',
    env,
    shell: isWindows && option.command.endsWith('.cmd'),
  });
  let run = spawnOption(commandOptions[0]);

  if (run.error && run.error.code === 'ENOENT') {
    for (let index = 1; index < commandOptions.length; index += 1) {
      const option = commandOptions[index];
      run = spawnOption(option);
      if (!run.error || run.error.code !== 'ENOENT') {
        break;
      }
    }
  }

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
