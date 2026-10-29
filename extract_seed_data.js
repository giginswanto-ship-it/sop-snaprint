const fs = require('fs');
const path = require('path');

function escapeHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
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

function parseMarkdown(md) {
  const lines = md.split('\n');
  let html = '';
  let inTable = false;
  let tableHeaderParsed = false;
  let inCodeBlock = false;
  let codeBlockContent = '';
  let inList = false;
  let listType = '';

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
      } else if (bqContent.includes('[!NOTE]')) {
        calloutClass = 'callout-note';
        bqContent = bqContent.replace('[!NOTE]', '<strong>CATATAN:</strong>');
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

  return html;
}

// Read markdown files
const masterMdPath = path.join(__dirname, 'BUNDEL_SOP_SNAPRINT_DIGITAL_PRINTING.md');
const formsMdPath = path.join(__dirname, 'LAMPIRAN_DOKUMEN_KONTROL_SOP_SNAPRINT.md');

const masterMd = fs.readFileSync(masterMdPath, 'utf8');
const formsMd = fs.readFileSync(formsMdPath, 'utf8');

// 1. Company Profile
const companyProfile = {
  brand_name: "SNAPRINT DIGITAL PRINTING",
  operator_name: "PT Duta Global Tech",
  operator_url: "dutaglobaltech.com",
  document_title: "BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET",
  document_subtitle: "Dokumen Komprehensif: Struktur SOP, Kualifikasi Kemitraan & Persyaratan Pembukaan Gerai Baru",
  document_version: "1.0 (Standar Kemitraan Nasional)",
  effective_date: "1 November 2026",
  status: "Master Franchise Operations Manual (Panduan Induk Operasional)",
  confidentiality_statement: "Seluruh materi, bagan alur kerja, standar teknis, formulir kontrol, metodologi kalibrasi, tata letak gerai, dan formulasi bisnis yang termaktub di dalam dokumen ini merupakan Hak Kekayaan Intelektual (HAKI) milik SNAPRINT DIGITAL PRINTING yang dikelola dan dioperasikan oleh dutaglobaltech.com. Dilarang keras menyalin, mereproduksi, memindai, menyebarluaskan, memperjualbelikan, atau mendistribusikan sebagian maupun seluruh isi panduan ini kepada pihak ketiga tanpa persetujuan tertulis resmi dari Direksi dutaglobaltech.com."
};

// 2. Parse Modules definition
const modulesData = [
  {
    code: 'MODUL 1',
    title: 'OPERASIONAL & PELAYANAN PELANGGAN',
    category: 'FRONT OFFICE',
    description: 'Standar pembukaan/penutupan gerai, pelayanan pelanggan, pra-cetak, kasir POS & penanganan komplain.',
    order: 1
  },
  {
    code: 'MODUL 2',
    title: 'PRODUKSI & PENGENDALIAN MUTU',
    category: 'BACK OFFICE / QC',
    description: 'Manajemen antrean produksi cetak, operasional mesin press digital & format lebar, finishing, dan kendali mutu akhir.',
    order: 2
  },
  {
    code: 'MODUL 3',
    title: 'PERAWATAN MESIN & MANAJEMEN ASET',
    category: 'MAINTENANCE',
    description: 'Perawatan harian printhead & mekanikal, pemeliharaan berkala kalibrasi ICC profile, serta troubleshooting kendala teknis.',
    order: 3
  },
  {
    code: 'MODUL 4',
    title: 'MANAJEMEN INVENTARIS & GUDANG',
    category: 'INVENTORY',
    description: 'Pengadaan bahan baku berbasis ROP & safety stock, penerimaan stok sistem FIFO, dan stock opname fisik berkala.',
    order: 4
  },
  {
    code: 'MODUL 5',
    title: 'PENGELOLAAN SUMBER DAYA MANUSIA',
    category: 'HRD & TRAINING',
    description: 'Standar rekrutmen staf outlet, kurikulum pelatihan 14 hari onboarding, kode etik, pakta integritas dan KPI bulanan.',
    order: 5
  },
  {
    code: 'MODUL 6',
    title: 'KEUANGAN, KASIR & PELAPORAN FRANCHISE',
    category: 'FINANCE',
    description: 'Rekonsiliasi kas harian penutupan shift, plafon kas kecil sistem impres, dan pelaporan keuangan royalty fee berkala.',
    order: 6
  },
  {
    code: 'MODUL 7',
    title: 'KESELAMATAN KERJA & TANGGAP DARURAT',
    category: 'K3 / HSE',
    description: 'Standar APD wajib gerai percetakan, tanggap darurat kebakaran & listrik metode PASS, serta protokol P3K darurat.',
    order: 7
  }
];

