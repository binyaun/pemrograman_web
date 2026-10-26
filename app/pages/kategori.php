<?php
if (!isset($connect)) {
    require_once __DIR__ . '/../../config/connect.php';
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    echo '<div class="alert alert-warning" role="alert">Hanya admin yang dapat mengelola kategori barang.</div>';
    return;
}

if (empty($_SESSION['master_data_csrf_token'])) {
    $_SESSION['master_data_csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['master_data_csrf_token'];
$kategori_edit = null;
$edit_id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

if ($edit_id) {
    $statement = $connect->prepare('SELECT id, kode_kategori, nama_kategori, keterangan FROM `kategori` WHERE id = ?');
    $statement->bind_param('i', $edit_id);
    $statement->execute();
    $kategori_edit = $statement->get_result()->fetch_assoc();
}

$result = $connect->query(
    'SELECT k.id, k.kode_kategori, k.nama_kategori, k.keterangan, COUNT(b.id) AS jumlah_barang
     FROM `kategori` k
     LEFT JOIN `barang` b ON b.kategori_id = k.id
     GROUP BY k.id, k.kode_kategori, k.nama_kategori, k.keterangan
     ORDER BY k.nama_kategori ASC'
);
?>

<div>
    <h3>Kategori Barang</h3>
    <p class="text-muted">Kelola pengelompokan barang untuk menjaga data inventaris tetap terstruktur.</p>
</div>

<?php if (isset($_GET['status'], $_GET['msg'])): ?>
    <?php $alert_class = $_GET['status'] === 'success' ? 'success' : ($_GET['status'] === 'warning' ? 'warning' : 'danger'); ?>
    <div class="alert alert-<?php echo $alert_class; ?>" role="alert">
        <?php echo htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><?php echo $kategori_edit ? 'Ubah Kategori' : 'Tambah Kategori'; ?></h5>
    </div>
    <div class="card-body">
        <form method="post" action="pages/master_data_action.php" class="row g-3">
            <input type="hidden" name="jenis" value="kategori">
            <input type="hidden" name="aksi" value="simpan">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($kategori_edit): ?>
                <input type="hidden" name="id" value="<?php echo (int) $kategori_edit['id']; ?>">
            <?php endif; ?>
            <div class="col-md-4">
                <label for="kode_kategori" class="form-label">Kode Kategori</label>
                <input type="text" class="form-control" id="kode_kategori" name="kode_kategori" maxlength="30" required
                    value="<?php echo htmlspecialchars($kategori_edit['kode_kategori'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-8">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" maxlength="100" required
                    value="<?php echo htmlspecialchars($kategori_edit['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-12">
                <label for="keterangan" class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo htmlspecialchars($kategori_edit['keterangan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><?php echo $kategori_edit ? 'Simpan Perubahan' : 'Tambah Kategori'; ?></button>
                <?php if ($kategori_edit): ?>
                    <a href="dashboard.php?p=kategori" class="btn btn-outline-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kategori</h5>
        <span class="badge bg-primary"><?php echo $result->num_rows; ?> Data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-center">Jumlah Barang</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['kode_kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['keterangan'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
                            <td class="text-center"><?php echo (int) $row['jumlah_barang']; ?></td>
                            <td class="text-center text-nowrap">
                                <a href="dashboard.php?p=kategori&amp;edit=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                                <form method="post" action="pages/master_data_action.php" class="d-inline" onsubmit="return confirm('Hapus kategori ini? Kategori yang masih digunakan barang tidak dapat dihapus.');">
                                    <input type="hidden" name="jenis" value="kategori">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kategori barang.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
