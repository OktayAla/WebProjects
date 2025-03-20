<?php
include 'includes/header.php';
?>

<main class="destinations-page">
    <section class="page-hero"
        style="background-image: url('https://sozcuo01.sozcucdn.com/wp-content/uploads/2020/08/16/iecrop/ayder-yaylasi-shutter_4_3_1597574808.jpg');">
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
                        <img src="https://i.pinimg.com/originals/b5/c9/b3/b5c9b36a11a32d29a89adc63d4d703e4.jpg" alt="Pamukkale">
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
                        <img src="https://yiyegeze.com/wp-content/uploads/2020/07/Uzung%C3%B6l.jpg" alt="Uzungöl">
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
                        <img src="https://i0.wp.com/turkeyoutdoor.org/wp-content/uploads/2024/08/Ihlara-Vadisi-Aksaray.jpg?w=960&ssl=1" alt="Ihlara Vadisi">
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
                        <img src="https://img-s2.onedio.com/id-60d9981ee8865fa833926284/rev-0/w-1200/h-800/f-jpg/s-608d4de6134f7792572a39fa2535a58cfc6e7ef7.jpg"
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
                        <img src="https://tv5comtr.teimg.com/tv5-com-tr/uploads/2024/06/salda-golu.webp" alt="Salda Gölü">
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
                        <img src="https://dokuzeylulcom.teimg.com/dokuzeylul-com/uploads/2023/10/ayder-yaylasinda-gezilecek-yerler.jpg" alt="Ayder Yaylası">
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
                        <img src="https://rayhaber.com/wp-content/uploads/2023/07/Nemrut-Kalderasi-ve-Krater-Golu-Turizme-Kazandirilacak.jpg"
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
                        <img src="https://img.superhaber.com/storage/files/images/2019/07/03/duden-selalesi-antalya-DJK0_cover.jpg" alt="Düden Şelalesi">
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
                        <img src="https://cdn.memleket.com.tr/news/307456.jpg" alt="Meke Gölü">
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
                        <img src="https://grandgames.net/puzzle/f1200/domik_u_ozera_2.jpg" alt="Abant Gölü">
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
                        <img src="https://pbs.twimg.com/media/EUWsUX4XYAAcuSD.jpg" alt="Karçal Dağları">
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
                        <img src="https://polatlipostasicom.teimg.com/polatlipostasi-com/uploads/2023/10/doganin-buyuleyici-hediyesi-ve-dogaseverlerin-ruyasi-karagol-2.JPG" alt="Karagöl">
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
                        <img src="https://www.gidiyoruz.com/wp-content/uploads/2019/01/Dedeman-Paland%C3%B6ken-0001-800x445@2x.jpg" alt="Palandöken Dağı">
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
                        <img src="https://avatars.mds.yandex.net/i?id=2ec02e1c5b33f0071f00549542420708d8c43cd9-7741114-images-thumbs&n=13" alt="Olimpos">
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
                        <img src="https://yiyegeze.com/wp-content/uploads/2020/09/DSC_9968.jpg" alt="Beyşehir Gölü">
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
                        <img src="https://i.pinimg.com/originals/98/97/c3/9897c3797b0cda4c7d819182a591f46a.jpg" alt="Sapanca Gölü">
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