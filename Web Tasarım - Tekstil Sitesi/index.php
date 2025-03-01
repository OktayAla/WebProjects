<?php require_once 'includes/header.php'; ?>


<style>
/* Quality Section Responsive Styling */
.quality-items {
    display: flex;
    flex-wrap: wrap; /* Öğelerin sığmadığında alt satıra geçmesini sağlar */
    gap: 2rem; /* Öğeler arasında boşluk bırakır */
    justify-content: center; /* Öğeleri yatayda ortalar */
}

.quality-item {
    flex: 1 1 calc(50% - 2rem); /* İki öğenin yan yana gelmesini sağlar */
    min-width: 280px; /* Minimum genişlik belirler */
}

@media (max-width: 768px) {
    .quality-item {
        flex: 1 1 100%; /* Mobil cihazlarda her öğe tam genişlikte olsun */
    }
}
</style>

<!-- Hero bölümü -->
<div class="hero-section d-flex align-items-center" data-aos="fade-up" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
            url('img/index/index_header.jpg') no-repeat center center/cover;">
    <div class="container text-center">
        <h1>ELEGANCE TEXTILE</h1>
        <p class="lead mb-4">Kalite ve Zarafetin Buluşma Noktası</p>
        <div class="hero-buttons">
            <a href="collections.php" class="btn btn-custom me-3">Koleksiyonlarımız</a>
            <a href="contact.php" class="btn btn-outline-light">İletişime Geçin</a>
        </div>
    </div>
</div>

<!-- Koleksiyonlar bölümü -->
<section id="collections" class="py-5">
    <div class="container">
        <h2 class="section-title text-center" data-aos="fade-up">Koleksiyonlarımız</h2>
        <div class="collection-grid">
            <?php
            $collections = [
                ['name' => 'Jakar Dokuma', 'image' => 'img/index/1_jakardokuma.jpg', 'link' => 'https://test.kodkampusu.com/elegancetextile/collections.php'],
                ['name' => 'Pamuklu Bornoz', 'image' => 'img/index/2_pamuklubornoz.jpg', 'link' => 'https://test.kodkampusu.com/elegancetextile/collections.php'],
                ['name' => 'Bambu Kumaş', 'image' => 'img/index/3_bambukumas.jpg', 'link' => 'https://test.kodkampusu.com/elegancetextile/collections.php']
            ];

            foreach ($collections as $collection): ?>
                <div class="product-card" data-aos="fade-up" data-aos-delay="100">
                    <a href="<?php echo $collection['link']; ?>">
                        <img src="<?php echo $collection['image']; ?>" class="card-img-top"
                            alt="<?php echo $collection['name']; ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo $collection['name']; ?></h5>
                            <span class="btn btn-custom btn-sm">İncele</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Firma hakkında neden biz -->
<section id="features" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Neden Biz?</h2>
        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up">
                <div class="feature-card text-center p-4">
                    <i class="bi bi-award-fill fs-1 text-accent mb-3"></i>
                    <h4>Premium Kalite</h4>
                    <p>40 yıllık tecrübe ile en yüksek kalite standartlarında üretim</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center p-4">
                    <i class="bi bi-globe2 fs-1 text-accent mb-3"></i>
                    <h4>Global Erişim</h4>
                    <p>50+ ülkeye ihracat ve dünya çapında distribütör ağı</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center p-4">
                    <i class="bi bi-tree-fill fs-1 text-accent mb-3"></i>
                    <h4>Sürdürülebilirlik</h4>
                    <p>%100 çevre dostu üretim süreçleri ve geri dönüşüm</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center p-4">
                    <i class="bi bi-patch-check-fill fs-1 text-accent mb-3"></i>
                    <h4>Sertifikasyon</h4>
                    <p>Uluslararası kalite sertifikaları ve standartlar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kalite Standartları Bölümü -->
<section id="quality" class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Kalite Standartlarımız</h2>
        <div class="quality-content">
            <div class="quality-items">
                <div class="quality-item d-flex align-items-start" data-aos="fade-right">
                    <div class="quality-icon me-3">
                        <i class="bi bi-shield-check text-accent fs-3"></i>
                    </div>
                    <div class="quality-info">
                        <h4 class="h5 mb-2">ISO 9001:2015 Sertifikası</h4>
                        <p class="mb-0">Kalite yönetim sistemimiz, uluslararası standartlara uygun olarak denetlenmekte
                            ve belgelendirilmektedir.</p>
                    </div>
                </div>

                <div class="quality-item d-flex align-items-start" data-aos="fade-left">
                    <div class="quality-icon me-3">
                        <i class="bi bi-check-circle text-accent fs-3"></i>
                    </div>
                    <div class="quality-info">
                        <h4 class="h5 mb-2">GOTS Sertifikası</h4>
                        <p class="mb-0">Global Organik Tekstil Standardı ile organik üretim süreçlerimiz
                            belgelendirilmektedir.</p>
                    </div>
                </div>

                <div class="quality-item d-flex align-items-start" data-aos="fade-right">
                    <div class="quality-icon me-3">
                        <i class="bi bi-award text-accent fs-3"></i>
                    </div>
                    <div class="quality-info">
                        <h4 class="h5 mb-2">OEKO-TEX® Standard 100</h4>
                        <p class="mb-0">Tüm ürünlerimiz, insan sağlığına zarar vermeyen tekstil standardı sertifikasına
                            sahiptir.</p>
                    </div>
                </div>

                <div class="quality-item d-flex align-items-start" data-aos="fade-left">
                    <div class="quality-icon me-3">
                        <i class="bi bi-graph-up text-accent fs-3"></i>
                    </div>
                    <div class="quality-info">
                        <h4 class="h5 mb-2">Kalite Kontrol Laboratuvarı</h4>
                        <p class="mb-0">Modern test ekipmanları ile sürekli kalite kontrolü ve AR-GE çalışmaları
                            yürütülmektedir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>