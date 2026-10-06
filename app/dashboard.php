<?php

  session_start();
  require_once __DIR__ . '/../config/connect.php';

  if (!isset($_SESSION['username'])) {
      header('Location: ../public/index.php?status=warning&msg=' . urlencode('Silakan login terlebih dahulu!'));
      exit;
  }

  $get = [
    'user' => $_SESSION['username'] ?? '',
    'level' => $_SESSION['level'] ?? '',
    'nim' => $_SESSION['nim'] ?? '',
    'nama' => $_SESSION['nama'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'foto' => $_SESSION['foto'] ?? ''
  ];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sistem Inventory Perusahaan </title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <style>
  .fakeimg {
    height: 200px;
    background: #aaa;
  }
  </style>

</head>
<body class="container">

<!-- Benner -->
<div class="p-5 bg-white text-black text-center">
  <div class="row">
    <div class="col-sm-2 text-end"> 
      <img src="../assets/images/ks.png" alt="Logo" width="150px" height="150px"> 
    </div>
    
    <div class="col-sm-7">
      <h2>PT Adalah Pokoknya</h2>
      <h5>Jl. Ciptomangun Kusumo no 23</h5>
      <h5>Telp. 085651371755, fax. 087678765454, email. prabowo99@gmail.com</h5>
      <br>
      <h5>yaa mau gimana lagi sod</h5>
    </div>
    
    <div class="col-sm-3">
      <img src="../assets/images/tugu.jpeg" alt="Logo" width="150px" height="150px">
    </div>
  </div>
</div>
<!-- Benner --> 


<!-- Navbar -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link active" href="dashboard.php">Home</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Master Data</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="dashboard.php?p=user_verification">Verifikasi User</a></li>
          <li><a class="dropdown-item" href="#">Supplier</a></li>
          <li><a class="dropdown-item" href="#">Customer</a></li>
          <li><a class="dropdown-item" href="#">User</a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Transaksi</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">View</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">Laporan</a>
      </li>
    </ul>
  </div>
</nav>
<!-- Navbar -->

<div class="container mt-5">
  <div class="row g-4">
    <!-- Side bar -->

    <?php
      $username = $_SESSION['username'] ?? '';
      $level = $_SESSION['level'] ?? 0;
      $profile = $connect->prepare('SELECT username, level, nim, nama_lengkap, email FROM `user` WHERE level = ? AND username = ?');
      $profile->bind_param('is', $level, $username);
      $profile->execute();
      $result = $profile->get_result();

      if ($row = $result->fetch_assoc()) {
          $get['user'] = $row['username'];
          $get['level'] = $row['level'];
          $get['nim'] = $row['nim'];
          $get['nama'] = $row['nama_lengkap'];
          $get['email'] = $row['email'];
          $get['foto'] = $row['foto'] ?? '../assets/images/default.jpg';
      }
    ?>
    <aside class="col-sm-4">
      <h2>Profile User</h2>
      <hr>
      <div> <img src = "<?php echo $get['foto']; ?>" alt="foto" width="300px" height="300px" ></div>
      <hr>
      <div>
        <p>User : <?php echo $get['user']; ?></p>
        <p>Level : <?php echo $get['level']; ?></p>
        <p>NIM : <?php echo $get['nim']; ?></p>
        <p>Nama : <?php echo $get['nama']; ?></p>
        <p>Email : <?php echo $get['email']; ?></p>
      <hr>
      </div>
      <a href="../public/index.php" class="btn btn-danger mt-3">Logout</a>
    </aside>

    <!-- Main content -->
    <div class="col-sm-8">
      <?php 
        $pages_dir = 'pages';
        $p = $_GET['p'] ?? $_GET['P'] ?? '';

        if (!empty($p)) {
          if (is_dir($pages_dir)) {
            $pages = scandir($pages_dir, 0);
            unset($pages[0], $pages[1]);
            if (in_array($p . '.php', $pages)) {
              include($pages_dir . '/' . $p . '.php');
            } elseif (file_exists('../' . $p . '.php')) {
              include('../' . $p . '.php');
            } elseif (file_exists($p . '.php')) {
              include($p . '.php');
            } else {
              echo "<p>Halaman tidak ditemukan</p>";
            }
          } else {
            if (file_exists($p . '.php')) {
              include($p . '.php');
            } elseif (file_exists('../' . $p . '.php')) {
              include('../' . $p . '.php');
            } else {
              echo "<p>Halaman tidak ditemukan</p>";
            }
          }
        } else {
          if (file_exists('home.php')) {
            include('home.php');
          } else {
            echo "<h2>Selamat Datang</h2><p>Selamat Datang di Aplikasi Inventory</p>";
          }
        }
      ?>
    </div>
    <!-- Main content -->
  </div>
</div>

<!-- Footer -->

<div class="mt-5 p-4 bg-dark text-white text-center">
  <p>Footer</p>
</div>
<!-- Footer -->
</body>
</html>