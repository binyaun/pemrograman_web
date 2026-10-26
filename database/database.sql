CREATE DATABASE IF NOT EXISTS dbs3alatih;

USE dbs3alatih;

CREATE TABLE IF NOT EXISTS `user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `level` INT NOT NULL DEFAULT 1,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `nim` VARCHAR(20) NOT NULL UNIQUE,
    `no_hp` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `jenis_kelamin` ENUM('laki-laki', 'perempuan') NOT NULL,
    `alamat` TEXT NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `log` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `aktif` TINYINT(1) NOT NULL DEFAULT 0,
    `hapus` TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `registrasi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `level` INT NOT NULL DEFAULT 1,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `nim` VARCHAR(20) NOT NULL UNIQUE,
    `no_hp` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `jenis_kelamin` ENUM('laki-laki', 'perempuan') NOT NULL,
    `alamat` TEXT NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `log` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `kategori` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode_kategori` VARCHAR(30) NOT NULL UNIQUE,
    `nama_kategori` VARCHAR(100) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `barang` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kategori_id` INT UNSIGNED NOT NULL,
    `kode_barang` VARCHAR(30) NOT NULL UNIQUE,
    `nama_barang` VARCHAR(150) NOT NULL,
    `satuan_kecil` VARCHAR(30) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_barang_kategori_id` (`kategori_id`),
    CONSTRAINT `fk_barang_kategori`
        FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pindahkan pendaftaran tertunda dari skema lama ke tabel registrasi.
INSERT INTO `registrasi` (level, username, nama_lengkap, nim, no_hp, email, jenis_kelamin, alamat, password, foto)
SELECT level, username, nama_lengkap, nim, no_hp, email, jenis_kelamin, alamat, password, foto
FROM `user`
WHERE aktif = 0 AND hapus = 0;

DELETE FROM `user`
WHERE aktif = 0 AND hapus = 0;