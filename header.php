<?php
// Her sayfada oturumu başlat ve veritabanına bağlan
session_start();
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TKBD - Sinema ve TV Dünyası</title> 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <link rel="stylesheet" href="css/style.css">
    
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark main-navbar">
  <div class="container-fluid">
    
    <a class="navbar-brand" href="index.php">
        <img src="images/logo.png" style="width: 60px;" alt="TKBD Logo">
    </a>
    
    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
        <i class="bi bi-list fs-5"></i>
        <span class="ms-1 d-none d-lg-inline">Menu</span>
    </button>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarIcerik">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarIcerik">
      
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Ana Sayfa</a>
        </li>
      </ul>
      
      <form action="arama.php" method="GET" class="d-flex flex-grow-1 mx-3" role="search">
        <input class="form-control" type="search" placeholder="Film, Dizi ara..." name="q" required>
        <button class="btn btn-outline-danger" type="submit">Ara</button>
      </form>
      
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> 
        
        <?php if (isset($_SESSION['kullanici_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="profil.php">
                        <i class="bi bi-person-fill me-2"></i> Profilim
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="watchlist.php">
                        <i class="bi bi-bookmark-fill me-2"></i> İzleme Listem
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                    <li>
                        <a class="dropdown-item text-danger" href="admin/index.php">
                            <i class="bi bi-gear-fill me-2"></i> Admin Paneli
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
                <li>
                    <a class="dropdown-item" href="cikis.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                    </a>
                </li>
              </ul>
            </li>

        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="giris.php">Giriş Yap</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-danger text-white" href="kayit.php">Kayıt Ol</a>
            </li>
        <?php endif; ?>
        
      </ul>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item">
            <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Ana Sayfa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="filmler.php"><i class="bi bi-film"></i> Filmler</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="diziler.php"><i class="bi bi-tv"></i> Diziler</a>
        </li>
        <li><hr class="text-secondary"></li> 
        <li class="nav-item">
            <a class="nav-link" href="hakkimizda.php"><i class="bi bi-info-circle"></i> Hakkımızda</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="iletisim.php"><i class="bi bi-envelope"></i> İletişim</a>
        </li>
    </ul>
  </div>
</div>

<div class="container mt-4">