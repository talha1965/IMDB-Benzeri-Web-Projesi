<?php
session_start();
// Veritabanı bağlantısını (db.php) bir üst klasörden çek
require '../db.php'; 

// --- Admin Güvenlik Kontrolü ---
// Bu dosya bütün admin sayfalarının başına ekleneceği için
// admin kontrolünü burada tek seferde yapıyoruz.
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
	  // Eğer 'admin' değilse, admin paneline giremesin, ana sayfaya atsın.
	  header("Location: ../index.php");
	  exit; // Kodu burada durdur, devam etmesin.
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Paneli</title> 

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="admin_style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
	<a class="navbar-brand" href="index.php">Admin Paneli</a>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
	  <span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="adminNavbar">
	  <ul class="navbar-nav me-auto">
		<li class="nav-item">
		  <a class="nav-link" href="index.php">Ana Panel</a>
		</li>
		<li class="nav-item">
		  <a class="nav-link" href="film_ekle.php">İçerik Ekle</a>
		</li>
		<li class="nav-item">
		  <a class="nav-link" href="filmleri_yonet.php">İçerikleri Yönet</a>
		</li>
		<li class="nav-item">
		  <a class="nav-link" href="kategorileri_yonet.php">Kategorileri Yönet</a>
		</li>
		<li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Haberler
          </a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="haber_ekle.php">Haber Ekle</a></li>
            <li><a class="dropdown-item" href="haberleri_yonet.php">Haberleri Yönet</a></li>
          </ul>
        </li>
	  </ul>
	  <ul class="navbar-nav ms-auto">
		<li class="nav-item">
			<a class="nav-link text-warning" href="../index.php" target="_blank">
				<i class="bi bi-box-arrow-up-right"></i> Siteye Git
			</a>
		</li>
	  </ul>
	</div>
  </div>
</nav>