<?php
if (!isset($connect)) {
    require_once __DIR__ . '/../../config/connect.php';
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    echo '<div class="alert alert-warning" role="alert">Hanya admin yang dapat melihat dan memproses verifikasi user.</div>';
    return;
}

$csrf_token = $_SESSION['verification_csrf_token'] ?? '';
if ($csrf_token === '') {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['verification_csrf_token'] = $csrf_token;
}

$query = "SELECT * FROM `registrasi` ORDER BY log ASC, id ASC";
$result = $connect->query($query);
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$app_base = rtrim(dirname($script_name), '/');
if (basename($script_name) !== 'dashboard.php') {
    $app_base .= '/app';
}
?>

<div>
    <h3>Verifikasi User Registrasi</h3>
    <hr>
    <p>Data pendaftar yang menunggu persetujuan admin dari tabel <code>registrasi</code>.</p>
</div>

<?php if (isset($_GET['status'], $_GET['msg'])): ?>
    <?php
        $alert_class = $_GET['status'] === 'success' ? 'success' : ($_GET['status'] === 'warning' ? 'warning' : 'danger');
    ?>
    <div class="alert alert-<?php echo $alert_class; ?>" role="alert">
        <?php echo htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="container-fluid px-0 mt-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar User</h5>
            <span class="badge bg-primary"><?php echo $result ? $result->num_rows : 0; ?> Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0 align-middle text-nowrap">
                    <thead class="table-dark text-center align-middle">
                        <tr>
                            <th>foto</th>
                            <th>id</th>
                            <th>level</th>
                            <th>username</th>
                            <th>nama_lengkap</th>
                            <th>nim</th>
                            <th>no_hp</th>
                            <th>email</th>
                            <th>jenis_kelamin</th>
                            <th>alamat</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $foto_file = !empty($row['foto']) ? $row['foto'] : 'default.jpg';
                                    $foto_path = (file_exists(__DIR__ . '/../../assets/images/' . $foto_file))
                                        ? '../assets/images/' . $foto_file
                                        : '../assets/images/default.jpg';
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto User" width="45" height="45" class="rounded-circle border" style="object-fit: cover;">
                                    </td>
                                    <td class="text-center fw-bold"><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($row['level'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['nim'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_hp'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['jenis_kelamin'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['alamat'] ?? ''); ?></td>
                                    <td class="text-center">
                                        <form method="post" action="<?php echo htmlspecialchars($app_base . '/pages/user_verification_ver.php', ENT_QUOTES, 'UTF-8'); ?>" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button class="btn btn-success btn-sm" type="submit">Verifikasi</button>
                                        </form>
                                        <form method="post" action="<?php echo htmlspecialchars($app_base . '/pages/user_verification_tolak.php', ENT_QUOTES, 'UTF-8'); ?>" class="d-inline" onsubmit="return confirm('Yakin ingin menolak dan menghapus data user ini?');">
                                            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button class="btn btn-danger btn-sm" type="submit">Tolak</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Tidak ada pendaftar yang menunggu verifikasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
