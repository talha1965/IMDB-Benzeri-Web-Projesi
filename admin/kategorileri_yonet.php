<?php
require 'admin_header.php'; 

$mesaj = "";
$mesaj_tipi = "";

// 1. Kategori Ekleme
if (isset($_POST['kategori_ekle'])) {
    
    $kategori_adi = trim($_POST['kategori_adi']);
    $sira = (int)$_POST['sira'];
    
    if (!empty($kategori_adi)) {
        try {
            $sorgu = $db->prepare("INSERT INTO kategoriler (kategori_adi, sira) VALUES (?, ?)");
            $sorgu->execute([$kategori_adi, $sira]);
            
            $mesaj = "'$kategori_adi' kategorisi başarıyla eklendi.";
            $mesaj_tipi = "success";
            
        } catch (PDOException $e) {
            $mesaj = "Hata: " . $e->getMessage();
            $mesaj_tipi = "danger";
        }
    } else {
        $mesaj = "Kategori adı boş olamaz.";
        $mesaj_tipi = "warning";
    }
}

// 2. Kategori Silme
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    try {
        $sorgu = $db->prepare("DELETE FROM kategoriler WHERE id = ?");
        $sorgu->execute([$sil_id]);
        
        $mesaj = "Kategori başarıyla silindi.";
        $mesaj_tipi = "success";
        
    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
        $mesaj_tipi = "danger";
    }
}

// 3. Listeleme
try {
    $kategoriler_sorgusu = $db->prepare("SELECT * FROM kategoriler ORDER BY sira ASC");
    $kategoriler_sorgusu->execute();
    $kategoriler = $kategoriler_sorgusu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kategoriler çekilirken hata: " . $e->getMessage());
}
?>

<script>document.title = "Admin Paneli - Kategorileri Yönet";</script>

<div class="container mt-4">
    <div class="admin-container">
        <h2>Kategorileri Yönet</h2>
        <p>Ana sayfada görünecek içerik sıralarını (kategorileri) buradan yönetin.</p>
        <hr>

        <?php if (!empty($mesaj)): ?>
            <div class="alert alert-<?php echo $mesaj_tipi; ?>"><?php echo $mesaj; ?></div>
        <?php endif; ?>

        <div class="card mb-4 dashboard-card"> 
            <div class="card-body">
                <h5 class="card-title">Yeni Kategori Oluştur</h5>
                <form action="kategorileri_yonet.php" method="POST" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="kategori_adi" class="form-label text-white">Kategori Adı</label>
                        <input type="text" class="form-control" id="kategori_adi" name="kategori_adi" placeholder="örn: En İyi Filmler" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sira" class="form-label text-white">Sıra No</label>
                        <input type="number" class="form-control" id="sira" name="sira" value="0" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="kategori_ekle" class="btn btn-primary w-100">Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
        
        <h4>Mevcut Kategoriler</h4>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover border-secondary">
                <thead>
                    <tr>
                        <th>Sıra</th>
                        <th>Kategori Adı</th>
                        <th>ID</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategoriler)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Henüz hiç kategori oluşturulmamış.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kategoriler as $kategori): ?>
                            <tr>
                                <td><?php echo $kategori['sira']; ?></td>
                                <td><?php echo htmlspecialchars($kategori['kategori_adi']); ?></td>
                                <td><?php echo $kategori['id']; ?></td>
                                <td>
                                    <a href="kategorileri_yonet.php?sil_id=<?php echo $kategori['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu kategoriyi silmek istediğine emin misin?');">
                                        <i class="bi bi-trash-fill"></i> Sil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>