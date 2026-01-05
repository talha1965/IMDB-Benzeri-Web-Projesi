<?php
// Yorum eklemek için session'ı başlat ve db'ye bağlan
session_start();
require 'db.php';

// Kullanıcı giriş yapmamışsa, bu sayfada işi yok
if (!isset($_SESSION['kullanici_id'])) {
    die("Yorum yapmak için giriş yapmalısınız.");
}

// Form 'POST' metoduyla mı gönderildi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Formdan gelen verileri alalım
    $yorum_metni = $_POST['yorum_metni'];
    $film_id = (int)$_POST['film_id'];
    $puan = (int)$_POST['puan'];
    
    // Yorumu yapanın ID'sini session'dan al
    $kullanici_id = $_SESSION['kullanici_id'];
    
    // Basit bir kontrol, yorum boş mu?
    if (empty(trim($yorum_metni))) {
        die("Yorum metni boş olamaz.");
    }
    // Puan 1-10 arasında mı?
    if ($puan < 1 || $puan > 10) {
        die("Lütfen 1 ile 10 arasında geçerli bir puan seçin.");
    }

    // Veritabanına kaydetmeyi dene
    try {
        // Yorumu ve puanı 'yorumlar' tablosuna ekle
        $sorgu = $db->prepare("INSERT INTO yorumlar (kullanici_id, film_id, yorum_metni, puan) 
                               VALUES (?, ?, ?, ?)");
        $sorgu->execute([$kullanici_id, $film_id, $yorum_metni, $puan]);

        // İş bittikten sonra, kullanıcının geldiği film sayfasına geri dön
        header("Location: detay.php?id=" . $film_id);
        exit;

    } catch (PDOException $e) {
        // (Not: İleride bir kullanıcı aynı filme iki kez yorum yapamasın diye 
        //  veritabanına 'UNIQUE' kuralı ekleyip burayı güncellemek gerekebilir)
        die("Veritabanı hatası: " . $e->getMessage());
    }

} else {
    // Eğer bu sayfaya POST ile gelinmediyse (direkt URL yazıldıysa)
    // ana sayfaya at
    header("Location: index.php");
    exit;
}
?>