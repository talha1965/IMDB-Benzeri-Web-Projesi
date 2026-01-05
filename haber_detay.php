<?php
require_once 'header.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $sorgu = $db->prepare("SELECT * FROM haberler WHERE id = ?");
        $sorgu->execute([$id]);
        $haber = $sorgu->fetch(PDO::FETCH_ASSOC);

        if (!$haber) {
            echo "<div class='container mt-5'><div class='alert alert-danger'>Haber bulunamadı.</div></div>";
            require_once 'footer.php';
            exit;
        }
    } catch (PDOException $e) {
        die("Hata: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold text-white mb-3"><?php echo htmlspecialchars($haber['baslik']); ?></h1>
            
            <p style="color: #adb5bd;">
                <i class="bi bi-calendar3 me-2"></i>
                <?php echo date('d.m.Y H:i', strtotime($haber['tarih'])); ?>
            </p>
            
            <?php if (!empty($haber['resim_url'])): ?>
                <div class="mb-4">
                    <img src="<?php echo htmlspecialchars($haber['resim_url']); ?>" 
                         class="img-fluid rounded shadow-lg w-100" 
                         alt="Haber Görseli" 
                         style="max-height: 500px; object-fit: cover;">
                </div>
            <?php endif; ?>
            
            <div class="text-light lh-lg fs-5">
                <?php echo nl2br(htmlspecialchars($haber['icerik'])); ?>
            </div>
            
            <hr class="my-5 border-secondary">
            
            <a href="index.php" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Ana Sayfaya Dön</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>