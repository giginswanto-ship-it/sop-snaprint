@extends('layouts.app')

@section('title', '[' . $form->form_code . '] ' . $form->title)
@section('badge', $form->form_code)

@section('styles')
<style>
  .form-input-group {
    margin-bottom: 16px;
  }
  .form-input-group label {
    display: block;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 6px;
  }
  .form-control {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--gray-border);
    border-radius: 6px;
    font-size: 0.9rem;
    outline: none;
    font-family: inherit;
  }
  .form-control:focus {
    border-color: var(--secondary);
    box-shadow: 0 0 0 2px rgba(44, 116, 179, 0.2);
  }
</style>
@endsection

@section('sidebar')
<aside class="sidebar">
  <div class="search-box">
    <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari formulir lain..." onkeyup="filterTOC()">
  </div>

  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>DOKUMEN KONTROL</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="{{ route('forms.index') }}">⬅ Semua Dokumen Kontrol</a>
      </li>
      @foreach($modules as $mod)
        <li class="toc-level-2">
          <strong>{{ $mod->code }}</strong>
        </li>
        @foreach($mod->forms as $f)
          <li class="toc-level-3">
            <a href="{{ route('forms.show', $f->slug) }}" class="{{ $f->id === $form->id ? 'active' : '' }}">
              [{{ $f->form_code }}] {{ Str::limit($f->title, 26) }}
            </a>
          </li>
        @endforeach
      @endforeach
    </ul>
  </nav>
</aside>
@endsection

@section('content')
<main class="main-content">
  <nav style="margin-bottom: 24px; font-size: 0.88rem; color: #64748B;">
    <a href="{{ route('forms.index') }}" style="color: var(--secondary); text-decoration: none;">Dokumen Kontrol</a> &gt; 
    <span>{{ $form->module->code ?? 'Modul' }}</span> &gt; 
    <span style="color: var(--primary); font-weight: 600;">{{ $form->form_code }}</span>
  </nav>

  <div class="sop-card-header" style="margin-top: 0; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div>
      <span class="badge badge-info">{{ $form->form_code }}</span>
      <h1 style="border: none; margin: 4px 0 0 0; padding: 0; font-size: 1.5rem; color: var(--primary);">
        {{ $form->title }}
      </h1>
      @if($form->reference_sop)
        <div style="font-size: 0.85rem; color: #64748B; margin-top: 4px;">
          Rujukan Prosedur: <strong>{{ $form->reference_sop }}</strong>
        </div>
      @endif
    </div>
    <div class="btn-group">
      <button class="btn btn-download" onclick="window.print()">🖨️ Cetak Formulir Kosong</button>
      <a href="#digital-form-section" class="btn btn-primary" onclick="document.getElementById('digital-form-section').scrollIntoView({behavior: 'smooth'})">
        📝 Isi Form Digital
      </a>
    </div>
  </div>

  <!-- Format Standar Resmi -->
  <div style="margin-bottom: 40px;">
    <h3 style="color: var(--primary); margin-top: 0;">Template Standar Format Fisik:</h3>
    <div style="background: #FFFFFF; border: 1px solid var(--gray-border); border-radius: 8px; padding: 20px;">
      {!! $form->template_html !!}
    </div>
  </div>

  <hr class="section-divider" />

  <!-- Digital Interactive Submission Form -->
  <section id="digital-form-section" style="background: #F8FAFC; border: 2px solid var(--secondary); border-radius: 10px; padding: 30px; margin-top: 40px;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
      <span style="font-size: 1.5rem;">✍️</span>
      <div>
        <h2 style="margin: 0; font-size: 1.3rem; color: var(--primary); border: none; padding: 0;">
          Pengisian Formulir Digital Langsung ke Database SQL
        </h2>
        <p style="margin: 0; font-size: 0.85rem; color: #64748B;">
          Data akan disimpan permanen ke tabel <code>form_submissions</code> dan dapat dipantau dari menu Rekap Logbook.
        </p>
      </div>
    </div>

    @if ($errors->any())
      <div class="callout callout-warning">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('forms.submit', $form->slug) }}" method="POST">
      @csrf

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <div class="form-input-group">
          <label>Nama Gerai / Cabang Outlet <span style="color: var(--danger);">*</span></label>
          <input type="text" name="outlet_name" class="form-control" placeholder="Contoh: Snaprint Cabang BSD" value="{{ old('outlet_name') }}" required>
        </div>

        <div class="form-input-group">
          <label>Nama Petugas / PIC <span style="color: var(--danger);">*</span></label>
          <input type="text" name="submitted_by" class="form-control" placeholder="Nama lengkap petugas" value="{{ old('submitted_by') }}" required>
        </div>

        <div class="form-input-group">
          <label>Tanggal Pelaksanaan <span style="color: var(--danger);">*</span></label>
          <input type="date" name="submission_date" class="form-control" value="{{ old('submission_date', date('Y-m-d')) }}" required>
        </div>
      </div>

      <!-- Dynamic fields according to schema -->
      @if(!empty($form->fields_schema))
        <div style="margin-top: 10px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
          @foreach($form->fields_schema as $field)
            @if(!in_array($field['name'], ['outlet_name', 'operator_name', 'check_date', 'notes']))
              <div class="form-input-group">
                <label>{{ $field['label'] }} @if(!empty($field['required']))<span style="color: var(--danger);">*</span>@endif</label>
                @if($field['type'] === 'select')
                  <select name="{{ $field['name'] }}" class="form-control" @if(!empty($field['required'])) required @endif>
                    @foreach($field['options'] as $opt)
                      <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                  </select>
                @elseif($field['type'] === 'number')
                  <input type="number" name="{{ $field['name'] }}" class="form-control" placeholder="0" @if(!empty($field['required'])) required @endif>
                @else
                  <input type="text" name="{{ $field['name'] }}" class="form-control" @if(!empty($field['required'])) required @endif>
                @endif
              </div>
            @endif
          @endforeach
        </div>
      @endif

      <div class="form-input-group" style="margin-top: 10px;">
        <label>Catatan Operasional / Keterangan Temuan</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Masukkan catatan kondisi fisik, tindak lanjut, atau keterangan tambahan...">{{ old('notes') }}</textarea>
      </div>

      <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="reset" class="btn btn-secondary" style="color: #475569; background: #E2E8F0;">Reset</button>
        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 0.95rem;">
          💾 Simpan Dokumen Kontrol ke Database SQL
        </button>
      </div>
    </form>
  </section>

  @if($recentSubmissions->count() > 0)
    <div style="margin-top: 40px;">
      <h3 style="color: var(--primary);">Histori Logbook Terakhir Formulir Ini:</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Tanggal</th>
              <th>Gerai / Cabang</th>
              <th>Petugas</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentSubmissions as $sub)
              <tr>
                <td>#{{ $sub->id }}</td>
                <td>{{ $sub->submission_date ? $sub->submission_date->format('d/m/Y') : '-' }}</td>
                <td><strong>{{ $sub->outlet_name }}</strong></td>
                <td>{{ $sub->submitted_by }}</td>
                <td>
                  <span class="badge {{ $sub->status === 'Approved' ? 'badge-success' : ($sub->status === 'Verified' ? 'badge-info' : 'badge-warning') }}">
                    {{ $sub->status }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('submissions.show', $sub->id) }}" style="color: var(--secondary); font-weight: 600; text-decoration: none;">
                    Lihat Dokumen ➔
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</main>
@endsection
