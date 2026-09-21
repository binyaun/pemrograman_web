<?php
if (!isset($connect)) {
    require_once __DIR__ . '/../../config/connect.php';
}

$query = "SELECT * FROM `user`";
$result = $connect->query($query);
?>

<div>
    <h3>Verifikasi User Registrasi</h3>
    <hr>
    <p>Data tabel pengguna dari database <code>dbs3alatih</code> (tabel <code>user</code>).</p>
</div>

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
                            <th>password</th>
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
                                    <td><?php echo htmlspecialchars($row['password'] ?? ''); ?></td>
                                    <td><button class='btn btn-success btn-sm'>Verifikasi</button>
                                    <button class='btn btn-danger btn-sm'>Tolak</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="15" class="text-center text-muted py-4">Data user tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
