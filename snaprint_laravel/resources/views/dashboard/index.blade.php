@extends('layouts.app')

@section('title', 'STATUS SISTEM & DATABASE SQL')
@section('badge', 'SQL Dashboard')

@section('sidebar')
<aside class="sidebar">
  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>MENU UTAMA</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="{{ route('sop.index') }}">📘 Buku Induk SOP</a>
      </li>
      <li class="toc-level-1">
        <a href="{{ route('forms.index') }}">📋 Dokumen Kontrol</a>
      </li>
      <li class="toc-level-1">
        <a href="{{ route('submissions.index') }}">📝 Logbook &amp; Arsip</a>
      </li>
      <li class="toc-level-1">
        <a href="{{ route('system.index') }}" class="active">⚙️ Status Database SQL</a>
      </li>
    </ul>
  </nav>
</aside>
@endsection

@section('content')
<main class="main-content">
  <div style="margin-bottom: 30px;">
    <h1 style="border: none; margin: 0; padding: 0; font-size: 1.8rem; color: var(--primary);">
      Status Sistem &amp; Arsitektur Basis Data SQL
    </h1>
    <p style="margin: 6px 0 0 0; color: #64748B;">
      Sistem SOP Snaprint Digital Printing beroperasi penuh dengan backend Laravel 10 dan penyimpanan relasional SQL.
    </p>
  </div>

  <!-- Metric Cards Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
    <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-top: 4px solid var(--primary); padding: 18px; border-radius: 8px;">
      <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #64748B;">Total Modul</div>
      <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin: 6px 0;">{{ $totalModules }}</div>
      <div style="font-size: 0.78rem; color: #64748B;">7 Pilar Operasional Lengkap</div>
    </div>

    <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-top: 4px solid var(--secondary); padding: 18px; border-radius: 8px;">
      <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #64748B;">Total SOP Prosedur</div>
      <div style="font-size: 2rem; font-weight: 800; color: var(--secondary); margin: 6px 0;">{{ $totalSops }}</div>
      <div style="font-size: 0.78rem; color: #64748B;">SOP-1.1 s/d SOP-7.3</div>
    </div>

    <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-top: 4px solid var(--accent); padding: 18px; border-radius: 8px;">
      <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #64748B;">Dokumen Kontrol</div>
      <div style="font-size: 2rem; font-weight: 800; color: var(--accent); margin: 6px 0;">{{ $totalForms }}</div>
      <div style="font-size: 0.78rem; color: #64748B;">Template Formulir &amp; Checklist</div>
    </div>

    <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-top: 4px solid var(--success); padding: 18px; border-radius: 8px;">
      <div style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #64748B;">Arsip Submisi Logbook</div>
      <div style="font-size: 2rem; font-weight: 800; color: var(--success); margin: 6px 0;">{{ $totalSubmissions }}</div>
      <div style="font-size: 0.78rem; color: #64748B;">Rekaman Terdata di Database</div>
    </div>
  </div>

  <!-- Database Connection Info -->
  <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px; padding: 20px; margin-bottom: 32px;">
    <h3 style="margin-top: 0; color: #1E40AF; display: flex; align-items: center; gap: 8px;">
      <span>🗄️</span> Konfigurasi Database SQL Aktif
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; font-size: 0.9rem; margin-top: 10px;">
      <div>
        <span style="color: #64748B;">Driver Koneksi:</span> <strong>{{ strtoupper($dbConnection) }}</strong>
      </div>
      <div>
        <span style="color: #64748B;">Status Koneksi:</span> <span class="badge badge-success">Terhubung &amp; Operasional</span>
      </div>
      <div>
        <span style="color: #64748B;">Kompatibilitas:</span> <strong>SQLite &amp; MySQL / MariaDB Ready</strong>
      </div>
    </div>
  </div>

  <h3 style="color: var(--primary);">Rincian Data Modul &amp; Struktur Relasional:</h3>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Kode Modul</th>
          <th>Nama &amp; Kategori Modul</th>
          <th>Jumlah SOP</th>
          <th>Jumlah Formulir</th>
          <th>Aksi Cepat</th>
        </tr>
      </thead>
      <tbody>
        @foreach($modules as $m)
          <tr>
            <td><strong>{{ $m->code }}</strong></td>
            <td>
              <strong>{{ $m->title }}</strong><br>
              <small style="color: #64748B;">{{ $m->category }}</small>
            </td>
            <td><span class="badge badge-info">{{ $m->sops_count }} SOP</span></td>
            <td><span class="badge badge-warning">{{ $m->forms_count }} Formulir</span></td>
            <td>
              <a href="{{ route('sop.index') }}#sec-{{ Str::slug($m->code . ' ' . $m->title) }}" style="color: var(--secondary); text-decoration: none; font-weight: 600;">
                Buka Modul ➔
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div style="margin-top: 36px; background: #F8FAFC; border: 1px solid var(--gray-border); border-radius: 8px; padding: 20px;">
    <h3 style="margin-top: 0; color: var(--primary);">💡 Panduan Menjalankan Sistem &amp; Pilihan Database:</h3>
    <div style="font-size: 0.9rem; line-height: 1.7; color: #334155;">
      <p>Sistem ini telah dikonfigurasi untuk siap jalan (<em>zero configuration</em>) menggunakan <strong>SQLite</strong> secara bawaan, dan sepenuhnya kompatibel dengan <strong>MySQL</strong>:</p>
      <ol style="margin-bottom: 0; padding-left: 20px;">
        <li><strong>Menjalankan Server Web Lokal:</strong><br>
          Cukup jalankan perintah: <code>php artisan serve</code> di folder <code>snaprint_laravel</code>, lalu buka <a href="http://localhost:8000" target="_blank" style="color: var(--secondary);">http://localhost:8000</a>.
        </li>
        <li style="margin-top: 8px;"><strong>Beralih ke MySQL (Opsional):</strong><br>
          Buka berkas <code>.env</code> di <code>snaprint_laravel</code>, sesuaikan:
          <pre style="margin: 8px 0; padding: 10px;">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snaprint_sop
DB_USERNAME=root
DB_PASSWORD=</pre>
          Lalu jalankan <code>php artisan migrate --seed</code> untuk memigrasikan dan mengisi seluruh 7 modul, 26 SOP, dan 30 formulir ke database MySQL.
        </li>
      </ol>
    </div>
  </div>
</main>
@endsection