// Extract SOPs from masterMd
const sops = [];
const sopRegex = /### \[?(SOP-(\d)\.(\d))\]?:\s*(.*?)\n([\s\S]*?)(?=(### \[?SOP-\d\.\d\]?|## BAGIAN IV|## LEMBAR PENGESAHAN|$))/g;
let sopMatch;

while ((sopMatch = sopRegex.exec(masterMd)) !== null) {
  const sopCode = sopMatch[1].trim();
  const moduleNum = parseInt(sopMatch[2]);
  const sopSubNum = parseInt(sopMatch[3]);
  const title = sopMatch[4].trim().replace(/^\*\*/, '').replace(/\*\*$/, '');
  const rawBody = sopMatch[5].trim();

  // Extract metadata if available
  let purpose = '';
  let scope = '';
  let responsibilities = '';
  let indicators = '';
  let referenceForms = '';

  const purposeMatch = rawBody.match(/\*\*1\.\s*Tujuan\*\*:\s*([\s\S]*?)(?=\n\*\*2\.|\n\*\*)/);
  if (purposeMatch) purpose = purposeMatch[1].trim();

  const scopeMatch = rawBody.match(/\*\*2\.\s*Ruang Lingkup\*\*:\s*([\s\S]*?)(?=\n\*\*3\.|\n\*\*)/);
  if (scopeMatch) scope = scopeMatch[1].trim();

  const respMatch = rawBody.match(/\*\*3\.\s*Tanggung Jawab\*\*:\s*([\s\S]*?)(?=\n\*\*4\.|\n\*\*)/);
  if (respMatch) responsibilities = respMatch[1].trim();

  const indMatch = rawBody.match(/\*\*5\.\s*Indikator Kinerja\*\*[\s\S]*?:\s*([\s\S]*?)(?=\n\*\*6\.|\n\*\*)/);
  if (indMatch) indicators = indMatch[1].trim();

  const refMatch = rawBody.match(/\*\*6\.\s*Lampiran & Dokumen Kontrol\*\*:\s*([\s\S]*?)(?=$)/);
  if (refMatch) referenceForms = refMatch[1].trim();

  const fullMd = `### ${sopCode}: ${title}\n\n` + rawBody;
  const contentHtml = parseMarkdown(fullMd);

  const slug = `sop-${moduleNum}-${sopSubNum}`;

  sops.push({
    module_code: `MODUL ${moduleNum}`,
    sop_code: sopCode,
    title: title,
    slug: slug,
    order: (moduleNum * 10) + sopSubNum,
    purpose: purpose,
    scope: scope,
    responsibilities: responsibilities,
    indicators: indicators,
    reference_forms: referenceForms,
    content_html: contentHtml
  });
}

// Extract Guidelines
const guidelines = [];

