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
    redirectToVerification('error', 'Permintaan penolakan tidak valid.');
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    header('Location: ../../public/index.php?status=warning&msg=' . urlencode('Hanya admin yang dapat menolak user.'));
    exit;
}

if (!isset($_SESSION['verification_csrf_token'], $_POST['csrf_token']) || !hash_equals($_SESSION['verification_csrf_token'], $_POST['csrf_token'])) {
    redirectToVerification('error', 'Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirectToVerification('error', 'Data user tidak valid.');
}

$delete = $connect->prepare('DELETE FROM `registrasi` WHERE id = ?');
$delete->bind_param('i', $id);

if (!$delete->execute()) {
    redirectToVerification('error', 'Terjadi kesalahan saat menghapus data pendaftar.');
}

if ($delete->affected_rows !== 1) {
    redirectToVerification('warning', 'Data pendaftar tidak ditemukan atau sudah diproses.');
}

redirectToVerification('success', 'Pendaftaran ditolak dan datanya berhasil dihapus.');
