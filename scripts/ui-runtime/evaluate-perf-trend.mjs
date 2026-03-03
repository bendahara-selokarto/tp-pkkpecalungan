import fs from 'node:fs';
import path from 'node:path';

const historyPath = path.join(process.cwd(), 'reports', 'ui-runtime', 'perf', 'history', 'perf-history.jsonl');
const outputDir = path.join(process.cwd(), 'reports', 'ui-runtime', 'perf');
const trendJsonPath = path.join(outputDir, 'trend-evaluation.json');
const trendMdPath = path.join(outputDir, 'trend-evaluation.md');
const WINDOW_SIZE = 3;
const MIN_DEGRADATION_PERCENT = 15;

const METRICS = [
  'responseStartMs',
  'domContentLoadedMs',
  'loadEventMs',
  'firstContentfulPaintMs',
];

const readHistory = () => {
  if (!fs.existsSync(historyPath)) {
    return [];
  }

  const lines = fs.readFileSync(historyPath, 'utf8')
    .split('\n')
    .map((line) => line.trim())
    .filter((line) => line !== '');

  return lines.map((line) => JSON.parse(line));
};

const getMetricValue = (summary, metric) => {
  const value = summary?.aggregate?.maxByMetric?.[metric];
  return typeof value === 'number' ? value : null;
};

const evaluateMetricTrend = (recent, metric) => {
  const values = recent.map((summary) => getMetricValue(summary, metric));
  if (values.some((value) => value === null)) {
    return {
      metric,
      values,
      status: 'insufficient-data',
      degraded: false,
    };
  }

  const isStrictlyWorsening = values[0] < values[1] && values[1] < values[2];
  const start = values[0];
  const end = values[2];
  const degradationPercent = start === 0 ? 0 : Math.round(((end - start) / start) * 100);
  const degraded = isStrictlyWorsening && degradationPercent >= MIN_DEGRADATION_PERCENT;

  return {
    metric,
    values,
    isStrictlyWorsening,
    degradationPercent,
    thresholdPercent: MIN_DEGRADATION_PERCENT,
    degraded,
    status: degraded ? 'flagged' : 'ok',
  };
};

const renderMarkdown = (result) => {
  const lines = [
    '# UI Runtime Performance Trend Evaluation',
    '',
    `Run at: ${result.runAt}`,
    `Window size: ${result.windowSize}`,
    `Status: ${result.status}`,
    `Warmup: ${result.isWarmup ? 'yes' : 'no'}`,
    `Warmup remaining runs: ${result.warmupRemainingRuns}`,
    '',
    '## Metric Checks',
  ];

  for (const check of result.checks) {
    const valuesText = Array.isArray(check.values) ? check.values.join(' -> ') : 'n/a';
    if (check.status === 'insufficient-data') {
      lines.push(`- ${check.metric}: insufficient data (${valuesText})`);
      continue;
    }

    lines.push(
      `- ${check.metric}: ${check.status} (values: ${valuesText}, degradation: ${check.degradationPercent}%, threshold: ${check.thresholdPercent}%)`,
    );
  }

  if (result.flaggedMetrics.length === 0) {
    lines.push('', '## Result', '- no sustained degradation detected');
  } else {
    lines.push('', '## Result');
    for (const metric of result.flaggedMetrics) {
      lines.push(`- sustained degradation flagged on ${metric}`);
    }
  }

  lines.push('', '## Next Action');
  if (result.isWarmup) {
    lines.push(`- collect at least ${result.warmupRemainingRuns} additional run(s) to activate full trend evaluation`);
  } else if (result.flaggedMetrics.length > 0) {
    lines.push('- investigate flagged metrics before promoting changes to baseline');
  } else {
    lines.push('- trend is stable; continue routine monitoring');
  }

  return `${lines.join('\n')}\n`;
};

const main = () => {
  fs.mkdirSync(outputDir, { recursive: true });
  const history = readHistory();
  const recent = history.slice(-WINDOW_SIZE);
  const hasWindow = recent.length === WINDOW_SIZE;

  const checks = hasWindow
    ? METRICS.map((metric) => evaluateMetricTrend(recent, metric))
    : METRICS.map((metric) => ({
      metric,
      values: [],
      status: 'insufficient-data',
      degraded: false,
    }));

  const flaggedMetrics = checks
    .filter((check) => check.degraded === true)
    .map((check) => check.metric);

  const result = {
    runAt: new Date().toISOString(),
    historySize: history.length,
    windowSize: WINDOW_SIZE,
    isWarmup: !hasWindow,
    warmupRemainingRuns: hasWindow ? 0 : Math.max(WINDOW_SIZE - history.length, 0),
    recentRunAts: recent.map((summary) => summary?.runAt ?? ''),
    checks,
    flaggedMetrics,
    status: !hasWindow
      ? 'warmup'
      : flaggedMetrics.length > 0
        ? 'degradation-flagged'
        : 'ok',
  };

  fs.writeFileSync(trendJsonPath, JSON.stringify(result, null, 2), 'utf8');
  fs.writeFileSync(trendMdPath, renderMarkdown(result), 'utf8');

  console.log(`[perf-trend] history=${history.length} status=${result.status} flagged=${flaggedMetrics.length}`);

  const stepSummaryPath = process.env.GITHUB_STEP_SUMMARY;
  if (typeof stepSummaryPath === 'string' && stepSummaryPath.trim() !== '') {
    const summaryLines = [
      '### UI Runtime Performance Trend',
      `- status: \`${result.status}\``,
      `- history: \`${result.historySize}\` run`,
      `- window: \`${result.windowSize}\` run`,
      `- warmup remaining: \`${result.warmupRemainingRuns}\` run`,
      `- flagged metrics: \`${flaggedMetrics.length}\``,
    ];

    if (result.isWarmup) {
      summaryLines.push('- note: warmup mode aktif karena history belum mencapai window evaluasi.');
    }

    fs.appendFileSync(stepSummaryPath, `${summaryLines.join('\n')}\n`, 'utf8');
  }

  // Optional strict mode for future promotion to blocking gate.
  if (process.env.PERF_TREND_ENFORCE === '1' && flaggedMetrics.length > 0) {
    process.exitCode = 1;
  }
};

main();
