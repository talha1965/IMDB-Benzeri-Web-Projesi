<?php
require 'admin_header.php'; 

// -----------------------------------------------------------------------
// 1. BÖLÜM: FİLMLER VE DİZİLERİ ÇEKME (FİLTRELEME MANTIĞI)
// -----------------------------------------------------------------------

$sql = "SELECT id, baslik, yonetmen, cikis_yili, tip FROM filmler WHERE 1=1";
$params = [];

// A) Arama Yapıldı mı?
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $sql .= " AND baslik LIKE :aranan";
    $params[':aranan'] = '%' . $_GET['q'] . '%';
}

// B) Tür Filtresi (Film mi Dizi mi?)
if (isset($_GET['tur']) && !empty($_GET['tur'])) {
    $sql .= " AND tip = :tur";
    $params[':tur'] = $_GET['tur'];
}

// C) Sıralama
$sirala = $_GET['sirala'] ?? 'yeni';
switch ($sirala) {
    case 'eski': $sql .= " ORDER BY id ASC"; break;
    case 'isim': $sql .= " ORDER BY baslik ASC"; break;
    case 'yil':  $sql .= " ORDER BY cikis_yili DESC"; break;
    default:     $sql .= " ORDER BY id DESC"; break; // Varsayılan: Yeni
}

try {
    $sorgu = $db->prepare($sql);
    $sorgu->execute($params);
    $filmler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Film Hatası: " . $e->getMessage());
}

// -----------------------------------------------------------------------
// 2. BÖLÜM: HABERLERİ ÇEKME
// -----------------------------------------------------------------------
try {
    $haberSorgu = $db->prepare("SELECT id, baslik, tarih FROM haberler ORDER BY tarih DESC");
    $haberSorgu->execute();
    $haberler = $haberSorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Haber Hatası: " . $e->getMessage());
}
?>

<script>document.title = "Admin Paneli - Tüm İçerik Yönetimi";</script>

<div class="container mt-4 mb-5">
    <div class="admin-container">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fa-solid fa-layer-group text-danger"></i> İçerik Yönetim Merkezi</h2>
            <div>
                <a href="film_ekle.php" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Film/Dizi Ekle</a>
                <a href="haber_ekle.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Haber Ekle</a>
            </div>
        </div>
        
        <p class="text-secondary">Filmler, diziler ve haberleri sekmeler arasında geçiş yaparak yönetebilirsiniz.</p>
        <hr>

        <ul class="nav nav-tabs mb-4" id="icerikTablari" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="video-tab" data-bs-toggle="tab" data-bs-target="#video-panel" type="button" role="tab">
                    <i class="fa-solid fa-film me-2"></i>Filmler & Diziler
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="haber-tab" data-bs-toggle="tab" data-bs-target="#haber-panel" type="button" role="tab">
                    <i class="fa-solid fa-newspaper me-2"></i>Haberler
                </button>
            </li>
        </ul>

        <div class="tab-content" id="icerikTabIcerigi">
            
            <div class="tab-pane fade show active" id="video-panel" role="tabpanel">
                
                <div class="card bg-dark border-secondary mb-3 p-3">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control bg-secondary text-white border-0" placeholder="Film adı ara..." value="<?php echo $_GET['q'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="tur" class="form-select bg-secondary text-white border-0">
                                <option value="">Tüm Türler</option>
                                <option value="film" <?php echo (isset($_GET['tur']) && $_GET['tur'] == 'film') ? 'selected' : ''; ?>>Sadece Filmler</option>
                                <option value="dizi" <?php echo (isset($_GET['tur']) && $_GET['tur'] == 'dizi') ? 'selected' : ''; ?>>Sadece Diziler</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="sirala" class="form-select bg-secondary text-white border-0">
                                <option value="yeni" <?php echo (isset($_GET['sirala']) && $_GET['sirala'] == 'yeni') ? 'selected' : ''; ?>>En Yeni Eklenen</option>
                                <option value="eski" <?php echo (isset($_GET['sirala']) && $_GET['sirala'] == 'eski') ? 'selected' : ''; ?>>En Eski Eklenen</option>
                                <option value="yil" <?php echo (isset($_GET['sirala']) && $_GET['sirala'] == 'yil') ? 'selected' : ''; ?>>Çıkış Yılına Göre</option>
                                <option value="isim" <?php echo (isset($_GET['sirala']) && $_GET['sirala'] == 'isim') ? 'selected' : ''; ?>>İsme Göre (A-Z)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100">Filtrele</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover border-secondary align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Başlık</th>
                                <th>Tür</th>
                                <th>Yönetmen</th>
                                <th>Yıl</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filmler)): ?>
                                <tr><td colspan="6" class="text-center py-4">Kriterlere uygun içerik bulunamadı.</td></tr>
                            <?php else: ?>
                                <?php foreach ($filmler as $film): ?>
                                    <tr>
                                        <td>#<?php echo $film['id']; ?></td>
                                        <td class="fw-bold text-white"><?php echo htmlspecialchars($film['baslik']); ?></td>
                                        <td>
                                            <?php if($film['tip'] == 'dizi'): ?>
                                                <span class="badge bg-warning text-dark">Dizi</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Film</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($film['yonetmen']); ?></td>
                                        <td><?php echo $film['cikis_yili']; ?></td>
                                        <td class="text-end">
                                            <a href="film_duzenle.php?id=<?php echo $film['id']; ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="film_sil.php?id=<?php echo $film['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?');"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="haber-panel" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="table table-dark table-striped table-hover border-secondary align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Haber Başlığı</th>
                                <th>Tarih</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($haberler)): ?>
                                <tr><td colspan="4" class="text-center py-4">Henüz haber eklenmemiş.</td></tr>
                            <?php else: ?>
                                <?php foreach ($haberler as $haber): ?>
                                    <tr>
                                        <td>#<?php echo $haber['id']; ?></td>
                                        <td><?php echo htmlspecialchars($haber['baslik']); ?></td>
                                        <td class="text-secondary"><?php echo date("d.m.Y H:i", strtotime($haber['tarih'])); ?></td>
                                        <td class="text-end">
                                            <a href="haber_duzenle.php?id=<?php echo $haber['id']; ?>" class="btn btn-info btn-sm"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="haber_sil.php?id=<?php echo $haber['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu haberi silmek istediğine emin misin?');"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>