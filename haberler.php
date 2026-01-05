<?php
require_once 'header.php';

try {
    // Tüm haberleri tarihe göre çek
    $sorgu = $db->prepare("SELECT * FROM haberler ORDER BY tarih DESC");
    $sorgu->execute();
    $tum_haberler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4 border-start border-4 border-danger ps-3 text-white">Film & Dizi Haberleri</h2>
    
    <?php if (empty($tum_haberler)): ?>
        <p class="text-white-50">Henüz haber eklenmemiş.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($tum_haberler as $haber): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 bg-dark border-secondary shadow-sm hover-scale">
                        <a href="haber_detay.php?id=<?php echo $haber['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo !empty($haber['resim_url']) ? htmlspecialchars($haber['resim_url']) : 'https://via.placeholder.com/400x250'; ?>" 
                                 class="card-img-top" 
                                 alt="Haber"
                                 style="height: 200px; object-fit: cover;">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-white fw-bold"><?php echo htmlspecialchars($haber['baslik']); ?></h5>
                                <p class="card-text flex-grow-1" style="color: #adb5bd;">
                                    <?php 
                                    // İçeriğin ilk 100 karakterini göster
                                    echo mb_strimwidth(htmlspecialchars($haber['icerik']), 0, 100, '...'); 
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-white-50"><?php echo date('d.m.Y', strtotime($haber['tarih'])); ?></small>
                                    <span class="btn btn-sm btn-outline-danger">Devamını Oku</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>