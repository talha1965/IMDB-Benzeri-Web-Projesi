<?php 
include 'db.php'; // Veritabanı bağlantısı (Gerekirse)
include 'header.php';   // Menülerin gelmesi için
?>

<style>
    body {
        background-color: #000000; /* Tam siyah arka plan */
        color: #e0e0e0; /* Göz yormayan açık gri yazı */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('img/sinema-bg.jpg'); /* Arka plana film görseli koyabilirsin */
        background-size: cover;
        background-position: center;
        padding: 100px 0;
        border-bottom: 3px solid #E50914; /* Netflix Kırmızısı çizgi */
    }

    .section-title {
        color: #E50914; /* Kırmızı başlıklar */
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        border-left: 5px solid #E50914;
        padding-left: 15px;
    }

    .custom-card {
        background-color: #141414; /* Koyu gri kartlar */
        border: 1px solid #333;
        border-radius: 8px;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }

    .custom-card:hover {
        transform: translateY(-5px);
        border-color: #E50914; /* Üzerine gelince kırmızı sınır */
    }

    .highlight-text {
        color: #fff;
        font-weight: 600;
    }

    .tech-badge {
        background-color: #222;
        color: #E50914;
        border: 1px solid #E50914;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 5px;
    }

    /* Sosyal Medya Butonları */
    .social-btn {
        background-color: #E50914;
        color: #fff;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        margin: 0 5px;
        transition: background 0.3s;
    }
    
    .social-btn:hover {
        background-color: #b20710;
        color: #fff;
    }
</style>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold text-white">Hikayemiz Bir "Merhaba Dünya" ile Başladı</h1>
        <p class="lead mt-3" style="max-width: 700px; margin: 0 auto;">
            Sadece kod yazmıyoruz, sinema tutkusunu teknolojiyle birleştiriyoruz. 
            Eskişehir'den yükselen dijital bir sahne.
        </p>
    </div>
</div>

<div class="container mt-5 mb-5">
    
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="section-title">Vizyonumuz</h2>
            <p>
                Her şey, Eskişehir Osmangazi Üniversitesi bilgisayar laboratuvarında basit bir fikirle başladı: 
                <span class="highlight-text">"Neden kendi film arşivimizi oluşturmayalım?"</span>
            </p>
            <p>
                Bugün geldiğimiz noktada, sadece filmlerin listelendiği statik bir yapıdan öte; 
                kullanıcıların yorum yapabildiği, haberleri takip edebildiği ve sinema dünyasının nabzını tutan 
                dinamik bir platform geliştirdik. Amacımız, karmaşık algoritmaları kullanıcı dostu bir arayüzle sunarak 
                sinemaseverlere en iyi deneyimi yaşatmak.
            </p>
        </div>
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80" alt="Kodlama Ekranı" class="img-fluid rounded shadow border border-dark">
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="section-title">Sahne Arkasındaki Güç</h2>
            <p>Bu proje, modern web teknolojilerinin en güncel standartları kullanılarak inşa edilmiştir. Güvenlik, hız ve sürdürülebilirlik önceliğimizdir.</p>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card custom-card h-100 p-4">
                <h4 class="text-white">Backend Mimarisi</h4>
                <p class="small text-secondary">Güçlü ve Güvenli</p>
                <p class="text-white"f>PHP (PDO) kullanılarak geliştirilen altyapımız, SQL Injection saldırılarına karşı korumalıdır. Veri bütünlüğü bizim için her şeydir.</p>
                <div>
                    <span class="tech-badge">PHP 8.2</span>
                    <span class="tech-badge">MySQL</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card custom-card h-100 p-4">
                <h4 class="text-white">Frontend Tasarım</h4>
                <p class="small text-secondary">Estetik ve Modern</p>
                <p class="text-white">Kullanıcı deneyimini (UX) ön planda tutan, mobil uyumlu ve Netflix tarzı karanlık tema (Dark Mode) tasarımı.</p>
                <div>
                    <span class="tech-badge">Bootstrap 5</span>
                    <span class="tech-badge">CSS3</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card custom-card h-100 p-4">
                <h4 class="text-white">Yönetim Paneli</h4>
                <p class="small text-secondary">Tam Kontrol</p>
                <p class="text-white">İçeriklerin dinamik olarak yönetilebildiği, güvenli oturum (session) yönetimine sahip gelişmiş admin paneli.</p>
                <div>
                    <span class="tech-badge">AdminLTE</span>
                    <span class="tech-badge">CRUD</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center bg-dark p-5 rounded border border-secondary">
        <div class="col-md-8">
            <h3 class="text-white mb-3">Ekibimizle Tanışın</h3>
            <p class="text-white">
                Bu proje, <strong>Talha Kaya</strong> tarafından Bilgisayar Programcılığı bitirme projesi kapsamında geliştirilmiştir. 
                Sinema sanatına duyduğumuz saygıyı, kodlama sanatıyla birleştirmeye devam ediyoruz.
            </p>
            <div class="mt-4">
                <a href="#" class="social-btn" title="GitHub"><i class="bi bi-github">GH</i></a>
                <a href="#" class="social-btn" title="LinkedIn"><i class="bi bi-linkedin">LI</i></a>
                <a href="#" class="social-btn" title="Instagram"><i class="bi bi-instagram">IG</i></a>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div style="width: 150px; height: 150px; background-color: #333; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 3px solid #E50914;">
                <span style="font-size: 3rem; color: #fff;">TK</span>
            </div>
            <p class="mt-3 text-white fw-bold">Talha Kaya</p>
            <p class="text-secondary small">Full Stack Developer Adayı</p>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>