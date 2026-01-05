</div> <footer class="main-footer text-center">
    <div class="container">
        
        <div class="footer-social">
            <a href="#" title="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" title="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
        
        <div class="footer-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="hakkimizda.php">Hakkımızda</a>
            <a href="iletisim.php">İletişim</a>
            <a href="#">Kullanım Şartları</a>
        </div>
        
        <div class="footer-copyright">
            <p>&copy; <?php echo date("Y"); ?> TKBD. Tüm hakları saklıdır.<br></p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
  var swiper = new Swiper(".horizontal-swiper", {
    // Varsayılan (Mobil) ayarlar
    slidesPerView: 1.5, // Mobilde 1 tam kart + yarım kart göster (Kaydırılabileceği anlaşılsın)
    spaceBetween: 15,   // Kartlar arası boşluk
    
    // Oklar
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    
    // Ekran boyutuna göre kart sayısını ayarla (Responsive)
    breakpoints: {
      // Tablet (576px ve üzeri)
      576: {
        slidesPerView: 2.5, // 2 tam + yarım
        spaceBetween: 20,
      },
      // Orta ekran (768px ve üzeri)
      768: {
        slidesPerView: 3.5, // 3 tam + yarım
        spaceBetween: 20,
      },
      // Geniş ekran (992px ve üzeri)
      992: {
        slidesPerView: 4.5, // 4 tam + yarım
        spaceBetween: 20,
      },
      // Çok geniş ekran (1200px ve üzeri)
      1200: {
        slidesPerView: 5.5, // 5 tam + yarım
        spaceBetween: 20,
      },
    },
  });
</script>
</body>
</html>