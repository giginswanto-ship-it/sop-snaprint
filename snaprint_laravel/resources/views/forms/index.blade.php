@extends('layouts.app')

@section('title', 'BUNDEL MASTER LAMPIRAN & DOKUMEN KONTROL OPERASIONAL')
@section('badge', 'Operational Control Forms')

@section('sidebar')
<aside class="sidebar">
  <div class="search-box">
    <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari formulir kontrol..." onkeyup="filterTOC()">
  </div>

  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>INDEKS FORMULIR</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="#sec-indeks-resmi">INDEKS RESMI DOKUMEN KONTROL</a>
      </li>
      @foreach($modules as $mod)
        <li class="toc-level-2">
          <strong>{{ $mod->code }}: {{ $mod->category ?? $mod->title }}</strong>
        </li>
        @foreach($mod->forms as $f)
          <li class="toc-level-3">
            <a href="#sec-{{ $f->slug }}">
              [{{ $f->form_code }}] {{ Str::limit($f->title, 28) }}
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
  <div class="cover-card">
    <h1>{{ $company->brand_name ?? 'SNAPRINT DIGITAL PRINTING' }}</h1>
    <div class="operator-tag">operated by {{ $company->operator_url ?? 'dutaglobaltech.com' }}</div>
    <h2>BUNDEL MASTER LAMPIRAN &amp; DOKUMEN KONTROL OPERASIONAL</h2>
    <p class="sub-title">Format Standar Formulir, Checklist, Logbook &amp; Dokumen Kendali Mutu (7 Modul / 26 SOP)</p>
    <div>
      <span class="db-badge">📋 30 Dokumen Kontrol Mutu Tersimpan di Database SQL</span>
    </div>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Informasi Dokumen</th>
          <th>Spesifikasi Dokumen</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Kode Dokumen</strong></td>
          <td>FRM-SNA-MASTER-2026-V1</td>
        </tr>
        <tr>
          <td><strong>Status Dokumen</strong></td>
          <td>Master Operational Control Templates (Dokumen Kontrol Resmi)</td>
        </tr>
        <tr>
          <td><strong>Penerbit</strong></td>
          <td>Divisi Operasional, Mutu &amp; Standarisasi Franchise Snaprint</td>
        </tr>
        <tr>
          <td><strong>Pengelola Sistem</strong></td>
          <td>{{ $company->operator_name ?? 'PT Duta Global Tech' }} (<em>{{ $company->operator_url ?? 'dutaglobaltech.com' }}</em>)</td>
        </tr>
        <tr>
          <td><strong>Tanggal Efektif</strong></td>
          <td>{{ $company->effective_date ?? '1 November 2026' }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="callout callout-note">
    <strong>CATATAN PENGGUNAAN FORMULIR:</strong><br>
    Seluruh template formulir di dalam bundel ini merupakan turunan langsung dari ketentuan <strong>Point 6 (Lampiran &amp; Dokumen Kontrol)</strong> pada masing-masing Standar Operasional Prosedur (SOP) Snaprint Digital Printing. Format ini dapat dicetak fisik (<em>hardcopy print</em>) atau diisi secara langsung melalui sistem form digital berbasis SQL di bawah ini.
  </div>

  <hr class="section-divider" />

  <section id="sec-indeks-resmi">
    <h1 class="heading-1">MASTER FORMAT DOKUMEN KONTROL OPERASIONAL RESMI</h1>

    @foreach($modules as $mod)
      <div style="margin-top: 40px;">
        <h2 class="heading-2">{{ $mod->code }}: {{ $mod->title }} @if($mod->category)({{ $mod->category }})@endif</h2>

        @foreach($mod->forms as $form)
          <article id="sec-{{ $form->slug }}" style="margin-top: 32px; padding: 24px; border: 1px solid var(--gray-border); border-radius: 8px; background: #FAFAFA;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 16px;">
              <div>
                <span class="badge badge-info" style="font-size: 0.82rem;">{{ $form->form_code }}</span>
                <h3 style="margin: 6px 0 2px 0; color: var(--primary);">{{ $form->title }}</h3>
                @if($form->reference_sop)
                  <small style="color: #64748B;"><em>Rujukan: {{ $form->reference_sop }}</em></small>
                @endif
              </div>
              <div style="display: flex; gap: 8px;">
                <a href="{{ route('forms.show', $form->slug) }}" class="btn btn-primary" style="font-size: 0.8rem; padding: 6px 12px;">
                  📝 Isi / Cetak Form
                </a>
              </div>
            </div>

            <div class="form-body" style="background: #FFFFFF; border-radius: 6px; padding: 12px; border: 1px solid #E2E8F0;">
              {!! $form->template_html !!}
            </div>
          </article>
        @endforeach
      </div>
    @endforeach
  </section>
</main>
@endsection
