<?php
if (!isset($connect)) {
    require_once __DIR__ . '/../../config/connect.php';
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['username']) || (int) ($_SESSION['level'] ?? 0) < 2) {
    echo '<div class="alert alert-warning" role="alert">Hanya admin yang dapat mengelola data barang.</div>';
    return;
}

if (empty($_SESSION['master_data_csrf_token'])) {
    $_SESSION['master_data_csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['master_data_csrf_token'];
$barang_edit = null;
$edit_id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

if ($edit_id) {
    $statement = $connect->prepare('SELECT id, kategori_id, kode_barang, nama_barang, satuan_kecil, keterangan FROM `barang` WHERE id = ?');
    $statement->bind_param('i', $edit_id);
    $statement->execute();
    $barang_edit = $statement->get_result()->fetch_assoc();
}

$kategori_result = $connect->query('SELECT id, kode_kategori, nama_kategori FROM `kategori` ORDER BY nama_kategori ASC');
$kategori = $kategori_result->fetch_all(MYSQLI_ASSOC);
$result = $connect->query(
    'SELECT b.id, b.kode_barang, b.nama_barang, b.satuan_kecil, b.keterangan, k.kode_kategori, k.nama_kategori
     FROM `barang` b
     INNER JOIN `kategori` k ON k.id = b.kategori_id
     ORDER BY b.nama_barang ASC'
);
?>

<div>
    <h3>Data Barang</h3>
    <p class="text-muted">Catat identitas, satuan terkecil, dan kategori setiap barang inventaris.</p>
</div>

<?php if (isset($_GET['status'], $_GET['msg'])): ?>
    <?php $alert_class = $_GET['status'] === 'success' ? 'success' : ($_GET['status'] === 'warning' ? 'warning' : 'danger'); ?>
    <div class="alert alert-<?php echo $alert_class; ?>" role="alert">
        <?php echo htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<?php if (!$kategori): ?>
    <div class="alert alert-info" role="alert">
        Tambahkan kategori terlebih dahulu sebelum mendaftarkan barang.
        <a href="dashboard.php?p=kategori" class="alert-link">Kelola kategori</a>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><?php echo $barang_edit ? 'Ubah Barang' : 'Tambah Barang'; ?></h5>
    </div>
    <div class="card-body">
        <form method="post" action="pages/master_data_action.php" class="row g-3">
            <input type="hidden" name="jenis" value="barang">
            <input type="hidden" name="aksi" value="simpan">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($barang_edit): ?>
                <input type="hidden" name="id" value="<?php echo (int) $barang_edit['id']; ?>">
            <?php endif; ?>
            <div class="col-md-4">
                <label for="kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode_barang" name="kode_barang" maxlength="30" required
                    value="<?php echo htmlspecialchars($barang_edit['kode_barang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-8">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" maxlength="150" required
                    value="<?php echo htmlspecialchars($barang_edit['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori</label>
                <select class="form-select" id="kategori_id" name="kategori_id" required <?php echo !$kategori ? 'disabled' : ''; ?>>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($kategori as $row): ?>
                        <option value="<?php echo (int) $row['id']; ?>" <?php echo (int) ($barang_edit['kategori_id'] ?? 0) === (int) $row['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['kode_kategori'] . ' - ' . $row['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="satuan_kecil" class="form-label">Satuan Kecil</label>
                <input type="text" class="form-control" id="satuan_kecil" name="satuan_kecil" maxlength="30" placeholder="Contoh: pcs, botol, kg" required
                    value="<?php echo htmlspecialchars($barang_edit['satuan_kecil'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-12">
                <label for="keterangan" class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo htmlspecialchars($barang_edit['keterangan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" <?php echo !$kategori ? 'disabled' : ''; ?>><?php echo $barang_edit ? 'Simpan Perubahan' : 'Tambah Barang'; ?></button>
                <?php if ($barang_edit): ?>
                    <a href="dashboard.php?p=barang" class="btn btn-outline-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Barang</h5>
        <span class="badge bg-primary"><?php echo $result->num_rows; ?> Data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan Kecil</th>
                    <th>Keterangan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['kode_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['kode_kategori'] . ' - ' . $row['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan_kecil'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['keterangan'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
                            <td class="text-center text-nowrap">
                                <a href="dashboard.php?p=barang&amp;edit=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                                <form method="post" action="pages/master_data_action.php" class="d-inline" onsubmit="return confirm('Hapus barang ini?');">
                                    <input type="hidden" name="jenis" value="barang">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data barang.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
