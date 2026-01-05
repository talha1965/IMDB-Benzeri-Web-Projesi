<?php
require 'admin_header.php'; 
$mesaj = "";
$mesaj_tipi = "";

// 1. Form Gönderilmiş mi? (GÜNCELLEME İŞLEMİ)
if (isset($_POST['film_guncelle'])) {
    
    // Formdan gelen verileri al
    $film_id = (int)$_POST['film_id'];
    $tip = $_POST['tip']; 
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $yonetmen = $_POST['yonetmen'];
    $tur = $_POST['tur'];
    $afis_url = $_POST['afis_url'];
    
    // YIL VERİLERİ (GÜNCELLENDİ)
    $cikis_yili = $_POST['cikis_yili'];
    // Bitiş yılı boşsa NULL yap, doluysa al
    $bitis_yili = !empty($_POST['bitis_yili']) ? $_POST['bitis_yili'] : NULL;

    // Slider görseli
    $slider_gorsel_url = !empty($_POST['slider_gorsel_url']) ? $_POST['slider_gorsel_url'] : NULL;

    $secilen_kategoriler = isset($_POST['kategoriler']) ? $_POST['kategoriler'] : [];

    try {
        $db->beginTransaction();

        // === GÜNCELLENDİ: 'bitis_yili' sorguya eklendi ===
        $update_sorgusu = $db->prepare("UPDATE filmler SET 
                                        baslik = ?, tip = ?, aciklama = ?, 
                                        yonetmen = ?, cikis_yili = ?, bitis_yili = ?, tur = ?, 
                                        afis_url = ?, slider_gorsel_url = ? 
                                        WHERE id = ?");
        
        // === GÜNCELLENDİ: execute dizisine '$bitis_yili' eklendi ===
        $update_sorgusu->execute([
            $baslik, $tip, $aciklama, 
            $yonetmen, $cikis_yili, $bitis_yili, $tur, 
            $afis_url, $slider_gorsel_url, 
            $film_id
        ]);
        
        // Eski kategorileri sil
        $silme_sorgusu = $db->prepare("DELETE FROM film_kategori_iliskisi WHERE film_id = ?");
        $silme_sorgusu->execute([$film_id]);

        // Yeni kategorileri ekle
        if (!empty($secilen_kategoriler)) {
            $iliski_sorgusu = $db->prepare("INSERT INTO film_kategori_iliskisi (film_id, kategori_id) VALUES (?, ?)");
            foreach ($secilen_kategoriler as $kategori_id) {
                $iliski_sorgusu->execute([$film_id, $kategori_id]);
            }
        }
        
        $db->commit();
        
        $mesaj = "İçerik başarıyla güncellendi!";
        $mesaj_tipi = "success";
        
        // İşlem bitince listeye dön
        header("Refresh:2; url=filmleri_yonet.php");

    } catch (PDOException $e) {
        $db->rollBack();
        $mesaj = "Güncelleme hatası: " . $e->getMessage();
        $mesaj_tipi = "danger";
    }
}

// 2. Sayfa Normal Yüklendiyse (VERİ ÇEKME)
try {
    if (!isset($_GET['id'])) { header("Location: filmleri_yonet.php"); exit; }
    $film_id_get = (int)$_GET['id'];

    // Filmin bilgilerini çek
    $sorgu = $db->prepare("SELECT * FROM filmler WHERE id = ?");
    $sorgu->execute([$film_id_get]);
    $film = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$film) { header("Location: filmleri_yonet.php"); exit; }

    // Kategorileri çek
    $tum_kategoriler_sorgu = $db->prepare("SELECT * FROM kategoriler ORDER BY sira ASC");
    $tum_kategoriler_sorgu->execute();
    $tum_kategoriler = $tum_kategoriler_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // Mevcut kategorileri işaretlemek için çek
    $mevcut_kategoriler_sorgu = $db->prepare("SELECT kategori_id FROM film_kategori_iliskisi WHERE film_id = ?");
    $mevcut_kategoriler_sorgu->execute([$film_id_get]);
    $mevcut_kategori_idler = $mevcut_kategoriler_sorgu->fetchAll(PDO::FETCH_COLUMN, 0);

} catch (PDOException $e) {
    die("Veri hatası: " . $e->getMessage());
}
?>

