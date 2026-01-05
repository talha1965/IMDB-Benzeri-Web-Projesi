<?php
require 'admin_header.php'; 
?>

<div class="container mt-5">
    <div class="admin-container">
        
        <h2 class="mb-3">Hoş Geldin, <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>!</h2>
        <p>Admin paneline hoş geldin. Lütfen yapmak istediğin işlemi seç.</p>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <i class="bi bi-plus-circle-fill me-2"></i> İçerik Ekle
                        </h5>
                        <p class="card-text flex-grow-1">Sisteme yeni bir film veya dizi (içerik) eklemek için bu bölümü kullan.</p>
                        <a href="film_ekle.php" class="btn btn-success w-100 mt-3">İçerik Ekleme Sayfasına Git</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <i class="bi bi-pencil-square me-2"></i> İçerikleri Yönet
                        </h5>
                        <p class="card-text flex-grow-1">Mevcut içerikleri listele, düzenle veya sistemden sil.</p>
                        <a href="filmleri_yonet.php" class="btn btn-danger w-100 mt-3">İçerikleri Yönetme Sayfasına Git</a>
                    </div>
                </div>
            </div>
        </div> 

        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <i class="bi bi-newspaper me-2"></i> Haber Ekle
                        </h5>
                        <p class="card-text flex-grow-1">Sisteme yeni bir haber, duyuru veya gelişme eklemek için burayı kullan.</p>
                        <a href="haber_ekle.php" class="btn btn-success w-100 mt-3">Haber Ekleme Sayfasına Git</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <i class="bi bi-list-columns-reverse me-2"></i> Haberleri Yönet
                        </h5>
                        <p class="card-text flex-grow-1">Mevcut haberleri listele ve yayından kaldır (sil).</p>
                        <a href="haberleri_yonet.php" class="btn btn-danger w-100 mt-3">Haberleri Yönetme Sayfasına Git</a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-4">
            <div class="col-md-8"> <div class="card dashboard-card h-100 text-center border-secondary">
                    <div class="card-body">
                        <h5 class="card-title text-white">
                            <i class="bi bi-tags-fill me-2 text-primary"></i> Kategorileri Yönet
                        </h5>
                        <p class="card-text">Ana sayfadaki film/dizi listelerini (Örn: En İyiler, Popülerler) buradan düzenle.</p>
                        <a href="kategorileri_yonet.php" class="btn btn-primary w-50">Kategorileri Yönet</a>
                    </div>
                </div>
            </div>
        </div>

        </div> 

    </div>
</div>

<?php 
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>'; 
?>