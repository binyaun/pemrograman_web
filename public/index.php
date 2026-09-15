<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <title>Website Pertama Saya</title>
</head>

<body>.

    <!-- Container Login -->
    <div class="login">

        <div class="row g-0">
            <div class="col-sm-6">
                <div class="gambar">
                    <img src="../assets/images/user.webp" alt="Gambar Kuda">
                </div>
            </div>
            <div class="col-sm-6">

                <div class="form-login">

                    <h1>Authorize</h1>

                    <form action="../user_validation.php" method="post" id="loginForm">

                        <label for="roleDropdown">
                            <b>Level Akses</b>
                        </label>

                        <div class="dropdown mb-3">
                            <button type="button" id="roleDropdown" class="btn dropdown-toggle role-select-btn" data-bs-toggle="dropdown" aria-expanded="false">Pilih Level Akses</button>
                            <ul class="dropdown-menu w-100" id="roleMenu">
                                <li><a class="dropdown-item" href="#" data-value="1">User</a></li>
                                <li><a class="dropdown-item" href="#" data-value="2">Admin</a></li>
                                <li><a class="dropdown-item" href="#" data-value="3">Super Admin</a></li>
                            </ul>
                            <input type="hidden" name="role" id="roleValue">
                        </div>

                        <label for="uname">
                            <b>Username</b>
                        </label>

                        <input
                            type="text"
                            id="uname"
                            placeholder="Enter Username"
                            name="user"
                            required
                            class="form-control"
                        >

                        <label for="psw">
                            <b>Password</b>
                        </label>

                        <input
                            type="password"
                            id="psw"
                            placeholder="Enter Password"
                            name="psw"
                            required
                            class="form-control"
                        >

                        <div class="form-submit-regis">

                            <div class="row">

                                <div class="col-sm-6">
                                    <button name="submit" type="submit" class="btn btn-primary w-100">
                                        Login
                                    </button>
                                </div>

                                <div class="col-sm-6 text-end">
                                    <button type="button" class="btn btn-warning w-100" onclick="document.getElementById('modalRegistrasi').style.display='flex'">Registrasi</button>
                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <div id="modalRegistrasi" class="modal">

        <div class="modal-content animate">

            <span
                onclick="document.getElementById('modalRegistrasi').style.display='none'"
                class="close"
                title="Close Modal">
                &times;
            </span>

            <h3 class="mb-3">Registrasi Akun</h3>

            <form action="../user_validation.php" method="post">
                <input type="hidden" name="action" value="register">

                <div class="form-section-title">Data Diri</div>

                <div class="mb-3">
                    <label for="reg-nama" class="form-label">
                        <b>Nama Lengkap</b>
                    </label>
                    <input
                        type="text"
                        id="reg-nama"
                        placeholder="Masukkan Nama Lengkap"
                        name="nama"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-nim" class="form-label">
                        <b>NIM</b>
                    </label>
                    <input
                        type="text"
                        id="reg-nim"
                        placeholder="Masukkan NIM"
                        name="nim"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-hp" class="form-label">
                        <b>No. HP</b>
                    </label>
                    <input
                        type="tel"
                        id="reg-hp"
                        placeholder="Contoh: 081234567890"
                        name="no_hp"
                        pattern="[0-9]{10,15}"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-email" class="form-label">
                        <b>Email</b>
                    </label>
                    <input
                        type="email"
                        id="reg-email"
                        placeholder="nama@email.com"
                        name="email"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-gender" class="form-label">
                        <b>Jenis Kelamin</b>
                    </label>
                    <select
                        id="reg-gender"
                        name="gender"
                        required
                        class="form-select"
                    >
                        <option value="" selected disabled>Pilih Jenis Kelamin</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reg-alamat" class="form-label">
                        <b>Alamat</b>
                    </label>
                    <textarea
                        id="reg-alamat"
                        placeholder="Masukkan Alamat Lengkap"
                        name="alamat"
                        rows="2"
                        required
                        class="form-control"
                    ></textarea>
                </div>

                <div class="form-section-title">Data Akun</div>

                <div class="mb-3">
                    <label for="reg-uname" class="form-label">
                        <b>Username</b>
                    </label>
                    <input
                        type="text"
                        id="reg-uname"
                        placeholder="Masukkan Username"
                        name="uname"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-psw" class="form-label">
                        <b>Password</b>
                    </label>
                    <input
                        type="password"
                        id="reg-psw"
                        placeholder="Masukkan Password"
                        name="psw"
                        required
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label for="reg-psw-confirm" class="form-label">
                        <b>Konfirmasi Password</b>
                    </label>
                    <input
                        type="password"
                        id="reg-psw-confirm"
                        placeholder="Ulangi Password"
                        name="psw_confirm"
                        required
                        class="form-control"
                    >
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="document.getElementById('modalRegistrasi').style.display='none'">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Daftar
                    </button>
                </div>

            </form>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tampilkan pesan login gagal jika ada parameter status
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const msg = urlParams.get('msg');

            if (status === 'success') {
                alert('✅ ' + decodeURIComponent(msg));
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'error') {
                alert('❌ Gagal!\n' + decodeURIComponent(msg));
                // Hapus parameter dari URL tanpa reload
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'warning') {
                alert('⚠️ Peringatan!\n' + decodeURIComponent(msg));
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Validasi form login sebelum submit
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function (e) {
                    const roleValue = document.getElementById('roleValue').value;
                    const username = document.getElementById('uname').value.trim();
                    const password = document.getElementById('psw').value;

                    if (!roleValue) {
                        e.preventDefault();
                        alert('⚠️ Peringatan!\nSilakan pilih Level Akses terlebih dahulu!');
                        return false;
                    }
                    if (!username) {
                        e.preventDefault();
                        alert('⚠️ Peringatan!\nUsername tidak boleh kosong!');
                        return false;
                    }
                    if (!password) {
                        e.preventDefault();
                        alert('⚠️ Peringatan!\nPassword tidak boleh kosong!');
                        return false;
                    }
                });
            }

            // Tutup modal jika klik di luar area modal-content
            window.onclick = function (event) {
                const modal = document.getElementById('modalRegistrasi');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };

            document.addEventListener('click', function (e) {
                const item = e.target.closest('#roleMenu .dropdown-item');
                if (!item) return;
                e.preventDefault();
                document.getElementById('roleDropdown').textContent = item.textContent.trim();
                document.getElementById('roleValue').value = item.dataset.value;
            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>