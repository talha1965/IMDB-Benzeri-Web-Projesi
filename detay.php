<?php
// Gerekli dosyaları (header, db) çek
require_once 'header.php';

// URL'den filmin ID'sini almamız lazım (örn: detay.php?id=5)
if (isset($_GET['id'])) {
    $film_id = (int)$_GET['id'];
} else {
    // ID yoksa, ana sayfaya geri yolla
    header("Location: index.php");
    exit;
}

try {
    // 1. Filmin kendi bilgilerini çek
    $sorgu = $db->prepare("SELECT * FROM filmler WHERE id = ?");
    $sorgu->execute([$film_id]);
    $film = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer bu ID ile bir film bulunamazsa, hata ver ve dur
    if (!$film) {
        echo "<div class='container mt-4'><div class='alert alert-danger'>HATA: Film bulunamadı!</div></div>";
        require_once 'footer.php';
        exit;
    }

    // 2. Bu filme ait yorumları çek (Yorum yapanın adıyla birlikte)
    $yorum_sorgusu = $db->prepare("
        SELECT yorumlar.*, kullanicilar.kullanici_adi 
        FROM yorumlar 
        JOIN kullanicilar ON yorumlar.kullanici_id = kullanicilar.id 
        WHERE yorumlar.film_id = ? 
        ORDER BY yorumlar.tarih DESC
    ");
    $yorum_sorgusu->execute([$film_id]);
    $yorumlar = $yorum_sorgusu->fetchAll(PDO::FETCH_ASSOC);

    // 3. Puan Ortalamasını hesapla
    $puan_sorgusu = $db->prepare("
        SELECT AVG(puan) as ortalama_puan, COUNT(puan) as puan_sayisi 
        FROM yorumlar 
        WHERE film_id = ? AND puan IS NOT NULL
    ");
    $puan_sorgusu->execute([$film_id]);
    $puan_sonuclari = $puan_sorgusu->fetch(PDO::FETCH_ASSOC);
    $ortalama_puan = $puan_sonuclari['ortalama_puan'];
    $puan_sayisi = $puan_sonuclari['puan_sayisi'];

    // 4. Kullanıcı bu filmi listesine eklemiş mi? (Watchlist kontrolü)
    $film_listede = false; 
    if (isset($_SESSION['kullanici_id'])) {
        $watch_sorgu = $db->prepare("SELECT id FROM watchlist WHERE kullanici_id = ? AND film_id = ?");
        $watch_sorgu->execute([$_SESSION['kullanici_id'], $film_id]);
        // Kayıt varsa (rowCount > 0), demek ki listededir
        if ($watch_sorgu->rowCount() > 0) {
            $film_listede = true;
        }
    }

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<div class="container">
    <div class="row">
        
        <div class="col-md-4">
            <img src="<?php echo !empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/400x600.png?text=Afiş+Yok'; ?>" 
                 class="img-fluid rounded shadow-lg" alt="<?php echo htmlspecialchars($film['baslik']); ?>">
            
            <div class="d-grid gap-2 mt-3">
                <?php if (isset($_SESSION['kullanici_id'])): ?>
                    
                    <?php if ($film_listede): ?>
                        <a href="watchlist_islem.php?film_id=<?php echo $film['id']; ?>&action=kaldir" class="btn btn-secondary btn-lg">
                            <i class="bi bi-bookmark-check-fill"></i> Listeden Kaldır
                        </a>
                    <?php else: ?>
                        <a href="watchlist_islem.php?film_id=<?php echo $film['id']; ?>&action=ekle" class="btn btn-danger btn-lg">
                            <i class="bi bi-bookmark-plus"></i> İzleme Listeme Ekle
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="text-light text-center mt-3">
                        Filmi listene eklemek için lütfen <a href="giris.php" class="text-danger">giriş yapın</a>.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8">
            <h1><?php echo htmlspecialchars($film['baslik']); ?> (<?php echo htmlspecialchars($film['cikis_yili']); ?>)</h1>
            <p class="text-light fs-5"><?php echo htmlspecialchars($film['tur']); ?></p>
            <hr>
            
            <h3><i class="bi bi-book-fill"></i> Açıklama</h3>
            <p class="text-light"><?php echo nl2br(htmlspecialchars($film['aciklama'])); ?></p>
            <hr>
            
            <p><strong>Yönetmen:</strong> <?php echo htmlspecialchars($film['yonetmen']); ?></p>

            <div class="my-3">
                <h4><i class="bi bi-star-fill text-warning"></i> Puanlama</h4>
                <?php if ($puan_sayisi > 0): ?>
                    <p class="fs-4">
                        Kullanıcı Puanı: 
                        <strong class="text-light"><?php echo number_format($ortalama_puan, 1); ?></strong> / 10 
                        <small style="color: #adb5bd;">(<?php echo $puan_sayisi; ?> oy)</small> 
                    </p>
                <?php else: ?>
                    <p style="color: #adb5bd;">Bu film henüz puanlanmamış.</p>
                <?php endif; ?>
            </div>
            <hr>

            <div class="mt-4">
                <h3><i class="bi bi-chat-dots-fill"></i> Yorum Yap</h3>
                <?php if (isset($_SESSION['kullanici_id'])): ?>
                    <form action="yorum_ekle.php" method="POST">
                        <input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
                        <div class="mb-3">
                            <label for="puan" class="form-label">Puanınız (1-10):</label>
                            <select class="form-select bg-dark text-light" id="puan" name="puan" required>
                                <option value="">-- Puan Seçin --</option>
                                <option value="10">10 - Mükemmel</option>
                                <option value="9">9 - Harika</option>
                                <option value="8">8 - Çok İyi</option>
                                <option value="7">7 - İyi</option>
                                <option value="6">6 - Fena Değil</option>
                                <option value="5">5 - Ortalama</option>
                                <option value="4">4 - Kötü</option>
                                <option value="3">3 - Çok Kötü</option>
                                <option value="2">2 - Berbat</option>
                                <option value="1">1 - Felaket</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="yorum_metni" class="form-label">Yorumunuz:</label>
                            <textarea class="form-control bg-dark text-light" id="yorum_metni" name="yorum_metni" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Yorumu Gönder</button>
                    </form>
                <?php else: ?>
                    <p class="alert alert-warning">
                        Yorum yapmak veya puan vermek için lütfen <a href="giris.php?yonlendir=detay.php?id=<?php echo $film['id']; ?>" class="alert-link">giriş yapın</a>.
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="mt-5">
                <h3><i class="bi bi-chat-quote-fill"></i> Yorumlar (<?php echo count($yorumlar); ?>)</h3>
                <hr>
                
                <?php if (empty($yorumlar)): ?>
                    <p style="color: #adb5bd;">Bu film için henüz hiç yorum yapılmamış.</p>
                <?php else: ?>
                    <?php foreach ($yorumlar as $yorum): ?>
                        <div class="card bg-dark border-secondary mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-danger fs-5">
                                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($yorum['kullanici_adi']); ?>
                                </h6>
                                <?php if (!empty($yorum['puan'])): ?>
                                    <p class="card-subtitle mb-2 text-warning">
                                        <i class="bi bi-star-fill"></i> Puanı: 
                                        <strong><?php echo htmlspecialchars($yorum['puan']); ?> / 10</strong>
                                    </p>
                                <?php endif; ?>

                                <p class="card-text text-light"><?php echo nl2br(htmlspecialchars($yorum['yorum_metni'])); ?></p>
                                <small style="color: #adb5bd;">
                                    <?php echo date('d M Y, H:i', strtotime($yorum['tarih'])); ?>
                                </small>

                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                                    <a href="yorum_sil.php?id=<?php echo $yorum['id']; ?>&film_id=<?php echo $film['id']; ?>" 
                                       class="btn btn-outline-secondary btn-sm float-end" 
                                       onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">
                                        <i class="bi bi-trash-fill"></i> Sil
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı çek
require_once 'footer.php';
?>