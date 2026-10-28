const fs = require('fs');
const path = require('path');

function escapeHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

function parseMarkdown(md) {
  const lines = md.split('\n');
  let html = '';
  let inTable = false;
  let tableHeaderParsed = false;
  let inCodeBlock = false;
  let codeBlockContent = '';
  let inList = false;
  let listType = '';
  let toc = [];

  for (let i = 0; i < lines.length; i++) {
    let line = lines[i];

    if (line.trim().startsWith('```')) {
      if (inCodeBlock) {
        html += `<pre><code>${escapeHtml(codeBlockContent.trimEnd())}</code></pre>\n`;
        inCodeBlock = false;
        codeBlockContent = '';
      } else {
        if (inTable) { html += '</tbody></table></div>\n'; inTable = false; }
        if (inList) { html += `</${listType}>\n`; inList = false; }
        inCodeBlock = true;
        codeBlockContent = '';
      }
      continue;
    }

    if (inCodeBlock) {
      codeBlockContent += line + '\n';
      continue;
    }

    if (line.trim().startsWith('|') && line.trim().endsWith('|')) {
      const rowContent = line.trim().slice(1, -1);
      const cells = rowContent.split('|').map(c => c.trim());
      
      if (cells.every(c => /^:?-+:?$/.test(c))) {
        continue;
      }

      if (!inTable) {
        if (inList) { html += `</${listType}>\n`; inList = false; }
        html += '<div class="table-responsive"><table>\n';
        inTable = true;
        tableHeaderParsed = false;
      }

      if (!tableHeaderParsed) {
        html += '<thead><tr>\n';
        cells.forEach(c => {
          html += `  <th>${formatInline(c)}</th>\n`;
        });
        html += '</tr></thead><tbody>\n';
        tableHeaderParsed = true;
      } else {
        html += '<tr>\n';
        cells.forEach(c => {
          html += `  <td>${formatInline(c)}</td>\n`;
        });
        html += '</tr>\n';
      }
      continue;
    } else {
      if (inTable) {
        html += '</tbody></table></div>\n';
        inTable = false;
      }
    }

    const trimmed = line.trim();

    if (!trimmed) {
      if (inList) { html += `</${listType}>\n`; inList = false; }
      continue;
    }

    if (/^(\*{3,}|-{3,}|_{3,})$/.test(trimmed)) {
      if (inList) { html += `</${listType}>\n`; inList = false; }
      html += '<hr class="section-divider" />\n';
      continue;
    }

    if (trimmed.startsWith('>')) {
      if (inList) { html += `</${listType}>\n`; inList = false; }
      let bqContent = trimmed.replace(/^>\s?/, '');
      let calloutClass = 'callout-note';
      if (bqContent.includes('[!IMPORTANT]')) {
        calloutClass = 'callout-important';
        bqContent = bqContent.replace('[!IMPORTANT]', '<strong>PENTING:</strong>');
      } else if (bqContent.includes('[!WARNING]')) {
        calloutClass = 'callout-warning';
        bqContent = bqContent.replace('[!WARNING]', '<strong>PERINGATAN:</strong>');
      } else if (bqContent.includes('[!TIP]')) {
        calloutClass = 'callout-tip';
        bqContent = bqContent.replace('[!TIP]', '<strong>TIPS:</strong>');
      }
      html += `<div class="callout ${calloutClass}">${formatInline(bqContent)}</div>\n`;
      continue;
    }

    if (trimmed.startsWith('#')) {
      if (inList) { html += `</${listType}>\n`; inList = false; }
      const match = trimmed.match(/^(#{1,6})\s+(.*)$/);
      if (match) {
        const level = match[1].length;
        const text = match[2];
        const slug = 'sec-' + text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        
        if (level <= 3) {
          toc.push({ level, text: text.replace(/\*/g, ''), slug });
        }

        let headingClass = `heading-${level}`;
        if (text.includes('SOP-') || text.includes('FRM-SNA-')) {
          headingClass += ' sop-card-header';
        }
        html += `<h${level} id="${slug}" class="${headingClass}">${formatInline(text)}</h${level}>\n`;
        continue;
      }
    }

    if (/^[-*+]\s+/.test(trimmed)) {
      if (!inList || listType !== 'ul') {
        if (inList) html += `</${listType}>\n`;
        html += '<ul>\n';
        inList = true;
        listType = 'ul';
      }
      const itemText = trimmed.replace(/^[-*+]\s+/, '');
      html += `  <li>${formatInline(itemText)}</li>\n`;
      continue;
    }

    if (/^\d+\.\s+/.test(trimmed)) {
      if (!inList || listType !== 'ol') {
        if (inList) html += `</${listType}>\n`;
        html += '<ol>\n';
        inList = true;
        listType = 'ol';
      }
      const itemText = trimmed.replace(/^\d+\.\s+/, '');
      html += `  <li>${formatInline(itemText)}</li>\n`;
      continue;
    }

    if (inList) { html += `</${listType}>\n`; inList = false; }
    html += `<p>${formatInline(trimmed)}</p>\n`;
  }

  if (inTable) html += '</tbody></table></div>\n';
  if (inList) html += `</${listType}>\n`;
  if (inCodeBlock) html += `<pre><code>${escapeHtml(codeBlockContent.trimEnd())}</code></pre>\n`;

  return { html, toc };
}

function formatInline(text) {
  let res = escapeHtml(text);
  res = res.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
  res = res.replace(/\*(.*?)\*/g, '<em>$1</em>');
  res = res.replace(/`([^`]+)`/g, '<code class="inline-code">$1</code>');
  res = res.replace(/\$\$(.*?)\$\$/g, '<div class="math-block">$1</div>');
  res = res.replace(/\$(.*?)\$/g, '<span class="math-inline">$1</span>');
  return res;
}

function buildHtmlFile(inputMdPath, outputHtmlPath, bookTitle, badgeText) {
  const mdContent = fs.readFileSync(inputMdPath, 'utf8');
  const { html, toc } = parseMarkdown(mdContent);

  let tocHtml = '<nav class="sidebar-toc">\n<div class="toc-header"><h3>DAFTAR ISI</h3></div>\n<ul class="toc-list">\n';
  toc.forEach(item => {
    const indentClass = `toc-level-${item.level}`;
    tocHtml += `  <li class="${indentClass}"><a href="#${item.slug}">${escapeHtml(item.text)}</a></li>\n`;
  });
  tocHtml += '</ul>\n</nav>\n';

  const fullHtml = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>${escapeHtml(bookTitle)}</title>
  <style>
    :root {
      --primary: #0A2647;
      --primary-light: #144272;
      --secondary: #2C74B3;
      --accent: #D89216;
      --dark: #1E293B;
      --gray-bg: #F8FAFC;
      --gray-border: #E2E8F0;
      --text: #334155;
      --success: #166534;
      --success-bg: #DCFCE7;
      --danger: #991B1B;
      --danger-bg: #FEE2E2;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color: var(--text);
      background-color: #EDF2F7;
      line-height: 1.65;
      font-size: 15px;
    }
    .top-bar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: var(--primary);
      color: #FFFFFF;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }
    .brand-title {
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .brand-badge {
      background: var(--accent);
      color: #000;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 4px;
      text-transform: uppercase;
    }
    .btn-group { display: flex; gap: 10px; }
    .btn {
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 0.88rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      border: none;
    }
    .btn-download { background: var(--accent); color: #111827; }
    .btn-download:hover { background: #F59E0B; transform: translateY(-1px); }
    .btn-secondary { background: rgba(255, 255, 255, 0.15); color: #FFFFFF; }
    .btn-secondary:hover { background: rgba(255, 255, 255, 0.25); }
    .app-container {
      display: flex;
      max-width: 1440px;
      margin: 0 auto;
      min-height: calc(100vh - 60px);
    }
    .sidebar {
      width: 330px;
      background: #FFFFFF;
      border-right: 1px solid var(--gray-border);
      position: sticky;
      top: 57px;
      height: calc(100vh - 57px);
      overflow-y: auto;
      padding: 20px 16px;
      flex-shrink: 0;
    }
    .sidebar-toc h3 {
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--accent);
    }
    .toc-list { list-style: none; }
    .toc-list li { margin-bottom: 6px; }
    .toc-list a {
      color: #475569;
      text-decoration: none;
      font-size: 0.85rem;
      display: block;
      padding: 4px 8px;
      border-radius: 4px;
      transition: all 0.15s;
    }
    .toc-list a:hover { background: #EFF6FF; color: var(--secondary); }
    .toc-level-1 { font-weight: 700; margin-top: 14px; }
    .toc-level-1 a { color: var(--primary); }
    .toc-level-2 { font-weight: 600; padding-left: 10px; }
    .toc-level-3 { padding-left: 20px; font-size: 0.82rem; color: #64748B; }
    .main-content {
      flex: 1;
      padding: 40px;
      background: #FFFFFF;
      margin: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      max-width: 1020px;
    }
    .cover-card {
      background: linear-gradient(135deg, #0A2647 0%, #144272 60%, #205295 100%);
      color: #FFFFFF;
      padding: 50px 40px;
      border-radius: 12px;
      margin-bottom: 40px;
      box-shadow: 0 10px 30px rgba(10, 38, 71, 0.3);
      border-top: 6px solid var(--accent);
      text-align: center;
    }
    .cover-card h1 {
      font-size: 2.2rem;
      font-weight: 800;
      letter-spacing: 1px;
      margin-bottom: 8px;
      color: #FFFFFF;
    }
    .cover-card .operator-tag {
      display: inline-block;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.95rem;
      font-weight: 600;
      margin-bottom: 24px;
      color: #E2E8F0;
    }
    .cover-card h2 {
      font-size: 1.5rem;
      color: #F8FAFC;
      margin-bottom: 12px;
      font-weight: 700;
      border: none;
      padding: 0;
    }
    .cover-card .sub-title {
      font-size: 1.05rem;
      color: #CBD5E1;
      max-width: 700px;
      margin: 0 auto 30px auto;
    }
    h1, h2, h3, h4, h5 {
      color: var(--primary);
      font-weight: 700;
      margin-top: 32px;
      margin-bottom: 14px;
      scroll-margin-top: 80px;
    }
    h1 { font-size: 1.8rem; border-bottom: 3px solid var(--primary); padding-bottom: 8px; }
    h2 { font-size: 1.4rem; border-bottom: 2px solid var(--gray-border); padding-bottom: 6px; margin-top: 40px; }
    h3 { font-size: 1.2rem; margin-top: 28px; }
    h4 { font-size: 1.05rem; color: var(--secondary); }
    p { margin-bottom: 14px; color: #334155; }
    ul, ol { margin-bottom: 16px; padding-left: 24px; }
    li { margin-bottom: 6px; }
    hr.section-divider { border: 0; height: 1px; background: #E2E8F0; margin: 36px 0; }
    .sop-card-header {
      background: #F1F5F9;
      border-left: 5px solid var(--secondary);
      padding: 12px 16px;
      border-radius: 0 8px 8px 0;
      margin-top: 36px;
    }
    .table-responsive {
      overflow-x: auto;
      margin: 20px 0;
      border-radius: 8px;
      border: 1px solid var(--gray-border);
    }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; background: #FFFFFF; }
    th {
      background: #F8FAFC;
      color: var(--primary);
      font-weight: 700;
      text-align: left;
      padding: 12px 14px;
      border-bottom: 2px solid var(--gray-border);
    }
    td { padding: 10px 14px; border-bottom: 1px solid var(--gray-border); color: #334155; vertical-align: top; }
    tr:nth-child(even) td { background: #FAFAFA; }
    tr:hover td { background: #F1F5F9; }
    .callout { padding: 16px 20px; border-radius: 8px; margin: 20px 0; font-size: 0.95rem; border-left: 4px solid; }
    .callout-important { background: #FFFBEB; border-color: #D97706; color: #92400E; }
    .callout-warning { background: var(--danger-bg); border-color: var(--danger); color: var(--danger); }
    .callout-tip { background: var(--success-bg); border-color: var(--success); color: var(--success); }
    .callout-note { background: #EFF6FF; border-color: #3B82F6; color: #1E40AF; }
    pre {
      background: #0F172A;
      color: #E2E8F0;
      padding: 16px;
      border-radius: 8px;
      overflow-x: auto;
      font-family: Consolas, Monaco, monospace;
      font-size: 0.82rem;
      margin: 18px 0;
      line-height: 1.45;
    }
    code.inline-code {
      background: #F1F5F9;
      color: #BE185D;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: Consolas, monospace;
      font-size: 0.9em;
    }
    .math-inline { font-family: "Cambria Math", serif; font-weight: bold; color: #0369A1; padding: 0 4px; }
    .search-box { margin-bottom: 16px; }
    .search-input {
      width: 100%;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--gray-border);
      font-size: 0.85rem;
      outline: none;
    }
    @media print {
      @page { size: A4; margin: 15mm; }
      body { background: #FFFFFF !important; font-size: 10pt; color: #000000; }
      .top-bar, .sidebar, .btn-group, .search-box { display: none !important; }
      .app-container { display: block !important; margin: 0 !important; max-width: 100% !important; }
      .main-content { margin: 0 !important; padding: 0 !important; box-shadow: none !important; max-width: 100% !important; }
      .cover-card {
        background: #0A2647 !important;
        color: #FFFFFF !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        page-break-after: always;
        padding: 60px 40px;
      }
      h1, h2 { page-break-after: avoid; }
      h1 { page-break-before: always; }
      .sop-card-header {
        page-break-before: always;
        background: #F1F5F9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      table, pre { page-break-inside: avoid; }
      pre { background: #F8FAFC !important; color: #000000 !important; border: 1px solid #CBD5E1; }
    }
  </style>
</head>
<body>
  <header class="top-bar">
    <div class="brand-title">
      <span>SNAPRINT DIGITAL PRINTING</span>
      <span class="brand-badge">${escapeHtml(badgeText)}</span>
    </div>
    <div class="btn-group">
      <button class="btn btn-secondary" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">⬆ Ke Atas</button>
      <button class="btn btn-download" onclick="window.print()">📥 Unduh / Cetak PDF</button>
    </div>
  </header>
  <div class="app-container">
    <aside class="sidebar">
      <div class="search-box">
        <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari SOP / Form..." onkeyup="filterTOC()">
      </div>
      ${tocHtml}
    </aside>
    <main class="main-content">
      <div class="cover-card">
        <h1>SNAPRINT DIGITAL PRINTING</h1>
        <div class="operator-tag">operated by dutaglobaltech.com</div>
        <h2>${escapeHtml(bookTitle)}</h2>
        <p class="sub-title">Dokumen Standar Sistem Waralaba &amp; Tata Kelola Mutu Percetakan Digital</p>
      </div>
      <div id="sopContent">${html}</div>
    </main>
  </div>
  <script>
    function filterTOC() {
      const input = document.getElementById('sopSearch').value.toLowerCase();
      const listItems = document.querySelectorAll('.toc-list li');
      listItems.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(input)) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>`;

  fs.writeFileSync(outputHtmlPath, fullHtml, 'utf8');
  console.log(`Generated HTML: ${outputHtmlPath}`);
}

// 1. Build Master HTML
buildHtmlFile(
  path.join(__dirname, 'BUNDEL_SOP_SNAPRINT_DIGITAL_PRINTING.md'),
  path.join(__dirname, 'BUNDEL_SOP_SNAPRINT_DIGITAL_BOOK.html'),
  'BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET',
  'Master Franchise Manual'
);

// 2. Build Dedicated Forms HTML
buildHtmlFile(
  path.join(__dirname, 'LAMPIRAN_DOKUMEN_KONTROL_SOP_SNAPRINT.md'),
  path.join(__dirname, 'LAMPIRAN_DOKUMEN_KONTROL_SNAPRINT.html'),
  'BUNDEL MASTER LAMPIRAN & DOKUMEN KONTROL OPERASIONAL',
  'Operational Control Forms'
);
