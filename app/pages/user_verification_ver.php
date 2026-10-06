<?php
require_once __DIR__ . '/../../config/connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function redirectToVerification(string $status, string $message): void
{
    header('Location: ../dashboard.php?p=user_verification&status=' . urlencode($status) . '&msg=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToVerification('error', 'Permintaan verifikasi tidak valid.');
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    header('Location: ../../public/index.php?status=warning&msg=' . urlencode('Hanya admin yang dapat memverifikasi user.'));
    exit;
}

if (!isset($_SESSION['verification_csrf_token'], $_POST['csrf_token']) || !hash_equals($_SESSION['verification_csrf_token'], $_POST['csrf_token'])) {
    redirectToVerification('error', 'Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirectToVerification('error', 'Data pendaftar tidak valid.');
}

try {
    $connect->begin_transaction();
    $transaction_started = true;

    $select = $connect->prepare('SELECT level, username, nama_lengkap, nim, no_hp, email, jenis_kelamin, alamat, password, foto FROM `registrasi` WHERE id = ? FOR UPDATE');
    $select->bind_param('i', $id);
    $select->execute();
    $registrasi = $select->get_result()->fetch_assoc();

    if (!$registrasi) {
        $connect->rollback();
        redirectToVerification('warning', 'Data pendaftar tidak ditemukan atau sudah diproses.');
    }

    $level = (int) $registrasi['level'];
    $username = $registrasi['username'];
    $nama_lengkap = $registrasi['nama_lengkap'];
    $nim = $registrasi['nim'];
    $no_hp = $registrasi['no_hp'];
    $email = $registrasi['email'];
    $jenis_kelamin = $registrasi['jenis_kelamin'];
    $alamat = $registrasi['alamat'];
    $password = $registrasi['password'];
    $foto = $registrasi['foto'] ?? '';

    $insert = $connect->prepare(
        'INSERT INTO `user` (level, username, nama_lengkap, nim, no_hp, email, jenis_kelamin, alamat, password, foto, aktif, hapus)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)'
    );
    $insert->bind_param(
        'isssssssss',
        $level,
        $username,
        $nama_lengkap,
        $nim,
        $no_hp,
        $email,
        $jenis_kelamin,
        $alamat,
        $password,
        $foto
    );
    $insert->execute();

    $delete = $connect->prepare('DELETE FROM `registrasi` WHERE id = ?');
    $delete->bind_param('i', $id);
    $delete->execute();

    if ($delete->affected_rows !== 1) {
        throw new RuntimeException('Pendaftar tidak berhasil dihapus setelah dipindahkan.');
    }

    $connect->commit();
} catch (mysqli_sql_exception | RuntimeException $exception) {
    if (!empty($transaction_started)) {
        $connect->rollback();
    }
    error_log('Gagal memverifikasi pendaftar: ' . $exception->getMessage());
    redirectToVerification('error', 'Terjadi kesalahan saat memindahkan data pendaftar ke tabel user.');
}

redirectToVerification('success', 'Pendaftar berhasil diverifikasi dan dipindahkan ke tabel user. Akun sekarang dapat login.');
