<?php
require_once 'header.php';

if (!isset($_SESSION['kullanici_id'])) { header("Location: giris.php"); exit; }
$kullanici_id = $_SESSION['kullanici_id'];

try {
    // 1. Kullanıcı Bilgileri
    $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
    $sorgu->execute([$kullanici_id]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    // 2. İstatistikler
    $watch_sayisi = $db->query("SELECT COUNT(*) FROM watchlist WHERE kullanici_id = $kullanici_id")->fetchColumn();
    $yorum_sayisi = $db->query("SELECT COUNT(*) FROM yorumlar WHERE kullanici_id = $kullanici_id")->fetchColumn();

    // 3. TÜM Yorumlar
    $yorumlar_sorgu = $db->prepare("SELECT yorumlar.*, filmler.baslik, filmler.afis_url, filmler.id as film_id FROM yorumlar JOIN filmler ON yorumlar.film_id = filmler.id WHERE yorumlar.kullanici_id = ? ORDER BY yorumlar.tarih DESC");
    $yorumlar_sorgu->execute([$kullanici_id]);
    $tum_yorumlar = $yorumlar_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // 4. YENİ: Watchlist (İzleme Listesi) Filmlerini Çek
    $watchlist_sorgu = $db->prepare("SELECT filmler.* FROM filmler JOIN watchlist ON filmler.id = watchlist.film_id WHERE watchlist.kullanici_id = ? ORDER BY watchlist.eklenme_tarihi DESC");
    $watchlist_sorgu->execute([$kullanici_id]);
    $watchlist_filmleri = $watchlist_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // 5. Admin Logları
    $admin_loglari = [];
    if ($kullanici['rol'] == 'admin') {
        $log_sorgu = $db->prepare("SELECT * FROM admin_loglari WHERE admin_id = ? ORDER BY tarih DESC LIMIT 20");
        $log_sorgu->execute([$kullanici_id]);
        $admin_loglari = $log_sorgu->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) { die("Hata: " . $e->getMessage()); }

$avatar_harf = strtoupper(substr($kullanici['kullanici_adi'], 0, 1));
?>

<div class="profile-header-wrapper">
    <div class="profile-banner"></div>
    <div class="container profile-meta-container">
        <div class="d-flex align-items-end">
            <div class="profile-avatar-big shadow-lg"><?php echo $avatar_harf; ?></div>
            <div class="ms-4 mb-2">
                <h1 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($kullanici['kullanici_adi']); ?></h1>
                <div class="mt-2">
                    <span class="badge <?php echo ($kullanici['rol']=='admin')?'bg-danger':'bg-secondary'; ?>"><?php echo strtoupper($kullanici['rol']); ?></span>
                    <span class="text-white-50 ms-2 small"><i class="bi bi-calendar3"></i> Üyelik: <?php echo date('d.m.Y', strtotime($kullanici['kayit_tarihi'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
         <div class="card bg-dark border-secondary mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary fw-bold">Watchlist</span>
                        <span class="fw-bold text-white"><?php echo $watch_sayisi; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary fw-bold">Yorumlar</span>
                        <span class="fw-bold text-white"><?php echo $yorum_sayisi; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="list-group bg-dark profile-menu">
                <button class="list-group-item list-group-item-action active" data-bs-toggle="tab" data-bs-target="#tab-yorumlar">
                    <i class="bi bi-chat-quote-fill me-2"></i> Yorumlarım & Puanlarım
                </button>
                
                <button class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#tab-watchlist">
                    <i class="bi bi-bookmark-fill me-2"></i> İzleme Listem
                </button>
                
                <?php if($kullanici['rol'] == 'admin'): ?>
                    <button class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#tab-loglar"><i class="bi bi-shield-lock-fill me-2"></i> Yönetim Geçmişi</button>
                    <a href="admin/index.php" class="list-group-item list-group-item-action"><i class="bi bi-gear-fill me-2"></i> Admin Paneli</a>
                <?php endif; ?>
                <a href="cikis.php" class="list-group-item list-group-item-action text-danger"><i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap</a>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="tab-yorumlar">
                    <h4 class="text-white mb-4 border-bottom border-secondary pb-2"><i class="bi bi-star-half text-warning me-2"></i> Puan ve Yorumlarım</h4>
                    <?php if (empty($tum_yorumlar)): ?>
                        <div class="alert alert-dark border-secondary text-center text-white-50">Henüz hiçbir filme yorum yapmadın.</div>
                    <?php else: ?>
                        <?php foreach ($tum_yorumlar as $yorum): ?>
                            <div class="card bg-dark border-secondary mb-3 review-card">
                                <div class="row g-0">
                                    <div class="col-md-2 col-3">
                                        <a href="detay.php?id=<?php echo $yorum['film_id']; ?>"><img src="<?php echo !empty($yorum['afis_url']) ? $yorum['afis_url'] : 'https://via.placeholder.com/100x150'; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover; min-height: 140px;" alt="Afiş"></a>
                                    </div>
                                    <div class="col-md-10 col-9">
                                        <div class="card-body h-100 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0"><a href="detay.php?id=<?php echo $yorum['film_id']; ?>" class="text-decoration-none text-white fw-bold"><?php echo htmlspecialchars($yorum['baslik']); ?></a></h5>
                                                <?php if($yorum['puan']): ?><span class="badge bg-warning text-dark fs-6"><i class="bi bi-star-fill"></i> <?php echo $yorum['puan']; ?></span><?php endif; ?>
                                            </div>
                                            <small class="text-white-50 mb-2"><?php echo date('d.m.Y H:i', strtotime($yorum['tarih'])); ?></small>
                                            <p class="card-text text-light mt-2" style="font-style: italic;">"<?php echo nl2br(htmlspecialchars($yorum['yorum_metni'])); ?>"</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="tab-watchlist">
                    <h4 class="text-white mb-4 border-bottom border-secondary pb-2"><i class="bi bi-bookmark-heart text-danger me-2"></i> İzleme Listem</h4>
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                        <?php if (empty($watchlist_filmleri)): ?>
                            <div class="col-12"><p class="text-white-50">Listeniz henüz boş.</p></div>
                        <?php else: ?>
                            <?php foreach ($watchlist_filmleri as $w_film): ?>
                                <div class="col">
                                    <div class="scroll-card"> <a href="detay.php?id=<?php echo $w_film['id']; ?>">
                                            <img src="<?php echo !empty($w_film['afis_url']) ? $w_film['afis_url'] : 'https://via.placeholder.com/200x300'; ?>" alt="Afiş">
                                            <div class="scroll-card-body">
                                                <strong><?php echo htmlspecialchars($w_film['baslik']); ?></strong>
                                                <span class="text-white-50"><?php echo $w_film['cikis_yili']; ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($kullanici['rol'] == 'admin'): ?>
                <div class="tab-pane fade" id="tab-loglar">
                    <h4 class="text-white mb-4 border-bottom border-secondary pb-2">Yönetim Geçmişi</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-bordered border-secondary">
                            <thead><tr class="text-white-50"><th>Tarih</th><th>İşlem</th><th>Detay</th></tr></thead>
                            <tbody>
                                <?php if(empty($admin_loglari)): ?><tr><td colspan="3" class="text-center text-white-50">İşlem yok.</td></tr><?php else: ?><?php foreach ($admin_loglari as $log): ?><tr><td class="text-white-50"><?php echo date('d.m.Y H:i', strtotime($log['tarih'])); ?></td><td class="text-warning fw-bold"><?php echo htmlspecialchars($log['islem']); ?></td><td class="text-light"><?php echo htmlspecialchars($log['detay']); ?></td></tr><?php endforeach; ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>