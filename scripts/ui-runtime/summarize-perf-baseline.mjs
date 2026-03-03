import fs from 'node:fs';
import path from 'node:path';

const latestDir = path.join(process.cwd(), 'reports', 'ui-runtime', 'perf', 'latest');
const outputDir = path.join(process.cwd(), 'reports', 'ui-runtime', 'perf');
const historyDir = path.join(outputDir, 'history');
const summaryJsonPath = path.join(outputDir, 'summary.json');
const summaryMdPath = path.join(outputDir, 'summary.md');
const historyPath = path.join(historyDir, 'perf-history.jsonl');

const BUDGET = {
  responseStartMs: 4000,
  domContentLoadedMs: 12000,
  loadEventMs: 20000,
  firstContentfulPaintMs: 8000,
};

const readLatestEvidence = () => {
  if (!fs.existsSync(latestDir)) {
    return [];
  }

  return fs.readdirSync(latestDir)
    .filter((fileName) => fileName.endsWith('.json'))
    .map((fileName) => {
      const filePath = path.join(latestDir, fileName);
      const content = fs.readFileSync(filePath, 'utf8');
      return JSON.parse(content);
    });
};

const summarize = (entries) => {
  const runAt = new Date().toISOString();

  const normalized = entries.map((entry) => {
    const metrics = entry?.snapshot?.navigation ?? null;
    const fcp = entry?.snapshot?.firstContentfulPaintMs ?? null;

    return {
      page: String(entry?.snapshot?.url ?? ''),
      project: String(entry?.project ?? ''),
      recordedAt: String(entry?.recordedAt ?? ''),
      responseStartMs: metrics?.responseStartMs ?? null,
      domContentLoadedMs: metrics?.domContentLoadedMs ?? null,
      loadEventMs: metrics?.loadEventMs ?? null,
      firstContentfulPaintMs: fcp,
    };
  });

  const hasEntries = normalized.length > 0;
  const maxByMetric = {
    responseStartMs: hasEntries ? Math.max(...normalized.map((item) => item.responseStartMs ?? 0)) : null,
    domContentLoadedMs: hasEntries ? Math.max(...normalized.map((item) => item.domContentLoadedMs ?? 0)) : null,
    loadEventMs: hasEntries ? Math.max(...normalized.map((item) => item.loadEventMs ?? 0)) : null,
    firstContentfulPaintMs: hasEntries ? Math.max(...normalized.map((item) => item.firstContentfulPaintMs ?? 0)) : null,
  };

  const breaches = [];
  for (const item of normalized) {
    for (const [metric, maxAllowed] of Object.entries(BUDGET)) {
      const value = item[metric];
      if (value !== null && typeof value === 'number' && value > maxAllowed) {
        breaches.push({
          page: item.page,
          project: item.project,
          metric,
          value,
          maxAllowed,
        });
      }
    }
  }

  return {
    runAt,
    budget: BUDGET,
    entries: normalized,
    aggregate: {
      count: normalized.length,
      maxByMetric,
      breachCount: breaches.length,
      status: breaches.length === 0 ? 'within-budget' : 'budget-breached',
    },
    breaches,
  };
};

const renderMarkdown = (summary) => {
  const lines = [
    '# UI Runtime Performance Summary',
    '',
    `Run at: ${summary.runAt}`,
    `Status: ${summary.aggregate.status}`,
    `Entries: ${summary.aggregate.count}`,
    '',
    '## Budget',
    `- responseStartMs <= ${summary.budget.responseStartMs}`,
    `- domContentLoadedMs <= ${summary.budget.domContentLoadedMs}`,
    `- loadEventMs <= ${summary.budget.loadEventMs}`,
    `- firstContentfulPaintMs <= ${summary.budget.firstContentfulPaintMs}`,
    '',
    '## Max Metrics',
    `- responseStartMs: ${summary.aggregate.maxByMetric.responseStartMs ?? 'n/a'}`,
    `- domContentLoadedMs: ${summary.aggregate.maxByMetric.domContentLoadedMs ?? 'n/a'}`,
    `- loadEventMs: ${summary.aggregate.maxByMetric.loadEventMs ?? 'n/a'}`,
    `- firstContentfulPaintMs: ${summary.aggregate.maxByMetric.firstContentfulPaintMs ?? 'n/a'}`,
    '',
  ];

  if (summary.breaches.length === 0) {
    lines.push('## Breaches', '- none');
  } else {
    lines.push('## Breaches');
    for (const breach of summary.breaches) {
      lines.push(`- ${breach.project} ${breach.page}: ${breach.metric}=${breach.value} (budget ${breach.maxAllowed})`);
    }
  }

  lines.push('', '## Entries');
  for (const entry of summary.entries) {
    lines.push(`- ${entry.project} ${entry.page}`);
    lines.push(`  responseStart=${entry.responseStartMs}, domContentLoaded=${entry.domContentLoadedMs}, loadEvent=${entry.loadEventMs}, fcp=${entry.firstContentfulPaintMs ?? 'n/a'}`);
  }

  return `${lines.join('\n')}\n`;
};

const ensureDirs = () => {
  fs.mkdirSync(outputDir, { recursive: true });
  fs.mkdirSync(historyDir, { recursive: true });
};

const main = () => {
  ensureDirs();
  const entries = readLatestEvidence();
  const summary = summarize(entries);

  fs.writeFileSync(summaryJsonPath, JSON.stringify(summary, null, 2), 'utf8');
  fs.writeFileSync(summaryMdPath, renderMarkdown(summary), 'utf8');
  fs.appendFileSync(historyPath, `${JSON.stringify(summary)}\n`, 'utf8');

  console.log(`[perf-summary] entries=${summary.aggregate.count} status=${summary.aggregate.status} output=${summaryJsonPath}`);
};

main();
