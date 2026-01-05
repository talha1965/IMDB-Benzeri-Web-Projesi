<?php
require_once 'header.php';

// --- SAYFALAMA AYARLARI ---
$limit = 18; 
$sayfa = isset($_GET['sayfa']) && is_numeric($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$baslangic = ($sayfa > 1) ? ($sayfa - 1) * $limit : 0;

try {
    // Toplam FİLM sayısını bul
    $toplamSorgu = $db->prepare("SELECT COUNT(id) FROM filmler WHERE tip = 'film'");
    $toplamSorgu->execute();
    $toplamVeri = $toplamSorgu->fetchColumn();
    $toplamSayfa = ceil($toplamVeri / $limit);

    // Filmleri çek
    $filmlerSorgu = $db->prepare("
        SELECT f.*, AVG(IFNULL(y.puan, 0)) AS ortalama_puan
        FROM filmler f
        LEFT JOIN yorumlar y ON f.id = y.film_id
        WHERE f.tip = 'film'
        GROUP BY f.id
        ORDER BY f.cikis_yili DESC
        LIMIT :baslangic, :limit
    ");
    $filmlerSorgu->bindValue(':baslangic', $baslangic, PDO::PARAM_INT);
    $filmlerSorgu->bindValue(':limit', $limit, PDO::PARAM_INT);
    $filmlerSorgu->execute();
    $filmler = $filmlerSorgu->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('<div class="alert alert-danger m-5">Veritabanı Hatası: ' . $e->getMessage() . '</div>');
}
?>

<style>
    .movie-card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .movie-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(229, 9, 20, 0.2) !important;
        border-color: #E50914 !important;
    }
    .pagination .page-link { background-color: #212529; border-color: #343a40; color: #fff; }
    .pagination .page-item.active .page-link { background-color: #E50914; border-color: #E50914; color: #fff; }
    .pagination .page-item.disabled .page-link { background-color: #1a1d20; color: #6c757d; }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
        <div>
            <h2 class="text-danger fw-bold mb-0" style="letter-spacing: 1px;"><i class="fa-solid fa-film me-2"></i>FİLMLER</h2>
            <small class="text-white">Arşivimizdeki tüm filmler listeleniyor.</small>
        </div>
        <span class="badge bg-secondary bg-opacity-25 text-light px-3 py-2">Toplam <?php echo $toplamVeri; ?> içerik</span>
    </div>

    <?php if (empty($filmler)): ?>
        <div class="alert alert-dark text-center py-5 my-5 shadow">
            <h4>Henüz Film Eklenmemiş</h4>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3 g-lg-4">
            <?php foreach ($filmler as $film): ?>
                <div class="col">
                    <a href="detay.php?id=<?php echo $film['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 bg-dark text-white border-secondary shadow-sm movie-card-hover" style="overflow: hidden;">
                            
                            <div style="position: relative;">
                                <img src="<?php echo !empty($film['afis_url']) ? htmlspecialchars($film['afis_url']) : 'https://via.placeholder.com/300x450?text=Afiş+Yok'; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($film['baslik']); ?>"
                                     style="aspect-ratio: 2/3; object-fit: cover;" loading="lazy">

                                <?php if ($film['ortalama_puan'] > 0): ?>
                                    <div class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-1 rounded shadow-sm" 
                                         style="font-size: 0.75rem; font-weight: bold;">
                                        <i class="fa-solid fa-star me-1" style="font-size: 0.7rem;"></i><?php echo number_format($film['ortalama_puan'], 1); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body p-2 d-flex flex-column">
                                <h6 class="card-title text-truncate mb-1 small fw-bold" title="<?php echo htmlspecialchars($film['baslik']); ?>">
                                    <?php echo htmlspecialchars($film['baslik']); ?>
                                </h6>
                                <div class="mt-auto d-flex justify-content-between align-items-center small text-secondary">
                                    <span><i class="fa-regular fa-calendar me-1"></i><?php echo $film['cikis_yili']; ?></span>
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary" style="font-size: 0.6rem;">FİLM</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($toplamSayfa > 1): ?>
        <nav aria-label="Sayfalama" class="my-5 d-flex justify-content-center">
            <ul class="pagination pagination-sm shadow-sm">
                <li class="page-item <?php echo ($sayfa <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?sayfa=<?php echo $sayfa - 1; ?>">&laquo;</a>
                </li>
                <?php for ($s = max(1, $sayfa - 2); $s <= min($toplamSayfa, $sayfa + 2); $s++): ?>
                    <li class="page-item <?php echo ($sayfa == $s) ? 'active' : ''; ?>">
                        <a class="page-link" href="?sayfa=<?php echo $s; ?>"><?php echo $s; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($sayfa >= $toplamSayfa) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?sayfa=<?php echo $sayfa + 1; ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>