@extends('layouts.app')

@section('title', $company->document_title ?? 'BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET')
@section('badge', 'Master Franchise Manual')

@section('sidebar')
<aside class="sidebar">
  <div class="search-box">
    <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari SOP / Form / Topik..." onkeyup="filterTOC()">
  </div>

  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>DAFTAR ISI</h3>
    </div>
    <ul class="toc-list">
      @foreach($guidelines as $g)
        <li class="toc-level-1">
          <a href="#sec-{{ $g->slug }}">{{ $g->title }}</a>
        </li>
      @endforeach

      <li class="toc-level-1">
        <a href="#sec-bagian-iii-bundel-7-modul-26-sop">BAGIAN III: BUNDEL 7 MODUL &amp; 26 SOP</a>
      </li>

      @foreach($modules as $mod)
        <li class="toc-level-2">
          <a href="#sec-{{ Str::slug($mod->code . ' ' . $mod->title) }}">
            {{ $mod->code }}: {{ $mod->title }}
          </a>
        </li>
        @foreach($mod->sops as $sop)
          <li class="toc-level-3">
            <a href="#sec-{{ $sop->slug }}">
              {{ $sop->sop_code }}: {{ $sop->title }}
            </a>
          </li>
        @endforeach
      @endforeach

      <li class="toc-level-1">
        <a href="{{ route('forms.index') }}">BAGIAN IV: FORMULIR KONTROL ({{ $totalFormsCount }})</a>
      </li>
    </ul>
  </nav>
</aside>
@endsection

@section('content')
<main class="main-content">
  <div class="cover-card">
    <h1>{{ $company->brand_name ?? 'SNAPRINT DIGITAL PRINTING' }}</h1>
    <div class="operator-tag">operated by {{ $company->operator_url ?? 'dutaglobaltech.com' }}</div>
    <h2>{{ $company->document_title ?? 'BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET' }}</h2>
    <p class="sub-title">{{ $company->document_subtitle ?? 'Dokumen Komprehensif: Struktur SOP, Kualifikasi Kemitraan & Persyaratan Pembukaan Gerai Baru' }}</p>
    <div>
      <span class="db-badge">🗄️ SQL Database Driven &bull; Laravel Framework &bull; Versi {{ $company->document_version ?? '1.0' }}</span>
    </div>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Informasi Dokumen</th>
          <th>Spesifikasi Sistem</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Status Dokumen</strong></td>
          <td>{{ $company->status ?? 'Master Franchise Operations Manual (Panduan Induk Operasional)' }}</td>
        </tr>
        <tr>
          <td><strong>Versi Dokumen</strong></td>
          <td>{{ $company->document_version ?? '1.0 (Standar Kemitraan Nasional)' }}</td>
        </tr>
        <tr>
          <td><strong>Pengelola Sistem / Operator</strong></td>
          <td>{{ $company->operator_name ?? 'PT Duta Global Tech' }} (<em>{{ $company->operator_url ?? 'dutaglobaltech.com' }}</em>)</td>
        </tr>
        <tr>
          <td><strong>Sifat Dokumen</strong></td>
          <td><strong style="color: var(--danger);">Sangat Rahasia (Strictly Confidential)</strong></td>
        </tr>
        <tr>
          <td><strong>Tanggal Berlaku</strong></td>
          <td>{{ $company->effective_date ?? '1 November 2026' }}</td>
        </tr>
        <tr>
          <td><strong>Basis Data &amp; Framework</strong></td>
          <td>Relational SQL Database (SQLite / MySQL) &amp; Laravel 10 MVC Architecture</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="callout callout-important">
    <strong>PERNYATAAN KERAHASIAN &amp; HAK KEKAYAAN INTELEKTUAL:</strong><br>
    {{ $company->confidentiality_statement }}
  </div>

  <hr class="section-divider" />

  <!-- Guidelines (Ringkasan Eksekutif, Bagian I, Bagian II) -->
  @foreach($guidelines as $g)
    <section id="sec-{{ $g->slug }}" class="guideline-section">
      {!! $g->content_html !!}
    </section>
    <hr class="section-divider" />
  @endforeach

  <!-- Bagian III: 7 Modul & 26 SOP -->
  <section id="sec-bagian-iii-bundel-7-modul-26-sop">
    <h1 id="sec-bagian-iii" class="heading-1">BAGIAN III: BUNDEL 7 MODUL &amp; 26 STANDAR OPERASIONAL PROSEDUR (SOP)</h1>
    <p>Seluruh prosedur baku operasional gerai mitra waralaba Snaprint Digital Printing dikelompokkan ke dalam 7 pilar modul utama yang terintegrasi secara menyeluruh:</p>

    @foreach($modules as $mod)
      <div id="sec-{{ Str::slug($mod->code . ' ' . $mod->title) }}" style="margin-top: 48px;">
        <h2 class="heading-2">{{ $mod->code }}: {{ $mod->title }} @if($mod->category)({{ $mod->category }})@endif</h2>
        @if($mod->description)
          <p><em>{{ $mod->description }}</em></p>
        @endif

        @foreach($mod->sops as $sop)
          <article id="sec-{{ $sop->slug }}" class="sop-item" style="margin-top: 36px; padding-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
              <h3 id="sec-{{ Str::slug($sop->sop_code . '-' . $sop->title) }}" class="sop-card-header" style="flex: 1; margin: 0;">
                {{ $sop->sop_code }}: {{ $sop->title }}
              </h3>
              <a href="{{ route('sop.show', $sop->slug) }}" class="btn btn-secondary" style="color: var(--primary); background: #E2E8F0; font-size: 0.78rem;">
                🔍 Tampilan Khusus
              </a>
            </div>

            <div class="sop-body" style="margin-top: 16px;">
              {!! $sop->content_html !!}
            </div>
          </article>
          <hr class="section-divider" />
        @endforeach
      </div>
    @endforeach
  </section>
</main>
@endsection