<script>document.title = "Düzenle: <?php echo htmlspecialchars($film['baslik']); ?>";</script>

<div class="container mt-4 mb-5">
    <div class="admin-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>İçeriği Düzenle</h2>
            <a href="filmleri_yonet.php" class="btn btn-secondary btn-sm">Listeye Dön</a>
        </div>
        <hr>

        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesaj_tipi; ?>"><?php echo $mesaj; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="baslik" class="form-label">Başlık</label>
                        <input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo htmlspecialchars($film['baslik']); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tip" class="form-label">Tip</label>
                        <select class="form-select" id="tip" name="tip" required>
                            <option value="film" <?php echo ($film['tip'] == 'film') ? 'selected' : ''; ?>>Film</option>
                            <option value="dizi" <?php echo ($film['tip'] == 'dizi') ? 'selected' : ''; ?>>Dizi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="aciklama" class="form-label">Açıklama</label>
                <textarea class="form-control" id="aciklama" name="aciklama" rows="5"><?php echo htmlspecialchars($film['aciklama']); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="yonetmen" class="form-label">Yönetmen</label>
                    <input type="text" class="form-control" id="yonetmen" name="yonetmen" value="<?php echo htmlspecialchars($film['yonetmen']); ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="cikis_yili" class="form-label">Başlangıç / Çıkış Yılı</label>
                    <input type="number" class="form-control" id="cikis_yili" name="cikis_yili" min="1800" max="2050" value="<?php echo $film['cikis_yili']; ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="bitis_yili" class="form-label">Bitiş Yılı</label>
                    <input type="number" class="form-control" id="bitis_yili" name="bitis_yili" 
                           value="<?php echo $film['bitis_yili']; ?>" 
                           placeholder="Devam ediyorsa boş">
                    <small class="text-muted" style="font-size: 0.7rem;">Sadece bitmiş diziler için.</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="tur" class="form-label">Tür (Virgülle ayırın)</label>
                <input type="text" class="form-control" id="tur" name="tur" value="<?php echo htmlspecialchars($film['tur']); ?>">
            </div>
            
            <div class="mb-3">
                <label for="afis_url" class="form-label">Afiş URL'si (Dikey Poster)</label>
                <input type="text" class="form-control" id="afis_url" name="afis_url" value="<?php echo htmlspecialchars($film['afis_url']); ?>">
            </div>

            <div class="mb-3">
                <label for="slider_gorsel_url" class="form-label">Slider Görsel URL'si (Yatay Banner)</label>
                <input type="text" class="form-control" id="slider_gorsel_url" name="slider_gorsel_url" 
                       value="<?php echo htmlspecialchars($film['slider_gorsel_url']); ?>">
            </div>
            
            <hr>
            
            <div class="mb-3">
                <h5>Kategoriler</h5>
                <div class="row">
                    <?php if (empty($tum_kategoriler)): ?>
                        <p class="text-danger">Kategori bulunamadı.</p>
                    <?php else: ?>
                        <?php foreach ($tum_kategoriler as $kategori): ?>
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="kategoriler[]" 
                                           value="<?php echo $kategori['id']; ?>" 
                                           id="kategori_<?php echo $kategori['id']; ?>"
                                           <?php if (in_array($kategori['id'], $mevcut_kategori_idler)) { echo 'checked'; } ?>>
                                    <label class="form-check-label" for="kategori_<?php echo $kategori['id']; ?>">
                                        <?php echo htmlspecialchars($kategori['kategori_adi']); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                 <a href="filmleri_yonet.php" class="btn btn-secondary me-md-2">İptal</a>
                 <button type="submit" name="film_guncelle" class="btn btn-primary px-5">Güncelle Kaydet</button>
            </div>
        </form>
    </div>
</div>

<?php 
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>'; 
?>