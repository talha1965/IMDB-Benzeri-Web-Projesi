<?php
session_start();
require 'db.php'; // Veritabanı

// Bu sayfayı sadece adminler kullanabilir
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    die("Bu işleme yetkiniz yok.");
}

// Gerekli ID'ler (silinecek yorumun ID'si ve geri dönülecek film ID'si) geldi mi?
if (isset($_GET['id']) && isset($_GET['film_id'])) {
    
    $yorum_id = (int)$_GET['id'];
    $film_id = (int)$_GET['film_id']; // Yorumu sildikten sonra bu filme geri döneceğiz

    try {
        // Yorumu 'yorumlar' tablosundan sil
        $sorgu = $db->prepare("DELETE FROM yorumlar WHERE id = ?");
        $sorgu->execute([$yorum_id]);

        $log_sorgu =$db->prepare("INSERT INTO admin_loglari (admin_id, islem, detay) VALUES (?, ?, ?)");
        $log_sorgu->execute([
            $_SESSION['kullanici_id'],
            "Yorum silindi.",
            "ID: $yorum_id olan yorum, film ID: $film_id_redirect sayfasından silindi."
        ]);
        

        // İşlem bitince, admini filmin detay sayfasına geri yolla
        header("Location: detay.php?id=" . $film_id_redirect);
        exit;

    } catch (PDOException $e) {
        // Bir hata olursa ekrana yazdır
        die("Hata: " . $e->getMessage());
    }

} else {
    // Eğer URL'de ID'ler eksikse, ana sayfaya at
    header("Location: index.php");
    exit;
}
?>