<?php
include 'includes/header.php';
?>

<main class="destinations-page">
    <section class="page-hero" style="background-image: url('https://www.allianz.com.tr/tr_TR/seninle-guzel/en-guzel-kamp/_jcr_content/root/stage/stageimage.img.82.3360.jpeg/1679653143304/turkiye-kamp-2920x1022.jpeg');">
        <div class="hero-overlay">
            <h1>Kamp Alanları</h1>
            <p>Türkiye'deki en iyi kamp alanlarını keşfedin.</p>
        </div>
    </section>

    <div class="destinations-grid">
        <aside class="filter-sidebar">
            <div class="filter-section">
                <h3>Bölgeler</h3>
                <div class="filter-options">
                    <label><input type="checkbox"> Marmara</label>
                    <label><input type="checkbox"> Ege</label>
                    <label><input type="checkbox"> Akdeniz</label>
                    <label><input type="checkbox"> Karadeniz</label>
                    <label><input type="checkbox"> İç Anadolu</label>
                    <label><input type="checkbox"> Doğu Anadolu</label>
                    <label><input type="checkbox"> Güneydoğu Anadolu</label>
                </div>
            </div>
        </aside>

        <div class="destinations-list">
            <!-- 1. Karadeniz Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/yedigoller-kamp.jpg" alt="Yedigöller Kamp Alanı">
                    <span class="destination-category">Karadeniz</span>
                </div>
                <div class="destination-info">
                    <h2>Yedigöller Milli Parkı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Bolu</span>
                        <span><i class="fas fa-calendar-alt"></i> Nisan - Kasım</span>
                    </div>
                    <p>Yedi ayrı gölü, zengin bitki örtüsü ve yaban hayatıyla muhteşem bir kamp deneyimi sunan milli park. Çadır ve karavan kamp alanları, piknik yerleri ve yürüyüş parkurları bulunmaktadır.</p>
                    <a href="yedigoller-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 2. Akdeniz Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/olimpos-kamp.jpg" alt="Olimpos Kamp Alanı">
                    <span class="destination-category">Akdeniz</span>
                </div>
                <div class="destination-info">
                    <h2>Olimpos Çıralı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Antalya</span>
                        <span><i class="fas fa-calendar-alt"></i> Tüm Yıl</span>
                    </div>
                    <p>Antik kent kalıntıları, doğal plajı ve ünlü Yanartaş'ı ile eşsiz bir kamp deneyimi. Denize sıfır kamp alanları ve bungalov seçenekleri mevcuttur.</p>
                    <a href="olimpos-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 3. Ege Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/kabakkoyu-kamp.jpg" alt="Kabak Koyu Kamp Alanı">
                    <span class="destination-category">Ege</span>
                </div>
                <div class="destination-info">
                    <h2>Kabak Koyu Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Muğla, Fethiye</span>
                        <span><i class="fas fa-calendar-alt"></i> Nisan - Ekim</span>
                    </div>
                    <p>El değmemiş doğası, berrak denizi ve muhteşem manzarasıyla kamp tutkunlarının gözdesi. Hem çadır hem de bungalov imkanları sunan tesisler mevcuttur.</p>
                    <a href="kabakkoyu-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 4. Karadeniz Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/kackar-kamp.jpg" alt="Kaçkar Dağları Kamp Alanı">
                    <span class="destination-category">Karadeniz</span>
                </div>
                <div class="destination-info">
                    <h2>Kaçkar Dağları Milli Parkı Kamp Alanları</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Rize</span>
                        <span><i class="fas fa-calendar-alt"></i> Haziran - Eylül</span>
                    </div>
                    <p>Buzul gölleri, yaylalar ve endemik bitki türleriyle dolu doğa harikası. Ayder, Kavron ve Elevit yaylalarında kamp alanları mevcuttur.</p>
                    <a href="kackar-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 5. Marmara Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/kurbagalıdere-kamp.jpg" alt="Kurbağalıdere Kamp Alanı">
                    <span class="destination-category">Marmara</span>
                </div>
                <div class="destination-info">
                    <h2>Kurbağalıdere Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Çanakkale, Güzelköy</span>
                        <span><i class="fas fa-calendar-alt"></i> Mayıs - Ekim</span>
                    </div>
                    <p>Denize sıfır konumu, sörf imkanları ve sakin atmosferiyle ideal bir kamp noktası. Temel kamp altyapısı ve duş imkanları mevcuttur.</p>
                    <a href="kurbagalidere-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 6. İç Anadolu Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/ilgaz-kamp.jpg" alt="Ilgaz Dağı Kamp Alanı">
                    <span class="destination-category">İç Anadolu</span>
                </div>
                <div class="destination-info">
                    <h2>Ilgaz Dağı Milli Parkı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Kastamonu/Çankırı</span>
                        <span><i class="fas fa-calendar-alt"></i> Mayıs - Ekim</span>
                    </div>
                    <p>Çam ormanları arasında doğayla iç içe kamp deneyimi. Kışın kayak merkezi olarak da hizmet vermektedir.</p>
                    <a href="ilgaz-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 7. Akdeniz Bölgesi -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/adrasan-kamp.jpg" alt="Adrasan Kamp Alanı">
                    <span class="destination-category">Akdeniz</span>
                </div>
                <div class="destination-info">
                    <h2>Adrasan Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Antalya</span>
                        <span><i class="fas fa-calendar-alt"></i> Tüm Yıl</span>
                    </div>
                    <p>Sakin koyları, berrak denizi ve doğal plajıyla popüler bir kamp noktası. Kano ve dalış aktiviteleri yapılabilir.</p>
                    <a href="adrasan-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 8. Doğu Anadolu - Nemrut Dağı -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/nemrut-kamp.jpg" alt="Nemrut Dağı Kamp Alanı">
                    <span class="destination-category">Doğu Anadolu</span>
                </div>
                <div class="destination-info">
                    <h2>Nemrut Dağı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Adıyaman</span>
                        <span><i class="fas fa-calendar-alt"></i> Mayıs - Eylül</span>
                    </div>
                    <p>Eşsiz gün doğumu ve batımı manzarasıyla ünlü, tarihi heykellerin yanında kamp yapma imkanı. Yüksek rakımda kamp deneyimi sunar.</p>
                    <a href="nemrut-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 9. Ege - Akbük Koyu -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/akbuk-kamp.jpg" alt="Akbük Koyu Kamp Alanı">
                    <span class="destination-category">Ege</span>
                </div>
                <div class="destination-info">
                    <h2>Akbük Koyu Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Aydın, Didim</span>
                        <span><i class="fas fa-calendar-alt"></i> Nisan - Ekim</span>
                    </div>
                    <p>Sakin atmosferi, temiz denizi ve çam ormanlarıyla çevrili koyda kamp deneyimi. Temel kamp altyapısı ve plaj imkanları mevcuttur.</p>
                    <a href="akbuk-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 10. Marmara - Longoz Ormanları -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/longoz-kamp.jpg" alt="Longoz Ormanları Kamp Alanı">
                    <span class="destination-category">Marmara</span>
                </div>
                <div class="destination-info">
                    <h2>İğneada Longoz Ormanları Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Kırklareli</span>
                        <span><i class="fas fa-calendar-alt"></i> Nisan - Ekim</span>
                    </div>
                    <p>Nadir görülen longoz ormanları, göller ve plajlarıyla eşsiz bir doğa deneyimi. Kuş gözlemciliği için ideal.</p>
                    <a href="longoz-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 11. İç Anadolu - Erciyes -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/erciyes-kamp.jpg" alt="Erciyes Dağı Tekir Kamp Alanı">
                    <span class="destination-category">İç Anadolu</span>
                </div>
                <div class="destination-info">
                    <h2>Erciyes Dağı Tekir Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Kayseri</span>
                        <span><i class="fas fa-calendar-alt"></i> Haziran - Eylül</span>
                    </div>
                    <p>Yüksek irtifa kampı için ideal, dağcılık aktiviteleri ve muhteşem manzaralar sunan kamp alanı.</p>
                    <a href="erciyes-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 12. Güneydoğu Anadolu - Hasankeyf -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/hasankeyf-kamp.jpg" alt="Hasankeyf Kamp Alanı">
                    <span class="destination-category">Güneydoğu Anadolu</span>
                </div>
                <div class="destination-info">
                    <h2>Hasankeyf Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Batman</span>
                        <span><i class="fas fa-calendar-alt"></i> Mart - Kasım</span>
                    </div>
                    <p>Dicle Nehri kıyısında, tarihi dokuyla iç içe kamp deneyimi. Temel kamp altyapısı ve tarihi alan gezileri mevcut.</p>
                    <a href="hasankeyf-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 13. Karadeniz - Altındere -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/altindere-kamp.jpg" alt="Altındere Vadisi Kamp Alanı">
                    <span class="destination-category">Karadeniz</span>
                </div>
                <div class="destination-info">
                    <h2>Altındere Vadisi Milli Parkı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Trabzon, Maçka</span>
                        <span><i class="fas fa-calendar-alt"></i> Mayıs - Ekim</span>
                    </div>
                    <p>Sümela Manastırı yakınında, yeşilin her tonunu barındıran vadide kamp imkanı. Trekking rotaları mevcut.</p>
                    <a href="altindere-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 14. Ege - Dilek Yarımadası -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/dilek-kamp.jpg" alt="Dilek Yarımadası Kamp Alanı">
                    <span class="destination-category">Ege</span>
                </div>
                <div class="destination-info">
                    <h2>Dilek Yarımadası Milli Parkı Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Aydın, Kuşadası</span>
                        <span><i class="fas fa-calendar-alt"></i> Nisan - Ekim</span>
                    </div>
                    <p>Bakir koyları, zengin florası ve faunasıyla doğal bir cennet. Denize girme ve doğa yürüyüşü imkanları mevcut.</p>
                    <a href="dilek-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

            <!-- 15. Doğu Anadolu - Aygır Gölü -->
            <div class="destination-card">
                <div class="destination-image">
                    <img src="images/aygir-kamp.jpg" alt="Aygır Gölü Kamp Alanı">
                    <span class="destination-category">Doğu Anadolu</span>
                </div>
                <div class="destination-info">
                    <h2>Aygır Gölü Kamp Alanı</h2>
                    <div class="destination-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Kars</span>
                        <span><i class="fas fa-calendar-alt"></i> Haziran - Ağustos</span>
                    </div>
                    <p>2000 metre rakımda bulunan krater gölü etrafında kamp deneyimi. Yıldız gözlem ve fotoğrafçılık için ideal.</p>
                    <a href="aygir-kamp-detay.php" class="btn-details">Detayları Gör</a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
