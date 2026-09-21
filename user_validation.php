<?php
    session_start();
    include 'config/connect.php';

    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $nama = trim($_POST['nama'] ?? '');
        $nim = trim($_POST['nim'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $alamat = trim($_POST['alamat'] ?? '');
        $username = trim($_POST['uname'] ?? '');
        $password = $_POST['psw'] ?? '';
        $password_confirm = $_POST['psw_confirm'] ?? '';

        if ($nama === '' || $nim === '' || $no_hp === '' || $email === '' || $gender === '' || $alamat === '' || $username === '' || $password === '') {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Semua field registrasi harus diisi!'));
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10,15}$/', $no_hp)) {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Format email atau nomor HP tidak valid!'));
            exit;
        }

        if (!in_array($gender, ['laki-laki', 'perempuan'], true)) {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Jenis kelamin tidak valid!'));
            exit;
        }

        if ($password !== $password_confirm) {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Konfirmasi password tidak sama!'));
            exit;
        }

        $check = $connect->prepare('SELECT id FROM `user` WHERE username = ? OR nim = ? OR email = ?');
        $check->bind_param('sss', $username, $nim, $email);
        $check->execute();
        $existing = $check->get_result();

        if ($existing->num_rows > 0) {
            header('Location: public/index.php?status=error&msg=' . urlencode('Username, NIM, atau email sudah terdaftar!'));
            exit;
        }

        $default_level = 1;
        $insert = $connect->prepare('INSERT INTO `user` (level, username, nama_lengkap, nim, no_hp, email, jenis_kelamin, alamat, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->bind_param('issssssss', $default_level, $username, $nama, $nim, $no_hp, $email, $gender, $alamat, $password);

        if ($insert->execute()) {
            header('Location: public/index.php?status=success&msg=' . urlencode('Registrasi berhasil. Silakan login!'));
        } else {
            header('Location: public/index.php?status=error&msg=' . urlencode('Registrasi gagal disimpan ke database.'));
        }
        exit;
    }

    if (isset($_POST['submit'])) {
        // Cek apakah semua field sudah diisi
        if (empty($_POST['role']) || empty($_POST['user']) || empty($_POST['psw'])) {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Semua field harus diisi!'));
            exit;
        }

        $level = filter_var($_POST['role'], FILTER_VALIDATE_INT);
        $username = trim($_POST['user']);
        $password = $_POST['psw'];

        if (!in_array($level, [1, 2, 3], true)) {
            header('Location: public/index.php?status=warning&msg=' . urlencode('Level akses tidak valid!'));
            exit;
        }

        $login = $connect->prepare('SELECT id, level, username, password FROM `user` WHERE level = ? AND username = ?');
        $login->bind_param('is', $level, $username);
        $login->execute();
        $result = $login->get_result();
        $row = $result->fetch_assoc();

        // Process the result set
        if ($row && $row['password'] === $password) {
            // data ada - login sukses
            $_SESSION['username'] = $username;
            $_SESSION['level'] = $row['level'];
            $_SESSION['user_id'] = $row['id'];
            header('Location: app/dashboard.php');
            exit;
        } else {
            // data tidak ada - login gagal
            header('Location: public/index.php?status=error&msg=' . urlencode('Username atau password salah!'));
            exit;
        }
    }

    if ($result -> num_rows > 0) {

        $_SESSION['username']= $username;
        $_SESSION['level']= $level;

        echo "<meta http-equiv='refresh' content='0; url=dashboard.php'>";
    } else {
        echo "<meta http-equiv='refresh' content='0; url=public/index.php'>";
        echo "<script>alert('Anda Gagal Login!');</script>";
    }
?>