<?php
require 'admin_header.php'; 
$mesaj = "";
$mesaj_tipi = ""; 

// Form gönderildi mi?
if (isset($_POST['film_ekle'])) {
    
    // 1. Verileri Al
    $baslik = $_POST['baslik'];
    $tip = $_POST['tip']; 
    $aciklama = $_POST['aciklama'];
    $yonetmen = $_POST['yonetmen'];
    $tur = $_POST['tur'];
    $afis_url = $_POST['afis_url'];
    
    // Yıl Verileri
    $cikis_yili = $_POST['cikis_yili'];
    // Bitiş yılı boşsa NULL olsun, doluysa veriyi al
    $bitis_yili = !empty($_POST['bitis_yili']) ? $_POST['bitis_yili'] : NULL;

    // Slider görseli (Boşsa NULL)
    $slider_gorsel_url = !empty($_POST['slider_gorsel_url']) ? $_POST['slider_gorsel_url'] : NULL;

    try {
        // 2. Veritabanına Ekle (bitis_yili eklendi)
        $insert_sorgusu = $db->prepare("INSERT INTO filmler 
            (baslik, tip, aciklama, yonetmen, cikis_yili, bitis_yili, tur, afis_url, slider_gorsel_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $insert_sorgusu->execute([
            $baslik, $tip, $aciklama, $yonetmen, $cikis_yili, $bitis_yili, $tur, $afis_url, $slider_gorsel_url
        ]);
        
        // Log Tutma
        $log_sorgu = $db->prepare("INSERT INTO admin_loglari (admin_id, islem, detay) VALUES (?, ?, ?)");
        $log_sorgu->execute([
            $_SESSION['kullanici_id'], 
            "İçerik Eklendi", 
            "$baslik ($tip) sisteme eklendi."
        ]);

        // Başarılı Mesajı ve Yönlendirme
        $yeni_film_id = $db->lastInsertId();
        $mesaj = "'$baslik' başarıyla eklendi. Yönlendiriliyorsunuz...";
        $mesaj_tipi = "success";
        
        header("Refresh:2; url=film_duzenle.php?id=" . $yeni_film_id);

    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
        $mesaj_tipi = "danger";
    }
}
?>

<script>document.title = "Admin Paneli - İçerik Ekle";</script>

<div class="container mt-4 mb-5">
    <div class="admin-container">
        <h2>Yeni İçerik Ekle</h2>
        <p>Siteye yeni bir film veya dizi ekleyin.</p>
        <hr>

        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesaj_tipi; ?>"><?php echo $mesaj; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="row">
                <div class="col-md-9 mb-3">
                    <label for="baslik" class="form-label">Başlık</label>
                    <input type="text" class="form-control bg-dark text-white" id="baslik" name="baslik" required placeholder="Örn: Inception">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tip" class="form-label">Tip</label>
                    <select class="form-select bg-dark text-white" id="tip" name="tip" required>
                        <option value="film" selected>Film</option>
                        <option value="dizi">Dizi</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="aciklama" class="form-label">Açıklama / Özet</label>
                <textarea class="form-control bg-dark text-white" id="aciklama" name="aciklama" rows="4"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="yonetmen" class="form-label">Yönetmen</label>
                    <input type="text" class="form-control bg-dark text-white" id="yonetmen" name="yonetmen">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Başlangıç / Çıkış Yılı</label>
                    <input type="number" name="cikis_yili" class="form-control bg-dark text-white" required placeholder="Örn: 2022">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Bitiş Yılı (Opsiyonel)</label>
                    <input type="number" name="bitis_yili" class="form-control bg-dark text-white" placeholder="Devam ediyorsa boş bırak">
                    <small class="text-muted" style="font-size: 0.7rem;">Sadece bitmiş diziler için.</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="tur" class="form-label">Türler (Virgülle ayırın)</label>
                <input type="text" class="form-control bg-dark text-white" id="tur" name="tur" placeholder="örn: Aksiyon, Macera, Dram">
            </div>
            
            <div class="mb-3">
                <label for="afis_url" class="form-label">Afiş URL'si (Dikey Poster)</label>
                <input type="text" class="form-control bg-dark text-white" id="afis_url" name="afis_url" placeholder="https://.../poster.jpg">
            </div>

            <div class="mb-3">
                <label for="slider_gorsel_url" class="form-label">Slider Görsel URL'si (Yatay - Opsiyonel)</label>
                <input type="text" class="form-control bg-dark text-white" id="slider_gorsel_url" name="slider_gorsel_url" placeholder="https://.../banner.jpg">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="film_ekle" class="btn btn-success btn-lg">İçeriği Kaydet</button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>