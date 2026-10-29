@extends('layouts.app')

@section('title', $sop->sop_code . ' - ' . $sop->title)
@section('badge', $sop->sop_code)

@section('sidebar')
<aside class="sidebar">
  <div class="search-box">
    <input type="text" id="sopSearch" class="search-input" placeholder="🔍 Cari SOP lain..." onkeyup="filterTOC()">
  </div>

  <nav class="sidebar-toc">
    <div class="toc-header">
      <h3>NAVIGASI SOP</h3>
    </div>
    <ul class="toc-list">
      <li class="toc-level-1">
        <a href="{{ route('sop.index') }}">⬅ Kembali ke Buku Induk</a>
      </li>
      @foreach($modules as $mod)
        <li class="toc-level-2">
          <strong>{{ $mod->code }}</strong>
        </li>
        @foreach($mod->sops as $s)
          <li class="toc-level-3">
            <a href="{{ route('sop.show', $s->slug) }}" class="{{ $s->id === $sop->id ? 'active' : '' }}">
              {{ $s->sop_code }}: {{ $s->title }}
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
    <a href="{{ route('sop.index') }}" style="color: var(--secondary); text-decoration: none;">Buku Induk</a> &gt; 
    <span>{{ $sop->module->code ?? 'Modul' }}</span> &gt; 
    <span style="color: var(--primary); font-weight: 600;">{{ $sop->sop_code }}</span>
  </nav>

  <div class="sop-card-header" style="margin-top: 0; margin-bottom: 24px;">
    <h1 style="border: none; margin: 0; padding: 0; font-size: 1.6rem; color: var(--primary);">
      {{ $sop->sop_code }}: {{ $sop->title }}
    </h1>
    <div style="margin-top: 6px; font-size: 0.88rem; color: #64748B;">
      Kategori: <strong>{{ $sop->module->category ?? 'Operasional' }}</strong> &bull; Status: <span class="badge badge-success">Aktif / Berlaku</span>
    </div>
  </div>

  <div class="sop-content">
    {!! $sop->content_html !!}
  </div>

  @if($relatedForms->count() > 0)
    <hr class="section-divider" />
    <div style="background: #F8FAFC; border: 1px solid var(--gray-border); border-radius: 8px; padding: 20px;">
      <h3 style="margin-top: 0; margin-bottom: 12px; color: var(--primary);">📋 Formulir &amp; Dokumen Kontrol Terkait:</h3>
      <p style="font-size: 0.9rem; margin-bottom: 14px;">Formulir kontrol resmi yang dirujuk oleh SOP ini dapat diakses, dicetak, atau diisi secara digital:</p>
      <div style="display: flex; flex-direction: column; gap: 8px;">
        @foreach($relatedForms as $form)
          <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; padding: 10px 14px; border-radius: 6px; border: 1px solid #E2E8F0;">
            <div>
              <strong style="color: var(--primary);">[{{ $form->form_code }}]</strong> {{ $form->title }}
            </div>
            <a href="{{ route('forms.show', $form->slug) }}" class="btn btn-primary" style="font-size: 0.78rem; padding: 5px 12px;">
              Buka Formulir ➔
            </a>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <div style="margin-top: 36px; display: flex; justify-content: space-between;">
    <a href="{{ route('sop.index') }}" class="btn btn-secondary" style="color: var(--primary); background: #E2E8F0;">
      ⬅ Kembali ke Buku Induk
    </a>
    <button class="btn btn-download" onclick="window.print()">
      🖨️ Cetak SOP Ini
    </button>
  </div>
</main>
@endsection
