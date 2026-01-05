<?php
session_start();
require '../db.php';

// Güvenlik
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') { die("Yetkisiz işlem."); }

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $sorgu = $db->prepare("DELETE FROM haberler WHERE id = ?");
        $sorgu->execute([$id]);
        
        // Log tutalım
        $log = $db->prepare("INSERT INTO admin_loglari (admin_id, islem, detay) VALUES (?, ?, ?)");
        $log->execute([$_SESSION['kullanici_id'], "Haber Silindi", "Haber ID: $id silindi."]);

        header("Location: haberleri_yonet.php");
    } catch (PDOException $e) {
        die("Hata: " . $e->getMessage());
    }
} else {
    header("Location: haberleri_yonet.php");
}
?>