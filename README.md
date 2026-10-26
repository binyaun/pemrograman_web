# Sistem Inventory

Aplikasi inventory berbasis PHP dan MySQL.

## Persiapan database

1. Pastikan MySQL berjalan.
2. Import `database/database.sql` melalui phpMyAdmin atau MySQL CLI. Skrip membuat database `dbs3alatih` beserta tabel aplikasi, termasuk `kategori` dan `barang`.
3. Sesuaikan host, username, password, dan nama database di `config/connect.php` bila konfigurasi MySQL berbeda.
4. Jalankan aplikasi melalui web server PHP, lalu masuk menggunakan akun admin.

Jika database aplikasi sudah dibuat sebelum fitur master data tersedia, jalankan `database/migrations/001_create_inventory_tables.sql` pada database `dbs3alatih` melalui phpMyAdmin atau MySQL CLI. Migrasi ini hanya membuat tabel master data dan aman dijalankan ulang.

## Master data

- **Kategori Barang**: kelola kode kategori, nama kategori, dan keterangan.
- **Data Barang**: kelola kode barang, nama barang, satuan kecil, keterangan, dan kategori.
- Kode kategori dan kode barang harus unik. Kategori yang masih digunakan barang tidak dapat dihapus.
- Pengelolaan kedua master data tersedia untuk akun admin melalui menu **Master Data**.