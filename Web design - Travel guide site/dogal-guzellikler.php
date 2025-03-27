<?php
include 'includes/header.php';
?>

<main class="destinations-page">
    <section class="page-hero"
        style="background-image: url('/turkiyegezirehberi/img/dogal-guzellikler/header.jpg');">
        <div class="hero-overlay">
            <h1>Doğal Güzellikler</h1>
            <p>Türkiye'nin nefes kesen doğal güzelliklerini keşfedin.</p>
        </div>
    </section>

    <div class="destinations-grid">
        <aside class="filter-sidebar">
            <div class="search-box">
                <h3>Doğal Güzellik Ara</h3>
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Ara..">
                </div>
            </div>

            <div class="filter-section">
                <h3>Bölgeler</h3>
                <div class="filter-options">
                    <label><input type="checkbox" value="Marmara"> Marmara</label>
                    <label><input type="checkbox" value="Ege"> Ege</label>
                    <label><input type="checkbox" value="Akdeniz"> Akdeniz</label>
                    <label><input type="checkbox" value="Karadeniz"> Karadeniz</label>
                    <label><input type="checkbox" value="İç Anadolu"> İç Anadolu</label>
                    <label><input type="checkbox" value="Doğu Anadolu"> Doğu Anadolu</label>
                    <label><input type="checkbox" value="Güneydoğu Anadolu"> Güneydoğu Anadolu</label>
                </div>
            </div>
        </aside>

        <div class="destinations-content">
            <div class="destinations-list">

                <!-- 1. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/pamukkale.jpg" alt="Pamukkale">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Pamukkale Travertenleri</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Denizli</span>
                        </div>
                        <p>Beyaz travertenlerden oluşan doğal terasları ve şifalı termal suları ile dünyaca ünlü UNESCO
                            Dünya Mirası.</p>
                        <a href="pages/dogal-guzellikler/pamukkale-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 2. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/uzungol.jpg" alt="Uzungöl">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Uzungöl</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Trabzon</span>
                        </div>
                        <p>Dağların arasında saklı, eşsiz doğal güzelliği ve geleneksel mimarisiyle ünlü göl ve çevresi.
                        </p>
                        <a href="pages/dogal-guzellikler/uzungol-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 3. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/ihlara.webp" alt="Ihlara Vadisi">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ihlara Vadisi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Aksaray</span>
                        </div>
                        <p>14 km uzunluğunda, tarihi kiliseleri ve doğal güzellikleriyle büyüleyen kanyon vadisi.</p>
                        <a href="pages/dogal-guzellikler/ihlara-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 4. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/saklikent.jpg"
                            alt="Saklıkent Kanyonu">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Saklıkent Kanyonu</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Muğla</span>
                        </div>
                        <p>Türkiye'nin en uzun ikinci kanyonu, doğa sporları ve eşsiz manzaralarıyla ünlü.</p>
                        <a href="pages/dogal-guzellikler/saklikent-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 5. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/salda.webp" alt="Salda Gölü">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Salda Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Burdur</span>
                        </div>
                        <p>Turkuaz rengi suları ve beyaz kumsallarıyla 'Türkiye'nin Maldivleri' olarak bilinen krater
                            gölü.
                        </p>
                        <a href="pages/dogal-guzellikler/salda-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 6. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/ayder.webp" alt="Ayder Yaylası">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ayder Yaylası</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Rize</span>
                        </div>
                        <p>Yeşilin her tonunu barındıran, şifalı kaplıcaları ve geleneksel yayla evleriyle ünlü doğal
                            cennet.</p>
                        <a href="pages/dogal-guzellikler/ayder-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 7. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/nemrut.jpg"
                            alt="Nemrut Krater Gölü">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Nemrut Krater Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Bitlis</span>
                        </div>
                        <p>Dünyanın en büyük krater göllerinden biri, eşsiz manzarası ve doğal yaşamıyla dikkat çekiyor.
                        </p>
                        <a href="pages/dogal-guzellikler/nemrut-krater-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 8. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/duden.webp" alt="Düden Şelalesi">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Düden Şelalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Antalya</span>
                        </div>
                        <p>Denize dökülen muhteşem şelalesi ve doğal parkıyla Antalya'nın en önemli doğal
                            güzelliklerinden.
                        </p>
                        <a href="pages/dogal-guzellikler/duden-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 9. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/meke.jpg" alt="Meke Gölü">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Meke Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Konya</span>
                        </div>
                        <p>Volkanik bir krater gölü olan Meke, ortasındaki adası ve benzersiz yapısıyla 'Dünyanın Nazar
                            Boncuğu' olarak anılıyor.</p>
                        <a href="pages/dogal-guzellikler/meke-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 10. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/abant.jpg" alt="Abant Gölü">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Abant Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Bolu</span>
                        </div>
                        <p>Dört mevsim farklı güzelliklere bürünen, çam ormanlarıyla çevrili doğal göl.</p>
                        <a href="pages/dogal-guzellikler/abant-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 11. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/karcal.jpg" alt="Karçal Dağları">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Karçal Dağları</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Artvin</span>
                        </div>
                        <p>Yaban hayatı ve endemik bitki türleriyle öne çıkan, dağcılık için ideal doğal yaşam alanı.
                        </p>
                        <a href="pages/dogal-guzellikler/karcal-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 12. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/borcka.webp" alt="Karagöl">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Borçka Karagöl</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Artvin</span>
                        </div>
                        <p>Heyelan sonucu oluşan, etrafı ladin ve köknar ormanlarıyla çevrili doğal göl.</p>
                        <a href="pages/dogal-guzellikler/karagol-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 13. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/palandoken.jpg" alt="Palandöken Dağı">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Palandöken Dağı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Erzurum</span>
                        </div>
                        <p>Türkiye'nin en önemli kış sporları merkezlerinden biri, muhteşem kayak pistleri ve doğal
                            güzelliği ile ünlü.</p>
                        <a href="pages/dogal-guzellikler/palandoken-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 14. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/olimpos.webp" alt="Olimpos">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Olimpos</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Antalya</span>
                        </div>
                        <p>Antik kent kalıntıları, doğal plajı ve sönmeyen ateşi Yanartaş ile ünlü doğal ve tarihi
                            güzellik.
                        </p>
                        <a href="pages/dogal-guzellikler/olimpos-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 15. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/beysehir.jpg" alt="Beyşehir Gölü">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Beyşehir Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Beyşehir, Konya</span>
                        </div>
                        <p>Türkiye'nin 2. en büyük gölü olma özelliğine sahip bu eşsiz göl, yüzlere kuş türüne ev
                            sahipliği yapıyor.</p>
                        <a href="pages/dogal-guzellikler/beysehir-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 16. Doğal Güzellik -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="/turkiyegezirehberi/img/dogal-guzellikler/sapanca.jpg" alt="Sapanca Gölü">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Sapanca Gölü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Sapanca, Sakarya</span>
                        </div>
                        <p>Marmara bölgesine bir doğal cenneti yaşatan, huzurun buluşma noktası olan doğal güzellik. Sakarya'da bulunan Sapanca gölü güzelliğiyle göz kamaştırıyor.</p>
                        <a href="pages/dogal-guzellikler/sapanca-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>




            </div>

            <!-- Sayfalama -->
            <div class="pagination-container">
                <div class="pagination">
                    <button class="page-btn prev-btn">
                        <i class="fas fa-chevron-left"></i>
                        Önceki
                    </button>

                    <button class="page-btn next-btn">
                        Sonraki
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="js/pagination.js"></script>

<script src="js/filter.js"></script>

<?php include 'includes/footer.php'; ?>