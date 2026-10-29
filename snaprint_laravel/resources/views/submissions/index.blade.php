@extends('layouts.app')

@section('title', 'REKAPITULASI LOGBOOK & ARSIP DOKUMEN KONTROL')
@section('badge', 'SQL Logbook Archive')

@section('sidebar')
<aside class="sidebar">
  <div class="search-box">
    <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari filter form..." onkeyup="filterTOC()">
  </div>

  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>NAVIGASI ARSIP</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="{{ route('submissions.index') }}" class="{{ !request('status') ? 'active' : '' }}">
          📋 Semua Arsip Dokumen
        </a>
      </li>
      <li class="toc-level-2">
        <a href="{{ route('submissions.index', ['status' => 'Submitted']) }}">
          ⏳ Status: Submitted
        </a>
      </li>
      <li class="toc-level-2">
        <a href="{{ route('submissions.index', ['status' => 'Verified']) }}">
          🔍 Status: Verified
        </a>
      </li>
      <li class="toc-level-2">
        <a href="{{ route('submissions.index', ['status' => 'Approved']) }}">
          ✅ Status: Approved
        </a>
      </li>
      <li class="toc-level-1" style="margin-top: 20px;">
        <a href="{{ route('forms.index') }}">➕ Buat Pengisian Baru</a>
      </li>
    </ul>
  </nav>
</aside>
@endsection

@section('content')
<main class="main-content">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <div>
      <h1 style="border: none; margin: 0; padding: 0; font-size: 1.6rem; color: var(--primary);">
        Rekapitulasi Logbook &amp; Arsip Dokumen Kontrol Mutu
      </h1>
      <p style="margin: 4px 0 0 0; color: #64748B; font-size: 0.9rem;">
        Catatan inspeksi harian, checklist pra-cetak, logbook mesin, dan rekonsiliasi kas tersimpan permanen di database SQL.
      </p>
    </div>
    <a href="{{ route('forms.index') }}" class="btn btn-download">
      ➕ Isi Formulir Kontrol
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">
      ✅ {{ session('success') }}
    </div>
  @endif

  <!-- Filter Card -->
  <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-radius: 8px; padding: 18px; margin-bottom: 24px;">
    <form action="{{ route('submissions.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
      <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;">Cari Nama Gerai / Cabang</label>
        <input type="text" name="outlet" class="search-input" placeholder="Nama cabang outlet..." value="{{ request('outlet') }}">
      </div>

      <div style="width: 220px;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;">Filter Tipe Formulir</label>
        <select name="form_code" class="search-input" style="height: 35px;">
          <option value="">-- Semua Formulir --</option>
          @foreach($forms as $f)
            <option value="{{ $f->form_code }}" {{ request('form_code') === $f->form_code ? 'selected' : '' }}>
              {{ $f->form_code }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="width: 160px;">
        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;">Status Validasi</label>
        <select name="status" class="search-input" style="height: 35px;">
          <option value="">-- Semua --</option>
          <option value="Submitted" {{ request('status') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
          <option value="Verified" {{ request('status') === 'Verified' ? 'selected' : '' }}>Verified</option>
          <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary" style="padding: 7px 18px;">
        🔍 Filter
      </button>
      @if(request()->hasAny(['outlet', 'form_code', 'status']))
        <a href="{{ route('submissions.index') }}" class="btn btn-secondary" style="color: #475569; background: #E2E8F0;">
          Reset
        </a>
      @endif
    </form>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Kode Form</th>
          <th>Nama Formulir</th>
          <th>Outlet Gerai</th>
          <th>Petugas PIC</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($submissions as $sub)
          <tr>
            <td>#{{ $sub->id }}</td>
            <td>{{ $sub->submission_date ? $sub->submission_date->format('d/m/Y') : '-' }}</td>
            <td><strong style="color: var(--secondary);">{{ $sub->form_code }}</strong></td>
            <td>{{ $sub->documentForm->title ?? '-' }}</td>
            <td><strong>{{ $sub->outlet_name }}</strong></td>
            <td>{{ $sub->submitted_by }}</td>
            <td>
              <span class="badge {{ $sub->status === 'Approved' ? 'badge-success' : ($sub->status === 'Verified' ? 'badge-info' : 'badge-warning') }}">
                {{ $sub->status }}
              </span>
            </td>
            <td>
              <a href="{{ route('submissions.show', $sub->id) }}" class="btn btn-primary" style="font-size: 0.75rem; padding: 4px 10px;">
                Buka ➔
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 30px; color: #94A3B8;">
              Belum ada arsip dokumen kontrol yang cocok dengan filter yang dipilih.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top: 20px;">
    {{ $submissions->links() }}
  </div>
</main>
@endsection
