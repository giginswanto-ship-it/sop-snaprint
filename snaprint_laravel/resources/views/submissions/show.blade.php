@extends('layouts.app')

@section('title', 'Dokumen Kontrol #' . $submission->id . ' [' . $submission->form_code . ']')
@section('badge', $submission->form_code)

@section('sidebar')
<aside class="sidebar">
  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>NAVIGASI</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="{{ route('submissions.index') }}">⬅ Kembali ke Daftar Logbook</a>
      </li>
      <li class="toc-level-1">
        <a href="{{ route('forms.show', $submission->documentForm->slug ?? Str::slug($submission->form_code)) }}">
          📄 Template Resmi [{{ $submission->form_code }}]
        </a>
      </li>
      <li class="toc-level-1">
        <a href="{{ route('sop.index') }}">📘 Buku Induk SOP</a>
      </li>
    </ul>
  </nav>
</aside>
@endsection

@section('content')
<main class="main-content">
  <nav style="margin-bottom: 24px; font-size: 0.88rem; color: #64748B;">
    <a href="{{ route('submissions.index') }}" style="color: var(--secondary); text-decoration: none;">Arsip Logbook</a> &gt; 
    <span>{{ $submission->form_code }}</span> &gt; 
    <span style="color: var(--primary); font-weight: 600;">#{{ $submission->id }}</span>
  </nav>

  @if(session('success'))
    <div class="alert alert-success">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="sop-card-header" style="margin-top: 0; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div>
      <span class="badge badge-info">{{ $submission->form_code }}</span>
      <h1 style="border: none; margin: 4px 0 0 0; padding: 0; font-size: 1.5rem; color: var(--primary);">
        Dokumen Rekaman Kontrol #{{ $submission->id }}: {{ $submission->documentForm->title ?? $submission->form_code }}
      </h1>
      <div style="font-size: 0.88rem; color: #64748B; margin-top: 4px;">
        Tanggal Entri: <strong>{{ $submission->submission_date ? $submission->submission_date->format('d F Y') : '-' }}</strong> &bull;
        Waktu Submit: {{ $submission->created_at->format('d/m/Y H:i') }}
      </div>
    </div>
    <div class="btn-group">
      <button class="btn btn-download" onclick="window.print()">🖨️ Cetak Berita Acara / Rekaman</button>
    </div>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th colspan="2">Parameter Entri Dokumen Operasional</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width: 250px;"><strong>Status Validasi Dokumen</strong></td>
          <td>
            <span class="badge {{ $submission->status === 'Approved' ? 'badge-success' : ($submission->status === 'Verified' ? 'badge-info' : 'badge-warning') }}" style="font-size: 0.85rem;">
              {{ $submission->status }}
            </span>
          </td>
        </tr>
        <tr>
          <td><strong>Nama Gerai / Cabang Outlet</strong></td>
          <td><strong style="font-size: 1.05rem; color: var(--primary);">{{ $submission->outlet_name }}</strong></td>
        </tr>
        <tr>
          <td><strong>Petugas Pelaksana (PIC)</strong></td>
          <td>{{ $submission->submitted_by }}</td>
        </tr>
        <tr>
          <td><strong>Tanggal Pelaksanaan</strong></td>
          <td>{{ $submission->submission_date ? $submission->submission_date->format('l, d F Y') : '-' }}</td>
        </tr>
        <tr>
          <td><strong>Catatan &amp; Temuan Fisik</strong></td>
          <td>{{ $submission->notes ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <h3 style="margin-top: 36px; color: var(--primary);">Rincian Data Formulir Terverifikasi:</h3>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th style="width: 250px;">Item / Parameter</th>
          <th>Nilai / Hasil Pemeriksaan</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($submission->form_data) && is_array($submission->form_data))
          @foreach($submission->form_data as $key => $val)
            <tr>
              <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
              <td>
                @if(is_numeric($val) && strpos($key, 'total') !== false)
                  Rp {{ number_format($val, 0, ',', '.') }}
                @elseif(is_bool($val))
                  {{ $val ? 'YA / SESUAI' : 'TIDAK / CACAT' }}
                @else
                  {{ $val }}
                @endif
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="2">Tidak ada data khusus tersimpan.</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <!-- Status Update / Verification Action -->
  <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-radius: 8px; padding: 20px; margin-top: 36px;">
    <h3 style="margin-top: 0; margin-bottom: 12px; color: var(--primary);">🔍 Otorisasi &amp; Verifikasi Store Manager / Tim Pusat:</h3>
    <form action="{{ route('submissions.updateStatus', $submission->id) }}" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
      @csrf
      <div style="width: 200px;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Ubah Status:</label>
        <select name="status" class="search-input" style="height: 38px;">
          <option value="Submitted" {{ $submission->status === 'Submitted' ? 'selected' : '' }}>Submitted (Baru)</option>
          <option value="Verified" {{ $submission->status === 'Verified' ? 'selected' : '' }}>Verified (Diverifikasi)</option>
          <option value="Approved" {{ $submission->status === 'Approved' ? 'selected' : '' }}>Approved (Disahkan)</option>
        </select>
      </div>

      <div style="flex: 1; min-width: 250px;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Catatan Verifikasi Tambahan:</label>
        <input type="text" name="notes" class="search-input" style="height: 38px;" placeholder="Catatan paraf / approval tim pusat...">
      </div>

      <button type="submit" class="btn btn-primary" style="padding: 9px 20px;">
        💾 Simpan Validasi
      </button>
    </form>
  </div>
</main>
@endsection
