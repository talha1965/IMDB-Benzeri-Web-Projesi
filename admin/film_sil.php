<?php
session_start();
require '../db.php'; // Veritabanı bağlantısı

// Sadece adminler bu sayfayı çalıştırabilsin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    die("Bu işleme yetkiniz yok.");
}

// Silinecek filmin ID'si URL'den (GET) geldi mi?
if (isset($_GET['id'])) {
    
    // ID'yi güvenli olarak al (sadece sayı)
    $film_id = (int)$_GET['id'];

    try {
        // Filmi veritabanından sil
        $sorgu = $db->prepare("DELETE FROM filmler WHERE id = ?");
        $sorgu->execute([$film_id]);

        // İşlem bitince, filmlerin listelendiği sayfaya geri dön
        header("Location: filmleri_yonet.php");
        exit;

    } catch (PDOException $e) {
        // Eğer veritabanında bir hata olursa, ekrana yazdır
        die("Hata: " . $e->getMessage());
    }

} else {
    // Eğer URL'de bir ID yoksa, kullanıcıyı listeye geri gönder
    header("Location: filmleri_yonet.php");
    exit;
}
?>