<?php
// Header'ı (menü, session, db) çek
require_once 'header.php';

// Kullanıcı zaten giriş yapmışsa, ana sayfaya yolla
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}

// Hata/başarı mesajı için boş değişken
$mesaj = "";

// 'Kayıt Ol' butonuna basıldıysa...
if (isset($_POST['kayit_ol'])) {
    
    $kullanici_adi = $_POST['kullanici_adi'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];
    
    // Güvenlik için şifreyi HER ZAMAN hash'le
    $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);

    try {
        // Bu e-posta adresi daha önce alınmış mı diye kontrol et
        $kontrol_sorgusu = $db->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $kontrol_sorgusu->execute([$email]);
        
        if ($kontrol_sorgusu->rowCount() > 0) {
            // E-posta varsa
            $mesaj = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            // E-posta yoksa, yeni kullanıcıyı kaydet
            $insert_sorgusu = $db->prepare("INSERT INTO kullanicilar (kullanici_adi, email, sifre) VALUES (?, ?, ?)");
            $insert_sorgusu->execute([$kullanici_adi, $email, $hashli_sifre]);
            $mesaj = "Kayıt başarılı! Lütfen giriş yapın.";
        }
    } catch (PDOException $e) {
        $mesaj = "Bir hata oluştu: " . $e->getMessage();
    }
}
?>

<script>document.title = "Kayıt Ol - TKBD";</script>

<div class="row d-flex justify-content-center">
    <div class="col-lg-6 col-md-8">
        
        <div class="auth-container">
            <h2>Kayıt Ol</h2>
            <p class="text-center mb-4">Sayfamıza kayıt olmak için formu doldurun.</p>

            <?php if (!empty($mesaj)): ?>
                <div class="alert <?php echo ($mesaj == 'Kayıt başarılı! Lütfen giriş yapın.') ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $mesaj; ?>
                </div>
            <?php endif; ?>

            <form action="kayit.php" method="POST">
                <div class="mb-3">
                    <label for="kullanici_adi" class="form-label">Kullanıcı Adı:</label>
                    <input type="text" id="kullanici_adi" name="kullanici_adi" class="form-control auth-form-input" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta:</label>
                    <input type="email" id="email" name="email" class="form-control auth-form-input" required>
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" class="form-control auth-form-input" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="kayit_ol" class="btn btn-danger btn-lg">Kayıt Ol</button>
                </div>
            </form>
            
            <p class="auth-link">Zaten bir hesabın var mı? <a href="giris.php">Giriş Yap</a></p>
        </div>

    </div>
</div>

<?php
// Footer'ı (sayfanın alt kısmı) çek
require_once 'footer.php';
?>