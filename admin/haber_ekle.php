<?php
require 'admin_header.php'; 
$mesaj = "";
$mesaj_tipi = "";

// Haber Ekleme İşlemi
if (isset($_POST['haber_ekle'])) {
    $baslik = $_POST['baslik'];
    $resim_url = $_POST['resim_url'];
    $icerik = $_POST['icerik'];

    try {
        $sorgu = $db->prepare("INSERT INTO haberler (baslik, resim_url, icerik) VALUES (?, ?, ?)");
        $sorgu->execute([$baslik, $resim_url, $icerik]);
        
        $mesaj = "Haber başarıyla yayınlandı!";
        $mesaj_tipi = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
        $mesaj_tipi = "danger";
    }
}
?>
<script>document.title = "Admin Paneli - Haber Ekle";</script>

<div class="container mt-4">
    <div class="admin-container">
        <h2>Yeni Haber Ekle</h2>
        <p>Ana sayfadaki sağ blokta görünecek güncel haberleri buradan ekleyin.</p>
        <hr>

        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesaj_tipi; ?>"><?php echo $mesaj; ?></div>
        <?php endif; ?>

        <form action="haber_ekle.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Haber Başlığı</label>
                <input type="text" class="form-control" name="baslik" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Haber Görsel URL</label>
                <input type="text" class="form-control" name="resim_url" placeholder="https://.../haber.jpg">
            </div>
            
            <div class="mb-3">
                <label class="form-label">İçerik (Detay)</label>
                <textarea class="form-control" name="icerik" rows="6" required></textarea>
            </div>
            
            <button type="submit" name="haber_ekle" class="btn btn-primary">Haberi Yayınla</button>
        </form>
    </div>
</div>

<?php 
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>'; 
?>