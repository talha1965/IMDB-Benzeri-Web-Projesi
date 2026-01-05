<?php
require 'admin_header.php'; 

$mesaj = "";
$mesaj_tipi = "";

// 1. HABER GÜNCELLEME İŞLEMİ (Formdan veri geldiyse)
if (isset($_POST['haber_guncelle'])) {
    $id = (int)$_POST['id'];
    $baslik = $_POST['baslik'];
    $resim_url = $_POST['resim_url'];
    $icerik = $_POST['icerik'];

    try {
        $guncelle = $db->prepare("UPDATE haberler SET baslik = ?, resim_url = ?, icerik = ? WHERE id = ?");
        $guncelle->execute([$baslik, $resim_url, $icerik, $id]);
        
        $mesaj = "Haber başarıyla güncellendi.";
        $mesaj_tipi = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
        $mesaj_tipi = "danger";
    }
}

// 2. HABERLERİ LİSTELEME
try {
    $sorgu = $db->prepare("SELECT * FROM haberler ORDER BY tarih DESC");
    $sorgu->execute();
    $haberler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<script>document.title = "Admin Paneli - Haberleri Yönet";</script>

<div class="container mt-4">
    <div class="admin-container">
        <h2>Haberleri Yönet</h2>
        <p>Sistemdeki haberleri düzenleyin veya silin.</p>
        <hr>

        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesaj_tipi; ?> alert-dismissible fade show" role="alert">
                <?php echo $mesaj; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover border-secondary align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 100px;">Görsel</th>
                        <th>Başlık</th>
                        <th style="width: 150px;">Tarih</th>
                        <th style="width: 200px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($haberler)): ?>
                        <tr><td colspan="5" class="text-center">Henüz hiç haber yok.</td></tr>
                    <?php else: ?>
                        <?php foreach ($haberler as $haber): ?>
                            <tr>
                                <td><?php echo $haber['id']; ?></td>
                                <td>
                                    <?php if(!empty($haber['resim_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($haber['resim_url']); ?>" style="height: 50px; width: 80px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <span class="">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($haber['baslik']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($haber['tarih'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $haber['id']; ?>">
                                        <i class="bi bi-pencil-square"></i> Düzenle
                                    </button>

                                    <a href="haber_sil.php?id=<?php echo $haber['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu haberi silmek istediğinize emin misiniz?');">
                                        <i class="bi bi-trash-fill"></i> Sil
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal<?php echo $haber['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $haber['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg"> <div class="modal-content bg-dark text-white border-secondary">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title" id="modalLabel<?php echo $haber['id']; ?>">Haberi Düzenle: #<?php echo $haber['id']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                        </div>
                                        <form action="haberleri_yonet.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $haber['id']; ?>">

                                                <div class="mb-3">
                                                    <label class="form-label text-white-50">Başlık</label>
                                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="baslik" value="<?php echo htmlspecialchars($haber['baslik']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-white-50">Görsel URL</label>
                                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="resim_url" value="<?php echo htmlspecialchars($haber['resim_url']); ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-white-50">İçerik</label>
                                                    <textarea class="form-control bg-dark text-white border-secondary" name="icerik" rows="6" required><?php echo htmlspecialchars($haber['icerik']); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                <button type="submit" name="haber_guncelle" class="btn btn-primary">Kaydet ve Güncelle</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>'; 
?>