<?php
session_start();
include '../baglanti.php';

// Güvenlik kontrolü
if (!isset($_SESSION['admin_giris'])) { header("Location: login.php"); exit; }

// --- GÜNCELLEME İŞLEMİ ---
if (isset($_POST['ayar_kaydet'])) {
    
    // Hakkımızda ve Başlık güncellemeleri
    $kaydet = $db->prepare("UPDATE ayarlar SET 
        site_baslik = :baslik,
        hakkimizda = :hakkimizda,
        iletisim_email = :email,
        anasayfa_siralama = :siralama,
        anasayfa_yon = :yon
        WHERE id = 1");
    
    $update = $kaydet->execute([
        'baslik' => $_POST['site_baslik'],
        'hakkimizda' => $_POST['hakkimizda'],
        'email' => $_POST['iletisim_email'],
        'siralama' => $_POST['anasayfa_siralama'],
        'yon' => $_POST['anasayfa_yon']
    ]);

    if ($update) {
        $mesaj = '<div class="alert alert-success">Ayarlar başarıyla güncellendi!</div>';
    } else {
        $mesaj = '<div class="alert alert-danger">Hata oluştu!</div>';
    }
}

// Mevcut ayarları çek
$ayarsor = $db->prepare("SELECT * FROM ayarlar WHERE id=1");
$ayarsor->execute();
$ayarcek = $ayarsor->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Site Ayarları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-dark text-white">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fa-solid fa-gear text-danger"></i> Site Genel Ayarları</h2>
                <a href="index.php" class="btn btn-outline-light btn-sm">Panele Dön</a>
            </div>

            <?php if(isset($mesaj)) echo $mesaj; ?>

            <form action="" method="POST">
                
                <div class="card bg-secondary border-0 mb-4">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="fa-solid fa-list-ol me-2"></i> Anasayfa Vitrin Sıralaması
                    </div>
                    <div class="card-body bg-dark border border-secondary text-white">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-warning">Sıralama Kriteri</label>
                                <select name="anasayfa_siralama" class="form-select bg-secondary text-white border-secondary">
                                    <option value="id" <?php echo ($ayarcek['anasayfa_siralama'] == 'id') ? 'selected' : ''; ?>>Eklenme Sırasına Göre (ID)</option>
                                    <option value="cikis_yili" <?php echo ($ayarcek['anasayfa_siralama'] == 'cikis_yili') ? 'selected' : ''; ?>>Yapım Yılına Göre</option>
                                    <option value="baslik" <?php echo ($ayarcek['anasayfa_siralama'] == 'baslik') ? 'selected' : ''; ?>>Film Adına Göre (A-Z)</option>
                                    <option value="ortalama_puan" <?php echo ($ayarcek['anasayfa_siralama'] == 'ortalama_puan') ? 'selected' : ''; ?>>IMDB Puanına Göre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-warning">Sıralama Yönü</label>
                                <select name="anasayfa_yon" class="form-select bg-secondary text-white border-secondary">
                                    <option value="DESC" <?php echo ($ayarcek['anasayfa_yon'] == 'DESC') ? 'selected' : ''; ?>>Azalan (Yeniden Eskiye / Yüksekten Düşüğe)</option>
                                    <option value="ASC" <?php echo ($ayarcek['anasayfa_yon'] == 'ASC') ? 'selected' : ''; ?>>Artan (Eskiden Yeniye / Düşükten Yükseğe)</option>
                                </select>
                            </div>
                        </div>
                        <small class="text-muted">* Bu ayar anasayfadaki "En Yeni İçerikler" bölümünün sıralamasını değiştirir.</small>
                    </div>
                </div>

                <div class="card bg-dark border-secondary">
                    <div class="card-header border-secondary fw-bold">
                        <i class="fa-solid fa-info-circle me-2"></i> Genel Bilgiler
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Site Başlığı</label>
                            <input type="text" name="site_baslik" class="form-control bg-secondary text-white border-0" value="<?php echo $ayarcek['site_baslik']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Hakkımızda Yazısı</label>
                            <textarea name="hakkimizda" class="form-control bg-secondary text-white border-0" rows="5"><?php echo $ayarcek['hakkimizda']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label>İletişim E-mail</label>
                            <input type="email" name="iletisim_email" class="form-control bg-secondary text-white border-0" value="<?php echo $ayarcek['iletisim_email']; ?>">
                        </div>

                        <button type="submit" name="ayar_kaydet" class="btn btn-success w-100 btn-lg">Ayarları Kaydet</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>