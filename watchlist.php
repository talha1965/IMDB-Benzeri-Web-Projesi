<?php
// Header'ı (menü, session, db) çek
require_once 'header.php';

// Güvenlik: Kullanıcı giriş yapmamışsa bu sayfayı göremez.
if (!isset($_SESSION['kullanici_id'])) {
    // Giriş sayfasına yolla
    header("Location: giris.php");
    exit;
}

// Giriş yapan kullanıcının ID'sini al
$kullanici_id = $_SESSION['kullanici_id'];

// Veritabanından bu kullanıcının listesindeki filmleri çek
try {
    // 'watchlist' tablosuyla 'filmler' tablosunu birleştir (JOIN)
    // Sadece bu kullanıcıya ait olanları (WHERE) al
    // En son eklediği en üstte görünsün (ORDER BY)
    $sorgu = $db->prepare("
        SELECT filmler.* FROM filmler 
        JOIN watchlist ON filmler.id = watchlist.film_id
        WHERE watchlist.kullanici_id = ?
        ORDER BY watchlist.eklenme_tarihi DESC
    ");
    $sorgu->execute([$kullanici_id]);
    $filmler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Watchlist çekilirken bir hata oluştu: " . $e->getMessage());
}

?>

<div class="container">

    <h1 class="mt-4 mb-3">
        <i class="bi bi-bookmark-fill text-danger"></i> İzleme Listem (<?php echo count($filmler); ?>)
    </h1>
    <p class="fs-5" style="color: #adb5bd;">Listene eklediğin filmler ve diziler burada görünür.</p>
    <hr>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        
        <?php if (empty($filmler)): ?>
            <div class="col-12">
                <p class="alert alert-info">İzleme listeniz şu anda boş. Detay sayfasından film ekleyebilirsiniz.</p>
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