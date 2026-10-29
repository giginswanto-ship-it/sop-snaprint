# SNAPRINT DIGITAL PRINTING - SISTEM SOP & DOKUMEN KONTROL MUTU
### Berbasis Basis Data Relasional SQL & Berframework Laravel 10
**Operated by [dutaglobaltech.com](https://dutaglobaltech.com)**

---

## 📌 Ringkasan Sistem
Sistem ini merupakan implementasi penuh berframework **Laravel 10** dengan arsitektur **Database SQL** untuk sistem waralaba (*Franchise Operations Manual*) dan bundel Standar Operasional Prosedur (SOP) resmi **SNAPRINT DIGITAL PRINTING**.

Seluruh estetika visual, warna korporat (`#0A2647`, `#2C74B3`, `#D89216`), tipografi, sidebar TOC dinamis, cover-card gradien, tata letak tabel, callout, dan format siap cetak (*print-ready layout*) dipertahankan **100% identik tanpa merubah gaya dan desain sistem**.

---

## 🗄️ Struktur Basis Data SQL & Model Relasional

Sistem ini didukung oleh skema database SQL dengan relasi terstruktur:

1. **`company_profiles`**: Profil perusahaan PT Duta Global Tech, legalitas, hak cipta, dan pernyataan kerahasiaan.
2. **`sop_modules`**: 7 Modul Operasional (Front Office, Back Office & QC, Maintenance, Inventory, HRD, Finance, K3).
3. **`sops`**: 26 SOP Operasional lengkap dengan nomor SOP, tujuan, ruang lingkup, tanggung jawab, langkah prosedur, indikator kinerja, dan formulir rujukan.
4. **`document_forms`**: 30 Master Dokumen Kontrol / Formulir (FRM-SNA-FO-01 s/d FRM-SNA-K3-03B) lengkap dengan skema formulir digital.
5. **`guidelines`**: Panduan Ringkasan Eksekutif, Bagian I (Site Opening Guidelines), Bagian II (Standar Anatomi Dokumen), dan Lembar Pengesahan Direksi.
6. **`form_submissions`**: Logbook & arsip entri formulir digital harian per gerai outlet (inspeksi opening/closing, nozzle check mesin, rekonsiliasi kas, komplain retur, dsb).
7. **`users`**: Manajemen autentikasi akun administrator & store manager.

---

## 🚀 Panduan Menjalankan Sistem

### 1. Menjalankan Server Web (Zero Config dengan SQLite bawaan)
Secara *default*, sistem telah terkonfigurasi dengan database **SQLite** siap pakai yang telah dimigrasikan dan di-seed dengan seluruh data master.

Jalankan perintah berikut:
```bash
# Masuk ke folder snaprint_laravel
cd snaprint_laravel

# Jalankan server lokal Laravel
php artisan serve
```
Buka peramban (browser) di: **[http://localhost:8000](http://localhost:8000)**

### 2. Menggunakan MySQL / MariaDB (Opsional)
Jika Anda ingin menghubungkan sistem ke server database MySQL:
1. Buat database baru di MySQL:
   ```sql
   CREATE DATABASE snaprint_sop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Sesuaikan konfigurasi pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=snaprint_sop
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```

---

## 🌐 Rute & Halaman Sistem

| Endpoint Rute | Nama Rute | Deskripsi |
| :--- | :--- | :--- |
| **`/`** atau **`/sop`** | `sop.index` | **Master SOP Digital Book**: Buku Induk Operasional Lengkap 7 Modul & 26 SOP berbasis data SQL. |
| **`/sop/{slug}`** | `sop.show` | **Tampilan Khusus SOP**: Tampilan detail satu SOP, indikator kinerja, rujukan formulir, dan tombol cetak khusus. |
| **`/forms`** | `forms.index` | **Katalog Dokumen Kontrol**: 30 formulir operasional terkelompok per modul. |
| **`/forms/{slug}`** | `forms.show` | **Tampilan Form & Input Digital**: Menampilkan format baku cetak dan form interaktif pengisian langsung ke SQL. |
| **`/submissions`** | `submissions.index` | **Logbook & Rekapitulasi Arsip**: Memantau seluruh entri pengisian dokumen kontrol harian gerai dengan filter status & outlet. |
| **`/submissions/{id}`** | `submissions.show` | **Detail Dokumen Rekaman**: Berita acara entri data dengan opsi validasi/approval dan cetak fisik. |
| **`/system`** | `system.index` | **Status Dashboard & Basis Data SQL**: Metrik data master, driver database aktif, dan rincian relasi tabel. |
| **`/api/search?q={query}`** | `api.search` | **Live Search API**: Pencarian cepat SOP dan formulir berbasis database. |

---

## 🔒 Hak Cipta & Kerahasiaan
Dokumen dan materi di dalam repositori ini dilindungi oleh Hak Kekayaan Intelektual (HAKI) milik **SNAPRINT DIGITAL PRINTING** yang dikelola dan dioperasikan oleh **PT Duta Global Tech** (*dutaglobaltech.com*). Seluruh hak cipta dilindungi undang-undang.
