<?php
session_start();
require 'db.php'; // Veritabanı

// Önce giriş yapmış mı diye kontrol et
if (!isset($_SESSION['kullanici_id'])) {
    die("Bu işlem için giriş yapmalısınız.");
}

// Gerekli bilgiler URL'den geldi mi? (film_id ve action=ekle/kaldir)
if (isset($_GET['film_id']) && isset($_GET['action'])) {
    
    // Gelen verileri al
    $film_id = (int)$_GET['film_id'];
    $action = $_GET['action'];
    $kullanici_id = $_SESSION['kullanici_id'];

    try {
        if ($action == 'ekle') {
            // Listeye ekle (IGNORE sayesinde, film zaten listedeyse hata vermez)
            $sorgu = $db->prepare("INSERT IGNORE INTO watchlist (kullanici_id, film_id) VALUES (?, ?)");
            $sorgu->execute([$kullanici_id, $film_id]);

        } elseif ($action == 'kaldir') {
            // Listeden sil
            $sorgu = $db->prepare("DELETE FROM watchlist WHERE kullanici_id = ? AND film_id = ?");
            $sorgu->execute([$kullanici_id, $film_id]);
        }

        // İşlem bitince, kullanıcıyı geldiği film sayfasına geri gönder
        header("Location: detay.php?id=" . $film_id);
        exit;

    } catch (PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }

} else {
    // URL'de eksik bilgi varsa ana sayfaya at
    header("Location: index.php");
    exit;
}
?>