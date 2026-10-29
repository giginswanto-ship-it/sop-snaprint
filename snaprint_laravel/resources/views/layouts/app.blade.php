<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET') - SNAPRINT DIGITAL PRINTING</title>
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
      padding: 10px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
      flex-wrap: wrap;
      gap: 12px;
    }
    .brand-wrapper {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .brand-title {
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #FFFFFF;
      text-decoration: none;
    }
    .brand-badge {
      background: var(--accent);
      color: #000;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .nav-tabs {
      display: flex;
      gap: 6px;
      align-items: center;
    }
    .nav-tab {
      color: #E2E8F0;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 6px;
      transition: all 0.2s;
    }
    .nav-tab:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #FFFFFF;
    }
    .nav-tab.active {
      background: var(--secondary);
      color: #FFFFFF;
    }
    .btn-group { display: flex; gap: 10px; align-items: center; }
    .btn {
      padding: 7px 14px;
      border-radius: 6px;
      font-size: 0.85rem;
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
    .btn-primary { background: var(--secondary); color: #FFFFFF; }
    .btn-primary:hover { background: var(--primary-light); }
    
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
    .toc-list li { margin-bottom: 4px; }
    .toc-list a {
      color: #475569;
      text-decoration: none;
      font-size: 0.84rem;
      display: block;
      padding: 5px 8px;
      border-radius: 4px;
      transition: all 0.15s;
    }
    .toc-list a:hover { background: #EFF6FF; color: var(--secondary); }
    .toc-level-1 { font-weight: 700; margin-top: 14px; }
    .toc-level-1 a { color: var(--primary); }
    .toc-level-2 { font-weight: 600; padding-left: 10px; }
    .toc-level-3 { padding-left: 18px; font-size: 0.82rem; color: #64748B; }
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
      padding: 46px 40px;
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
      border: none;
      padding: 0;
    }
    .cover-card .operator-tag {
      display: inline-block;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.95rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #E2E8F0;
    }
    .cover-card h2 {
      font-size: 1.45rem;
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
      margin: 0 auto 20px auto;
    }
    .cover-card .db-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(0, 0, 0, 0.35);
      color: #F8FAFC;
      font-size: 0.78rem;
      padding: 4px 12px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
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
    .math-block { text-align: center; padding: 10px; font-weight: bold; color: #0369A1; }
    .search-box { margin-bottom: 16px; position: relative; }
    .search-input {
      width: 100%;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--gray-border);
      font-size: 0.85rem;
      outline: none;
    }
    .search-input:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 2px rgba(44, 116, 179, 0.2);
    }
    .badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 700;
    }
    .badge-success { background: var(--success-bg); color: var(--success); }
    .badge-warning { background: #FEF3C7; color: #92400E; }
    .badge-info { background: #E0F2FE; color: #0369A1; }

    /* Flash alerts */
    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 0.92rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }

    @media print {
      @page { size: A4; margin: 15mm; }
      body { background: #FFFFFF !important; font-size: 10pt; color: #000000; }
      .top-bar, .sidebar, .btn-group, .search-box, .nav-tabs { display: none !important; }
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
  @yield('styles')
</head>
<body>
  <header class="top-bar">
    <div class="brand-wrapper">
      <a href="{{ route('sop.index') }}" class="brand-title">
        <span>SNAPRINT DIGITAL PRINTING</span>
      </a>
      <span class="brand-badge">@yield('badge', 'SQL Laravel System')</span>
    </div>

    <nav class="nav-tabs">
      <a href="{{ route('sop.index') }}" class="nav-tab {{ request()->routeIs('sop.*') ? 'active' : '' }}">📘 Master SOP</a>
      <a href="{{ route('forms.index') }}" class="nav-tab {{ request()->routeIs('forms.*') ? 'active' : '' }}">📋 Dokumen Kontrol</a>
      <a href="{{ route('submissions.index') }}" class="nav-tab {{ request()->routeIs('submissions.*') ? 'active' : '' }}">📝 Logbook &amp; Arsip</a>
      <a href="{{ route('system.index') }}" class="nav-tab {{ request()->routeIs('system.*') ? 'active' : '' }}">⚙️ Status SQL</a>
    </nav>

    <div class="btn-group">
      <button class="btn btn-secondary" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">⬆ Ke Atas</button>
      <button class="btn btn-download" onclick="window.print()">📥 Unduh / Cetak PDF</button>
    </div>
  </header>

  <div class="app-container">
    @yield('sidebar')
    @yield('content')
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
  @yield('scripts')
</body>
</html>
