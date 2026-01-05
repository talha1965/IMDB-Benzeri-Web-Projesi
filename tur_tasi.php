<?php
// Veritabanı bağlantısı
require 'db.php';

echo "<h1>Veri Taşıma İşlemi Başladı...</h1>";

try {
    // 1. Tüm filmleri ve eski 'tur' sütununu çek
    $sorgu = $db->query("SELECT id, baslik, tur FROM filmler WHERE tur IS NOT NULL AND tur != ''");
    $filmler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    $eklenen_tur_sayisi = 0;
    $eklenen_iliski_sayisi = 0;

    foreach ($filmler as $film) {
        $film_id = $film['id'];
        $eski_tur_yazisi = $film['tur']; // Örn: "Aksiyon, Macera, Dram"

        // Virgüllere göre parçala
        $turler_dizisi = explode(',', $eski_tur_yazisi);

        foreach ($turler_dizisi as $tur_adi) {
            $tur_adi = trim($tur_adi); // Baştaki/sondaki boşlukları sil
            
            if (empty($tur_adi)) continue;

            // A) Bu tür 'turler' tablosunda var mı? Yoksa ekle.
            // Önce ID'sini bulmaya çalış
            $tur_kontrol = $db->prepare("SELECT id FROM turler WHERE tur_adi = ?");
            $tur_kontrol->execute([$tur_adi]);
            $mevcut_tur = $tur_kontrol->fetch(PDO::FETCH_ASSOC);

            if ($mevcut_tur) {
                $tur_id = $mevcut_tur['id'];
            } else {
                // Yoksa yeni ekle
                $tur_ekle = $db->prepare("INSERT INTO turler (tur_adi) VALUES (?)");
                $tur_ekle->execute([$tur_adi]);
                $tur_id = $db->lastInsertId();
                $eklenen_tur_sayisi++;
                echo "Yeni Tür Eklendi: <strong>$tur_adi</strong><br>";
            }

            // B) İlişkiyi Kur (Hangi film, hangi türe ait)
            // INSERT IGNORE kullanarak, daha önce eklenmişse hata vermesini engelliyoruz
            $iliski_ekle = $db->prepare("INSERT IGNORE INTO film_tur_iliskisi (film_id, tur_id) VALUES (?, ?)");
            $iliski_ekle->execute([$film_id, $tur_id]);
            
            if ($iliski_ekle->rowCount() > 0) {
                $eklenen_iliski_sayisi++;
            }
        }
    }

    echo "<hr>";
    echo "<h3>İşlem Tamamlandı!</h3>";
    echo "Toplam yeni oluşturulan tür: $eklenen_tur_sayisi <br>";
    echo "Toplam kurulan ilişki: $eklenen_iliski_sayisi <br>";
    echo "<br><a href='index.php'>Admin Paneline Dön</a>";

} catch (PDOException $e) {
    die("Hata oluştu: " . $e->getMessage());
}
?>