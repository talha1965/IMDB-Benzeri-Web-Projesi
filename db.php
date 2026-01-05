<?php
// Veritabanı bağlantı ayarları
$sunucu = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani_adi = "imbd_proje"; // Projemizin veritabanı adı

try {
    // PDO ile veritabanına bağlanmayı dene
    $db = new PDO("mysql:host=$sunucu;dbname=$veritabani_adi;charset=utf8", $kullanici, $sifre);
    
    // Hata ayarını aç (bir sorun olursa PHP hata versin)
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Bağlantı başarılı!"; // Bağlantıyı test etmek için bu satırı açabilirsin
    
} catch (PDOException $e) {
    // Eğer bağlantı başarısız olursa, hatayı göster ve siteyi durdur.
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>