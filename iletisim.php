<?php 
include 'db.php'; // Veritabanı bağlantısı
include 'header.php';   // Menülerin gelmesi için

// FORM GÖNDERİLDİ Mİ KONTROLÜ
if (isset($_POST['mesaj_gonder'])) {
    
    // Verileri güvenli şekilde al
    $ad = htmlspecialchars($_POST['ad']);
    $email = htmlspecialchars($_POST['email']);
    $konu = htmlspecialchars($_POST['konu']);
    $mesaj = htmlspecialchars($_POST['mesaj']);

    // Veritabanına kaydet
    $kaydet = $db->prepare("INSERT INTO mesajlar SET
        gonderen_ad = ?,
        email = ?,
        konu = ?,
        mesaj = ?
    ");
    $insert = $kaydet->execute([$ad, $email, $konu, $mesaj]);

    if ($insert) {
        $durum = "basarili";
    } else {
        $durum = "hata";
    }
}
?>

<style>
    body {
        background-color: #000;
        color: #fff;
    }
    .contact-card {
        background-color: #141414; /* Koyu gri kutu */
        border-radius: 10px;
        border: 1px solid #333;
        padding: 30px;
    }
    .form-control {
        background-color: #333;
        border: 1px solid #444;
        color: #fff;
    }
    .form-control:focus {
        background-color: #333;
        color: #fff;
        border-color: #E50914; /* Odaklanınca kırmızı */
        box-shadow: 0 0 5px rgba(229, 9, 20, 0.5);
    }
    .btn-red {
        background-color: #E50914;
        color: white;
        font-weight: bold;
        border: none;
        transition: all 0.3s;
    }
    .btn-red:hover {
        background-color: #b20710;
        color: white;
    }
    .icon-box {
        font-size: 1.5rem;
        color: #E50914;
        margin-right: 15px;
    }
    .contact-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        background-color: #1a1a1a;
        padding: 15px;
        border-radius: 8px;
    }
</style>

<div class="container mt-5 mb-5">
    
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 style="color: #E50914; font-weight: bold;">Bize Ulaşın</h2>
            <p class="text-secondary">Sorularınız, önerileriniz veya iş birlikleri için aşağıdaki formu kullanabilirsiniz.</p>
        </div>
    </div>

    <?php if(isset($durum) && $durum == "basarili") { ?>
        <div class="alert alert-success text-center">Mesajınız başarıyla gönderildi! En kısa sürede döneceğiz.</div>
    <?php } elseif(isset($durum) && $durum == "hata") { ?>
        <div class="alert alert-danger text-center">Mesaj gönderilirken bir hata oluştu.</div>
    <?php } ?>


    <div class="row">
        
        <div class="col-md-5 mb-4">
            <div class="contact-card h-100">
                <h4 class="mb-4 text-white">İletişim Kanalları</h4>
                
                <div class="contact-info-item">
                    <i class="bi bi-geo-alt-fill icon-box"></i>
                    <div>
                        <h6 class="mb-0 text-white">Adres</h6>
                        <small class="text-secondary">Eskişehir Osmangazi Üniversitesi, Meşelik Kampüsü, Eskişehir</small>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="bi bi-envelope-fill icon-box"></i>
                    <div>
                        <h6 class="mb-0 text-white">E-Posta</h6>
                        <small class="text-secondary">talhakaya@ogrenci.ogu.edu.tr</small>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="bi bi-clock-fill icon-box"></i>
                    <div>
                        <h6 class="mb-0 text-white">Yanıt Süresi</h6>
                        <small class="text-secondary">Mesajlarınıza genellikle 24 saat içinde dönüş yapıyoruz.</small>
                    </div>
                </div>

                <div class="mt-3 rounded overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3066.456637175446!2d30.4993!3d39.7305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cc3e6640397799%3A0x634d0b004273d40!2sEski%C5%9Fehir%20Osmangazi%20University!5e0!3m2!1sen!2str!4v1634567890123!5m2!1sen!2str" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="contact-card h-100">
                <h4 class="mb-4 text-white">Mesaj Gönderin</h4>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">Adınız Soyadınız</label>
                            <input type="text" name="ad" class="form-control" required placeholder="Talha Kaya">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">E-Posta Adresiniz</label>
                            <input type="email" name="email" class="form-control" required placeholder="ornek@mail.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Konu</label>
                        <select name="konu" class="form-control">
                            <option value="Genel">Genel Soru</option>
                            <option value="Öneri">Öneri / İstek</option>
                            <option value="Hata Bildirimi">Hata Bildirimi</option>
                            <option value="İşbirliği">İşbirliği</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Mesajınız</label>
                        <textarea name="mesaj" class="form-control" rows="5" required placeholder="Mesajınızı buraya yazın..."></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="gizlilik" required>
                        <label class="form-check-label text-secondary small" for="gizlilik">
                            Kişisel verilerimin işlenmesini kabul ediyorum. Bilgileriniz 3. şahıslarla paylaşılmaz.
                        </label>
                    </div>

                    <button type="submit" name="mesaj_gonder" class="btn btn-red w-100 py-2">
                        <i class="bi bi-send-fill me-2"></i> GÖNDER
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>