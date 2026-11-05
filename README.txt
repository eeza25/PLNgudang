SISTEM INFORMASI PENGELOLAAN STOK DAN PERSEDIAAN BARANG
PT PLN (PERSERO) ULP TANJUNG PURA

VERSI DISEMPURNAKAN
- Login aman dengan password_hash/password_verify dan session regeneration
- Role admin dan petugas
- Dashboard statistik: total barang, stok, masuk, keluar, transaksi hari ini, stok minimum
- Master data barang: tambah, edit, pencarian, arsip (soft delete)
- Transaksi masuk/keluar memakai database transaction + row locking
- Barang keluar otomatis ditolak jika stok tidak mencukupi
- Validasi tanggal agar tidak di masa depan
- Riwayat transaksi sampai 50 data
- Laporan filter periode dan jenis
- Cetak laporan / Save as PDF melalui dialog print browser
- Ekspor laporan CSV
- CSRF protection pada form POST
- Tampilan responsif untuk laptop dan HP
- Pesan error duplikasi username/kode barang
- File setup_admin.php untuk membuat/reset akun awal

TEKNOLOGI
PHP 8.x (disarankan 8.1+), MySQL/MariaDB, HTML5, CSS3, XAMPP.

INSTALASI XAMPP
1. Install XAMPP.
2. Aktifkan Apache dan MySQL.
3. Salin folder "pln_inventory_ulp_tanjung_pura" ke C:\xampp\htdocs\
4. Buka http://localhost/phpmyadmin
5. Import database.sql.
6. Buka http://localhost/pln_inventory_ulp_tanjung_pura/setup_admin.php
7. Login: http://localhost/pln_inventory_ulp_tanjung_pura/login.php
8. Akun awal: admin / admin123
9. Setelah login, buka menu Pengguna dan buat akun petugas sesuai kebutuhan.
10. Untuk keamanan, hapus atau rename setup_admin.php setelah akun admin berhasil dibuat.

CARA MENGGUNAKAN
1. Tambahkan master barang pada Data Barang.
2. Gunakan Barang Masuk untuk menambah stok dan mencatat riwayat.
3. Gunakan Barang Keluar untuk mengurangi stok. Sistem mencegah stok menjadi minus.
4. Pantau kartu Stok Minimum pada Dashboard.
5. Gunakan Laporan untuk filter tanggal/jenis.
6. Tombol "Cetak / Simpan PDF" membuka print browser; pilih "Save as PDF" untuk menyimpan PDF.
7. Tombol "Ekspor CSV" menghasilkan file yang dapat dibuka di Excel.

CATATAN
Sistem ini merupakan template akademik/proyek. Sesuaikan kode material, satuan, lokasi, SOP, format dokumen, hak akses, dan kebutuhan keamanan dengan aturan resmi sebelum dipakai operasional.
