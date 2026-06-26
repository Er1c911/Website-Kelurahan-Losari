# TODO

- [ ] Mengosongkan tampilan bagian “Informasi Desa” (Profil Desa, Layanan Publik, Potensi & Kegiatan) di halaman user (`/`) dan admin (`/admin/dashboard`).
  - [x] Update `KelolaInformasiController@index` agar tidak mengirim konten sections.
  - [x] Update `AdminKelolaInformasiController@index` agar tidak membuat/menampilkan sections (atau mengirim sections kosong).
  - [x] Update Blade `welcome_user.blade.php` agar bagian loop informasi desa tidak tampil / tetap tampil namun tanpa isi.
  - [x] Update Blade `admin/dashboard.blade.php` agar editor konten tidak tampil (atau tabel/daftar kosong).

- [x] Jalankan pemeriksaan manual dengan membuka `/` dan `/admin/dashboard`.
- [ ] Pastikan tombol “Kelola Informasi Desa” mengarah ke halaman kelola yang benar.