// Ringkasan Eksekutif
const ringkasanMatch = masterMd.match(/## RINGKASAN EKSEKUTIF([\s\S]*?)(?=# BAGIAN I)/);
if (ringkasanMatch) {
  guidelines.push({
    section_code: 'RINGKASAN_EKSEKUTIF',
    title: 'Ringkasan Eksekutif Standar Mutu Snaprint',
    slug: 'ringkasan-eksekutif',
    order: 1,
    content_html: parseMarkdown('## RINGKASAN EKSEKUTIF' + ringkasanMatch[1])
  });
}

// Bagian I
const bagian1Match = masterMd.match(/# BAGIAN I: PERSYARATAN & PEDOMAN PEMBUKAAN OUTLET BARU([\s\S]*?)(?=# BAGIAN II)/);
if (bagian1Match) {
  guidelines.push({
    section_code: 'BAGIAN_I',
    title: 'Bagian I: Persyaratan & Pedoman Pembukaan Outlet Baru (Site Opening Guidelines)',
    slug: 'pedoman-pembukaan-outlet-baru',
    order: 2,
    content_html: parseMarkdown('# BAGIAN I: PERSYARATAN & PEDOMAN PEMBUKAAN OUTLET BARU' + bagian1Match[1])
  });
}

// Bagian II
const bagian2Match = masterMd.match(/# BAGIAN II: STANDAR ANATOMI PENULISAN DOKUMEN SOP([\s\S]*?)(?=# BAGIAN III)/);
if (bagian2Match) {
  guidelines.push({
    section_code: 'BAGIAN_II',
    title: 'Bagian II: Standar Anatomi Penulisan Dokumen SOP & Tata Kelola Dokumen',
    slug: 'standar-anatomi-sop',
    order: 3,
    content_html: parseMarkdown('# BAGIAN II: STANDAR ANATOMI PENULISAN DOKUMEN SOP' + bagian2Match[1])
  });
}

// Lembar Pengesahan
const pengesahanMatch = masterMd.match(/(?:#|##)\s*LEMBAR PENGESAHAN DOKUMEN([\s\S]*?)$/);
if (pengesahanMatch) {
  guidelines.push({
    section_code: 'LEMBAR_PENGESAHAN',
    title: 'Lembar Pengesahan Dokumen & Otorisasi Direksi',
    slug: 'lembar-pengesahan',
    order: 4,
    content_html: parseMarkdown('## LEMBAR PENGESAHAN DOKUMEN' + pengesahanMatch[1])
  });
}

// Extract Forms from formsMd
const forms = [];
const formRegex = /### \[?(FRM-SNA-([A-Z0-9]+)-([0-9A-Z]+))\]?\s*(.*?)\n([\s\S]*?)(?=(### \[?FRM-SNA-|$))/g;
let formMatch;

const modMap = {
  'FO': 'MODUL 1',
  'PROD': 'MODUL 2',
  'MNT': 'MODUL 3',
  'INV': 'MODUL 4',
  'HRD': 'MODUL 5',
  'FIN': 'MODUL 6',
  'K3': 'MODUL 7'
};

let formOrder = 1;
while ((formMatch = formRegex.exec(formsMd)) !== null) {
  const formCode = formMatch[1].trim();
  const modKey = formMatch[2].trim();
  const formSub = formMatch[3].trim();
  const rawTitle = formMatch[4].trim();
  const rawBody = formMatch[5].trim();

  const refMatch = rawBody.match(/\*\(Rujukan:\s*(SOP-[0-9.]+.*?)\)\*/);
  const refSop = refMatch ? refMatch[1].trim() : '';

  const fullMd = `### [${formCode}] ${rawTitle}\n\n` + rawBody;
  const contentHtml = parseMarkdown(fullMd);

  const slug = formCode.toLowerCase().replace(/[^a-z0-9]+/g, '-');

  // Fields schema based on form
  let fieldsSchema = [
    { name: 'outlet_name', label: 'Nama Outlet / Cabang', type: 'text', required: true },
    { name: 'operator_name', label: 'Nama Petugas / PIC', type: 'text', required: true },
    { name: 'check_date', label: 'Tanggal Pemeriksaan', type: 'date', required: true },
    { name: 'shift', label: 'Shift Kerja', type: 'select', options: ['Pagi (08.00 - 16.00)', 'Sore (13.00 - 21.00)', 'Full Shift'], required: true },
    { name: 'notes', label: 'Catatan Operasional / Keterangan', type: 'textarea', required: false }
  ];

  if (formCode.includes('FO-01')) {
    fieldsSchema.push({
      name: 'opening_status',
      label: 'Status Buka Gerai',
      type: 'select',
      options: ['OK Lengkap Sesuai Standar', 'Ada Catatan Perbaikan', 'Kendala Serius (Eskalasi)'],
      required: true
    });
  } else if (formCode.includes('FIN-01')) {
    fieldsSchema.push(
      { name: 'total_cash', label: 'Total Uang Tunai Laci (Rp)', type: 'number', required: true },
      { name: 'total_edc', label: 'Total Transaksi EDC / QRIS (Rp)', type: 'number', required: true },
      { name: 'system_sales', label: 'Total Penjualan Sistem POS (Rp)', type: 'number', required: true },
      { name: 'cash_diff', label: 'Selisih Kas (Rp)', type: 'number', required: true }
    );
  } else if (formCode.includes('MNT-01')) {
    fieldsSchema.push(
      { name: 'machine_name', label: 'Nama & Tipe Mesin', type: 'text', required: true },
      { name: 'nozzle_status', label: 'Hasil Nozzle Check', type: 'select', options: ['100% Sempurna (No Clog)', 'Sedikit Putus (Auto Cleaned)', 'Buntu Berat (Eskalasi Teknisi)'], required: true },
      { name: 'counter_start', label: 'Counter Klik Awal', type: 'number', required: true },
      { name: 'counter_end', label: 'Counter Klik Akhir', type: 'number', required: true }
    );
  }

  forms.push({
    module_code: modMap[modKey] || 'MODUL 1',
    form_code: formCode,
    title: rawTitle,
    slug: slug,
    reference_sop: refSop,
    category: modKey,
    order: formOrder++,
    fields_schema: fieldsSchema,
    template_html: contentHtml
  });
}

const output = {
  company_profile: companyProfile,
  modules: modulesData,
  guidelines: guidelines,
  sops: sops,
  forms: forms
};

const outputDir = path.join(__dirname, 'snaprint_laravel', 'database', 'seeders', 'data');
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

fs.writeFileSync(path.join(outputDir, 'snaprint_seed.json'), JSON.stringify(output, null, 2), 'utf8');
console.log(`Successfully generated seed data: ${sops.length} SOPs, ${forms.length} Forms, ${modulesData.length} Modules, ${guidelines.length} Guidelines.`);
