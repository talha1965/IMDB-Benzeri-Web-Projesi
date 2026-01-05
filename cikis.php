<?php
// Oturumu sonlandırabilmek için önce başlatmamız gerekiyor
session_start();

// Session içindeki tüm değişkenleri (kullanici_id, rol vb.) temizle
session_unset();

// Oturumu tamamen yok et
session_destroy();

// Artık çıkış yapıldığına göre, kullanıcıyı giriş sayfasına geri yolla
header("Location: giris.php");
exit; // Yönlendirmeden sonra kodun durduğundan emin ol
?>