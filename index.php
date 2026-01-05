<?php
// Header dosyasının HTML başlangıcını (<html>, <head>, <body>) ve navbar'ı içerdiğini varsayıyoruz.
require_once 'header.php';

try {
    // A) Slider (Son 5 Film)
    $slider_sorgu = $db->prepare("SELECT * FROM filmler ORDER BY eklenme_tarihi DESC LIMIT 5");
    $slider_sorgu->execute();
    $slider_filmleri = $slider_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // B) En Yeni İçerikler (18 Adet)
    // --- ÖNCE AYARLARI ÇEKELİM ---
    $ayarSor = $db->prepare("SELECT anasayfa_siralama, anasayfa_yon FROM ayarlar WHERE id=1");
    $ayarSor->execute();
    $siteAyar = $ayarSor->fetch(PDO::FETCH_ASSOC);

    // Ayarları değişkenlere atayalım (Güvenlik için varsayılan değerler koyalım)
    $siralama_kriteri = !empty($siteAyar['anasayfa_siralama']) ? $siteAyar['anasayfa_siralama'] : 'id';
    $siralama_yonu    = !empty($siteAyar['anasayfa_yon']) ? $siteAyar['anasayfa_yon'] : 'DESC';

    // B) Dinamik Vitrin İçerikleri (18 Adet)
    // NOT: Puan sıralaması özel bir durumdur, o yüzden if ile kontrol ediyoruz.
    if ($siralama_kriteri == 'ortalama_puan') {
        // Puana göre sıralama sorgusu
        $yeni_icerik_sorgu = $db->prepare("
            SELECT filmler.*, AVG(IFNULL(yorumlar.puan, 0)) AS ortalama_puan
            FROM filmler
            LEFT JOIN yorumlar ON filmler.id = yorumlar.film_id
            GROUP BY filmler.id
            ORDER BY ortalama_puan $siralama_yonu
            LIMIT 18
        ");
    } else {
        // Diğer kriterlere (Yıl, ID, Başlık) göre sıralama sorgusu
        // $siralama_kriteri değişkenini doğrudan sorguya yazıyoruz
        $yeni_icerik_sorgu = $db->prepare("
            SELECT filmler.*, AVG(IFNULL(yorumlar.puan, 0)) AS ortalama_puan
            FROM filmler
            LEFT JOIN yorumlar ON filmler.id = yorumlar.film_id
            GROUP BY filmler.id
            ORDER BY filmler.$siralama_kriteri $siralama_yonu
            LIMIT 18
        ");
    }
    
    $yeni_icerik_sorgu->execute();
    $yeni_icerikler = $yeni_icerik_sorgu->fetchAll(PDO::FETCH_ASSOC);
    $yeni_icerik_sorgu = $db->prepare("
        SELECT filmler.*, AVG(IFNULL(yorumlar.puan, 0)) AS ortalama_puan
        FROM filmler
        LEFT JOIN yorumlar ON filmler.id = yorumlar.film_id
        GROUP BY filmler.id
        ORDER BY filmler.cikis_yili DESC LIMIT 18
    ");
    $yeni_icerik_sorgu->execute();
    $yeni_icerikler = $yeni_icerik_sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    // C) Kategoriler
    $kategoriler_sorgu = $db->prepare("SELECT * FROM kategoriler ORDER BY sira ASC");
    $kategoriler_sorgu->execute();
    $kategoriler = $kategoriler_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // D) Haberler (Son 3 Haber)
    $haber_sorgu = $db->prepare("SELECT * FROM haberler ORDER BY tarih DESC LIMIT 3");
    $haber_sorgu->execute();
    $haberler = $haber_sorgu->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Veri hatası: ". $e->getMessage());
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="style.css">

<div class="container mt-4"> <div class="promo-section rounded-3 shadow-lg mb-5" style="position: relative; overflow: hidden;">
        <div class="row g-0">
            <div class="col-lg-7">
                <div id="promoSlider" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php if (empty($slider_filmleri)): ?>
                            <div class="carousel-item active promo-slider-item">
                                <img src="https://via.placeholder.com/900x500.png?text=Film+Ekleyin" class="promo-slider-image">
                            </div>
                        <?php else: ?>
                            <?php foreach ($slider_filmleri as $index => $film): ?>
                                <div class="carousel-item <?php echo ($index == 0) ? 'active' : ''; ?> promo-slider-item">
                                    <img src="<?php 
                                        echo !empty($film['slider_gorsel_url']) 
                                            ? htmlspecialchars($film['slider_gorsel_url']) 
                                            : (!empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/900x500.png?text=Gorsel+Yok'); 
                                    ?>" class="promo-slider-image" alt="<?php echo htmlspecialchars($film['baslik']); ?>">
                                    
                                    <div class="promo-caption">
                                        <div class="promo-poster-overlay">
                                            <img src="<?php echo !empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/120x180.png?text=Afiş'; ?>">
                                            <a href="watchlist_islem.php?film_id=<?php echo $film['id']; ?>&action=ekle" class="plus-icon"><i class="fa-solid fa-plus"></i></a>
                                        </div>
                                        
                                        <div class="promo-trailer-info">
                                            <a href="detay.php?id=<?php echo $film['id']; ?>" class="play-button"><i class="fa-solid fa-circle-play"></i></a>
                                            <div class="promo-text">
                                                <h5 class="mb-0"><?php echo htmlspecialchars($film['baslik']); ?></h5>
                                                <p class="mb-0" style="color: #adb5bd;">Detayları gör</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <button class="slider-arrow arrow-left" type="button" data-bs-target="#promoSlider" data-bs-slide="prev">
                        <i class="fa-solid fa-angle-left"></i>
                    </button>
                    <button class="slider-arrow arrow-right" type="button" data-bs-target="#promoSlider" data-bs-slide="next">
                        <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="promo-news-list p-3">
                    <h4 class="promo-news-title mb-3">Haberler</h4>
                    <ul class="list-unstyled">
                        <?php if (empty($haberler)): ?>
                            <li class="text-muted">Henüz haber eklenmemiş.</li>
                        <?php else: ?>
                            <?php foreach ($haberler as $haber): ?>
                                <li class="mb-3">
                                    <a href="haber_detay.php?id=<?php echo $haber['id']; ?>" class="promo-news-item d-flex align-items-center text-decoration-none">
                                        <img src="<?php echo !empty($haber['resim_url']) ? htmlspecialchars($haber['resim_url']) : 'https://via.placeholder.com/120x70.png?text=Haber'; ?>" 
                                             class="rounded-2 me-3" alt="Haber Görseli">
                                        <div class="news-content">
                                            <small class="text-light news-title"><?php echo htmlspecialchars($haber['baslik']); ?></small>
                                            <small class="d-block news-source" style="color: #adb5bd;">
                                                <?php echo date('d.m.Y', strtotime($haber['tarih'])); ?>
                                            </small>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="haberler.php" class="browse-trailers-link">Tüm Haberler <i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="category-row mt-5">
        <h3 class="category-title">En Yeni İçerikler</h3>
        <div class="swiper horizontal-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($yeni_icerikler as $film): ?>
                    <div class="swiper-slide">
                        <div class="scroll-card">
                            <a href="detay.php?id=<?php echo $film['id']; ?>">
                                <img src="<?php echo !empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/200x300.png?text=Afis'; ?>" 
                                     alt="<?php echo htmlspecialchars($film['baslik']); ?>">
                                <div class="scroll-card-body">
                                    <strong><?php echo htmlspecialchars($film['baslik']); ?></strong>
                                    <?php if (isset($film['ortalama_puan']) && $film['ortalama_puan'] > 0): ?>
                                        <div class="scroll-card-rating"><i class="fa-solid fa-star"></i> <?php echo number_format($film['ortalama_puan'], 1); ?></div>
                                    <?php else: ?>
                                        <span><?php echo $film['cikis_yili']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    <?php foreach ($kategoriler as $kategori): 
        $sql_taban = "SELECT filmler.*, AVG(IFNULL(yorumlar.puan, 0)) AS ortalama_puan FROM filmler JOIN film_kategori_iliskisi ON filmler.id = film_kategori_iliskisi.film_id LEFT JOIN yorumlar ON filmler.id = yorumlar.film_id WHERE film_kategori_iliskisi.kategori_id = ?";
        $sql_siralama = "GROUP BY filmler.id"; 
        $limit = "LIMIT 12";
        
        switch ($kategori['kategori_adi']) {
            case 'En İyi Filmler': $sql_taban .= " AND filmler.tip = 'film'"; $sql_siralama .= " ORDER BY ortalama_puan DESC"; break;
            case 'En İyi Diziler': $sql_taban .= " AND filmler.tip = 'dizi'"; $sql_siralama .= " ORDER BY ortalama_puan DESC"; break;
            case 'En Yeni Çıkan Filmler': $sql_taban .= " AND filmler.tip = 'film'"; $sql_siralama .= " ORDER BY filmler.cikis_yili DESC"; break;
            case 'En Yeni Çıkan Diziler': $sql_taban .= " AND filmler.tip = 'dizi'"; $sql_siralama .= " ORDER BY filmler.cikis_yili DESC"; break;
        }
        
        try {
            $film_sorgusu = $db->prepare($sql_taban . " " . $sql_siralama . " " . $limit);
            $film_sorgusu->execute([$kategori['id']]);
            $kategori_filmleri = $film_sorgusu->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { die("Hata: " . $e->getMessage()); }

        if (count($kategori_filmleri) > 0): 
    ?>
    <div class="category-row mt-5">
        <h3 class="category-title"><?php echo htmlspecialchars($kategori['kategori_adi']); ?></h3>
        <div class="swiper horizontal-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($kategori_filmleri as $k_film): ?>
                <div class="swiper-slide">
                    <div class="scroll-card">
                        <a href="detay.php?id=<?php echo $k_film['id']; ?>">
                            <img src="<?php echo !empty($k_film['afis_url']) ? htmlspecialchars($k_film['afis_url']) : 'https://via.placeholder.com/200x300'; ?>" alt="<?php echo htmlspecialchars($k_film['baslik']); ?>">
                            <div class="scroll-card-body">
                                <strong><?php echo htmlspecialchars($k_film['baslik']); ?></strong>
                                <?php if (isset($k_film['ortalama_puan']) && $k_film['ortalama_puan'] > 0): ?>
                                    <div class="scroll-card-rating"><i class="fa-solid fa-star"></i> <?php echo number_format($k_film['ortalama_puan'], 1); ?></div>
                                <?php else: ?>
                                    <span><?php echo $k_film['cikis_yili']; ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
    <?php endif; endforeach; ?>

</div> <?php require_once 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // Swiper Başlatıcı
    const swiper = new Swiper('.horizontal-swiper', {
        slidesPerView: 2, 
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: { slidesPerView: 3, spaceBetween: 20 },
            768: { slidesPerView: 4, spaceBetween: 30 },
            1024: { slidesPerView: 5, spaceBetween: 30 },
        }
    });
</script>