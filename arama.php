<?php
// Header'ı (menü, session, db bağlantısı) çek
require_once 'header.php';

// Arama terimi URL'de var mı diye bakalım (örn: arama.php?q=oppenheimer)
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    
    // Aranan kelimeyi al
    $q_raw = $_GET['q'];
    
    // Veritabanında 'LIKE' ile aramak için başına ve sonuna % ekleyelim
    $q_sql = "%" . $q_raw . "%";

    try {
        // Filmleri 'baslik' sütununda ara, en yeni çıkanlar üstte olsun
        $sorgu = $db->prepare("SELECT * FROM filmler WHERE baslik LIKE ? ORDER BY cikis_yili DESC");
        $sorgu->execute([$q_sql]);
        $filmler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Sorguda hata olursa
        die("Arama sorgusu hatası: " . $e->getMessage());
    }

} else {
    // Eğer kimse bir şey aramadıysa veya boş arama yaptıysa
    $filmler = []; // Boş bir film dizisi oluştur (hata vermesin)
    $q_raw = ""; // Aranan kelimeyi boş yap
}
?>

<div class="container">

    <h1 class="mt-4 mb-3">
        <i class="bi bi-search text-danger"></i> Arama Sonuçları
    </h1>
    
    <p class="fs-5" style="color: #adb5bd;">
        "<?php echo htmlspecialchars($q_raw); ?>" için (<?php echo count($filmler); ?>) sonuç bulundu.
    </p>
    <hr>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        
        <?php if (empty($filmler)): ?>
            <div class="col-12">
                <p class="alert alert-warning">Aradığınız kritere uygun film bulunamadı.</p>
            </div>
        <?php else: ?>
            <?php foreach ($filmler as $film): ?>
                <div class="col">
                    <div class="card h-100 bg-dark text-light border-secondary">
                        <img src="<?php echo !empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/300x450.png?text=Afiş+Yok'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($film['baslik']); ?>"
                             style="height: 400px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($film['baslik']); ?> (<?php echo htmlspecialchars($film['cikis_yili']); ?>)</h5>
                            <p class="card-text flex-grow-1" style="color: #adb5bd;"><?php echo htmlspecialchars($film['tur']); ?></p>
                            <a href="detay.php?id=<?php echo $film['id']; ?>" class="btn btn-danger mt-auto">Detayları Gör</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php
// Footer'ı çek
require_once 'footer.php';
?>