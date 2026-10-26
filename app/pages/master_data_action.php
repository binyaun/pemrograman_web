<?php
require_once __DIR__ . '/../../config/connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function redirectToMasterData(string $jenis, string $status, string $message): void
{
    header('Location: ../dashboard.php?p=' . urlencode($jenis) . '&status=' . urlencode($status) . '&msg=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$jenis = $_POST['jenis'] ?? '';
if (!in_array($jenis, ['kategori', 'barang'], true)) {
    redirectToMasterData('kategori', 'error', 'Jenis data yang diminta tidak valid.');
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    header('Location: ../../public/index.php?status=warning&msg=' . urlencode('Hanya admin yang dapat mengelola data master.'));
    exit;
}

if (
    !isset($_SESSION['master_data_csrf_token'], $_POST['csrf_token'])
    || !is_string($_POST['csrf_token'])
    || !hash_equals($_SESSION['master_data_csrf_token'], $_POST['csrf_token'])
) {
    redirectToMasterData($jenis, 'error', 'Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
}

try {
    $schema_check = $connect->prepare(
        "SELECT COUNT(*) AS jumlah
         FROM information_schema.tables
         WHERE table_schema = DATABASE() AND table_name IN ('kategori', 'barang')"
    );
    $schema_check->execute();
    if ((int) $schema_check->get_result()->fetch_assoc()['jumlah'] !== 2) {
        redirectToMasterData(
            $jenis,
            'error',
            'Tabel master data belum tersedia. Jalankan database/migrations/001_create_inventory_tables.sql pada database aplikasi.'
        );
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Gagal memeriksa skema master data: ' . $exception->getMessage());
    redirectToMasterData($jenis, 'error', 'Database tidak dapat memeriksa tabel master data. Periksa konfigurasi database.');
}

$aksi = $_POST['aksi'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$table = $jenis === 'kategori' ? '`kategori`' : '`barang`';

if ($aksi === 'hapus') {
    if (!$id || $id < 1) {
        redirectToMasterData($jenis, 'error', 'Data yang akan dihapus tidak valid.');
    }

    if ($jenis === 'kategori') {
        $usage = $connect->prepare('SELECT COUNT(*) AS total FROM `barang` WHERE kategori_id = ?');
        $usage->bind_param('i', $id);
        $usage->execute();
        if ((int) $usage->get_result()->fetch_assoc()['total'] > 0) {
            redirectToMasterData($jenis, 'warning', 'Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
        }
    }

    try {
        $statement = $connect->prepare("DELETE FROM {$table} WHERE id = ?");
        $statement->bind_param('i', $id);
        $statement->execute();
    } catch (mysqli_sql_exception $exception) {
        error_log('Gagal menghapus ' . $jenis . ': ' . $exception->getMessage());
        redirectToMasterData($jenis, 'error', 'Terjadi kesalahan saat menghapus data.');
    }

    if ($statement->affected_rows !== 1) {
        redirectToMasterData($jenis, 'warning', 'Data tidak ditemukan atau sudah dihapus.');
    }

    redirectToMasterData($jenis, 'success', ucfirst($jenis) . ' berhasil dihapus.');
}

if ($aksi !== 'simpan') {
    redirectToMasterData($jenis, 'error', 'Aksi yang diminta tidak valid.');
}

if (array_key_exists('id', $_POST)) {
    if (!$id || $id < 1) {
        redirectToMasterData($jenis, 'error', 'Data yang akan diperbarui tidak valid.');
    }

    $existing = $connect->prepare("SELECT id FROM {$table} WHERE id = ?");
    $existing->bind_param('i', $id);
    $existing->execute();
    if (!$existing->get_result()->fetch_assoc()) {
        redirectToMasterData($jenis, 'warning', 'Data yang akan diperbarui tidak ditemukan.');
    }
}

try {
    if ($jenis === 'kategori') {
        $kode = is_string($_POST['kode_kategori'] ?? null) ? trim($_POST['kode_kategori']) : '';
        $nama = is_string($_POST['nama_kategori'] ?? null) ? trim($_POST['nama_kategori']) : '';
        $keterangan = is_string($_POST['keterangan'] ?? null) ? trim($_POST['keterangan']) : '';

        if ($kode === '' || $nama === '' || mb_strlen($kode, 'UTF-8') > 30 || mb_strlen($nama, 'UTF-8') > 100) {
            redirectToMasterData($jenis, 'warning', 'Kode kategori dan nama kategori wajib diisi sesuai batas karakter.');
        }

        if ($id && $id > 0) {
            $statement = $connect->prepare('UPDATE `kategori` SET kode_kategori = ?, nama_kategori = ?, keterangan = ? WHERE id = ?');
            $statement->bind_param('sssi', $kode, $nama, $keterangan, $id);
            $statement->execute();
            redirectToMasterData($jenis, 'success', 'Kategori berhasil diperbarui.');
        }

        $statement = $connect->prepare('INSERT INTO `kategori` (kode_kategori, nama_kategori, keterangan) VALUES (?, ?, ?)');
        $statement->bind_param('sss', $kode, $nama, $keterangan);
        $statement->execute();
        redirectToMasterData($jenis, 'success', 'Kategori berhasil ditambahkan.');
    }

    $kode = is_string($_POST['kode_barang'] ?? null) ? trim($_POST['kode_barang']) : '';
    $nama = is_string($_POST['nama_barang'] ?? null) ? trim($_POST['nama_barang']) : '';
    $satuan = is_string($_POST['satuan_kecil'] ?? null) ? trim($_POST['satuan_kecil']) : '';
    $keterangan = is_string($_POST['keterangan'] ?? null) ? trim($_POST['keterangan']) : '';
    $kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_VALIDATE_INT);

    if ($kode === '' || $nama === '' || $satuan === '' || mb_strlen($kode, 'UTF-8') > 30 || mb_strlen($nama, 'UTF-8') > 150 || mb_strlen($satuan, 'UTF-8') > 30) {
        redirectToMasterData($jenis, 'warning', 'Kode, nama, dan satuan kecil wajib diisi sesuai batas karakter.');
    }

    if (!$kategori_id || $kategori_id < 1) {
        redirectToMasterData($jenis, 'warning', 'Pilih kategori barang yang valid.');
    }

    $category_check = $connect->prepare('SELECT id FROM `kategori` WHERE id = ?');
    $category_check->bind_param('i', $kategori_id);
    $category_check->execute();
    if (!$category_check->get_result()->fetch_assoc()) {
        redirectToMasterData($jenis, 'warning', 'Kategori yang dipilih tidak ditemukan.');
    }

    if ($id && $id > 0) {
        $statement = $connect->prepare('UPDATE `barang` SET kategori_id = ?, kode_barang = ?, nama_barang = ?, satuan_kecil = ?, keterangan = ? WHERE id = ?');
        $statement->bind_param('issssi', $kategori_id, $kode, $nama, $satuan, $keterangan, $id);
        $statement->execute();
        redirectToMasterData($jenis, 'success', 'Data barang berhasil diperbarui.');
    }

    $statement = $connect->prepare('INSERT INTO `barang` (kategori_id, kode_barang, nama_barang, satuan_kecil, keterangan) VALUES (?, ?, ?, ?, ?)');
    $statement->bind_param('issss', $kategori_id, $kode, $nama, $satuan, $keterangan);
    $statement->execute();
    redirectToMasterData($jenis, 'success', 'Data barang berhasil ditambahkan.');
} catch (mysqli_sql_exception $exception) {
    error_log('Gagal menyimpan ' . $jenis . ': ' . $exception->getMessage());
    if ($exception->getCode() === 1062) {
        redirectToMasterData($jenis, 'warning', $jenis === 'kategori' ? 'Kode kategori sudah digunakan.' : 'Kode barang sudah digunakan.');
    }
    redirectToMasterData($jenis, 'error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
}
