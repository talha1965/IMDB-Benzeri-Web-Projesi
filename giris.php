<?php
// Header'ı (menü, session, db) çek
require_once 'header.php';

// Kullanıcı zaten giriş yapmışsa, ana sayfaya yolla
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}

// Hata mesajı için boş bir değişken
$mesaj = "";

// Form gönderildiyse...
if (isset($_POST['giris_yap'])) {
    
    $email = $_POST['email'];
    $sifre_form = $_POST['sifre'];

    try {
        // E-postaya göre kullanıcıyı bul
        $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $sorgu->execute([$email]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı bulunduysa VE şifre (hash'li) doğruysa
        if ($kullanici && password_verify($sifre_form, $kullanici['sifre'])) {
            
            // Kullanıcı bilgilerini Session'a kaydet (giriş yap)
            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
            $_SESSION['rol'] = $kullanici['rol']; // Admin mi diye bilmemiz için
            
            // Ana sayfaya yönlendir
            header("Location: index.php");
            exit;
        } else {
            // Kullanıcı yoksa veya şifre yanlışsa
            $mesaj = "E-posta veya şifre hatalı!";
        }
    } catch (PDOException $e) {
        $mesaj = "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<script>document.title = "Giriş Yap - TKBD";</script>

<div class="row d-flex justify-content-center">
    <div class="col-lg-6 col-md-8">

        <div class="auth-container">
            <h2>Giriş Yap</h2>
            
            <?php if (!empty($mesaj)): ?>
                <div class="alert alert-danger">
                    <?php echo $mesaj; ?>
                </div>
            <?php endif; ?>

            <form action="giris.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta:</label>
                    <input type="email" id="email" name="email" class="form-control auth-form-input" required>
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" class="form-control auth-form-input" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="giris_yap" class="btn btn-danger btn-lg">Giriş Yap</button>
                </div>
            </form>
            
            <p class="auth-link">Hesabın yok mu? <a href="kayit.php">Kayıt Ol</a></p>
        </div>

    </div>
</div>

<?php
// Footer'ı (sayfanın alt kısmı) çek
require_once 'footer.php';
?>