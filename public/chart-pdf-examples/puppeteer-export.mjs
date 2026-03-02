import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

let puppeteer;
try {
  puppeteer = await import('puppeteer');
} catch (error) {
  console.error('Dependency missing: install puppeteer first with `npm i -D puppeteer`.');
  process.exit(1);
}

const sourceArg = process.argv[2];
const sourcePath = sourceArg
  ? path.resolve(process.cwd(), sourceArg)
  : path.resolve(process.cwd(), 'public/chart-pdf-examples/apexcharts-jspdf.html');

if (!fs.existsSync(sourcePath)) {
  console.error(`Source file not found: ${sourcePath}`);
  process.exit(1);
}

const outputDir = path.resolve(process.cwd(), 'public/chart-pdf-examples/output');
const outputPath = path.resolve(outputDir, 'puppeteer-dashboard.pdf');

fs.mkdirSync(outputDir, { recursive: true });

const browser = await puppeteer.default.launch({ headless: true });
try {
  const page = await browser.newPage();
  await page.goto(pathToFileURL(sourcePath).href, { waitUntil: 'networkidle0' });
  await page.pdf({
    path: outputPath,
    format: 'A4',
    landscape: true,
    printBackground: true,
    margin: { top: '12mm', right: '12mm', bottom: '12mm', left: '12mm' }
  });
  console.log(`PDF generated: ${outputPath}`);
} finally {
  await browser.close();
}
