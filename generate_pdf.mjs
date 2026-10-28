import path from 'path';
import { fileURLToPath } from 'url';
import { ReadableStream } from 'stream/web';

if (!globalThis.ReadableStream) {
  globalThis.ReadableStream = ReadableStream;
}

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function renderPdf(browser, htmlFileName, pdfFileName, headerTitle) {
  const page = await browser.newPage();
  const htmlPath = path.join(__dirname, htmlFileName);
  const pdfPath = path.join(__dirname, pdfFileName);
  
  console.log(`Navigating to: ${htmlFileName}`);
  await page.goto(`file://${htmlPath.replace(/\\/g, '/')}`, {
    waitUntil: 'networkidle0'
  });

  console.log(`Generating: ${pdfFileName}`);
  try {
    await page.pdf({
      path: pdfPath,
      format: 'A4',
      printBackground: true,
      margin: {
        top: '15mm',
        bottom: '15mm',
        left: '15mm',
        right: '15mm'
      },
      displayHeaderFooter: true,
      headerTemplate: `
        <div style="font-size: 8px; font-family: sans-serif; color: #888; width: 100%; display: flex; justify-content: space-between; padding: 0 15mm;">
          <span>SNAPRINT DIGITAL PRINTING - ${headerTitle}</span>
          <span>operated by dutaglobaltech.com</span>
        </div>
      `,
      footerTemplate: `
        <div style="font-size: 8px; font-family: sans-serif; color: #888; width: 100%; display: flex; justify-content: space-between; padding: 0 15mm;">
          <span>Sangat Rahasia (Strictly Confidential)</span>
          <span>Halaman <span class="pageNumber"></span> dari <span class="totalPages"></span></span>
        </div>
      `
    });
    console.log(`Successfully generated: ${pdfFileName}`);
  } catch (err) {
    console.warn(`Could not write to ${pdfFileName}:`, err.message);
  }
  await page.close();
}

async function run() {
  console.log('Loading Puppeteer...');
  const puppeteerModule = await import('file:///C:/Users/Swanto/AppData/Roaming/npm/node_modules/md-to-pdf/node_modules/puppeteer/lib/puppeteer/puppeteer.js');
  const puppeteer = puppeteerModule.default || puppeteerModule;

  console.log('Launching Chrome browser...');
  const browser = await puppeteer.launch({
    headless: true,
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
  });

  // 1. Render Dedicated Operational Forms PDF
  await renderPdf(
    browser,
    'LAMPIRAN_DOKUMEN_KONTROL_SNAPRINT.html',
    'LAMPIRAN_DOKUMEN_KONTROL_SNAPRINT.pdf',
    'Master Lampiran & Dokumen Kontrol Operasional Resmi'
  );

  // 2. Render Master Bundle PDF with new unlocked name
  await renderPdf(
    browser,
    'BUNDEL_SOP_SNAPRINT_DIGITAL_BOOK.html',
    'BUNDEL_SOP_SNAPRINT_MASTER_LENGKAP.pdf',
    'Master Franchise Operations Manual & SOP Bundel Lengkap'
  );

  // 3. Try to also update the original file name if possible
  await renderPdf(
    browser,
    'BUNDEL_SOP_SNAPRINT_DIGITAL_BOOK.html',
    'BUNDEL_SOP_SNAPRINT_DIGITAL_PRINTING.pdf',
    'Master Franchise Operations Manual & SOP Bundel'
  );

  await browser.close();
  console.log('PDF generation run finished!');
}

run().catch(err => {
  console.error('Fatal error in PDF generator:', err);
  process.exit(1);
});
