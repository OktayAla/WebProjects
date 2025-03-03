<?php
include 'includes/header.php';
?>

<main class="destinations-page">
    <section class="page-hero"
        style="background-image: url('https://i.pinimg.com/originals/59/ca/cc/59caccda29543432f7da7c8e10cbce5e.jpg');">
        <div class="hero-overlay">
            <h1>Tarihi Yerler</h1>
            <p>Türkiye'nin zengin tarihini keşfedin.</p>
        </div>
    </section>

    <div class="destinations-grid">
        <aside class="filter-sidebar">
            <!-- Arama kutusu bölgeler filtresinden önce ayrı bir kutu olarak yer alacak -->
            <div class="search-box">
                <h3>Tarihi Yer Ara</h3>
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Ara..">
                </div>
            </div>

            <!-- Bölgeler filtresi ayrı bir kutu olarak -->
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


                <!-- 1. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://arkeofili.com/wp-content/uploads/2020/07/ayasofya1.jpg" alt="Ayasofya">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ayasofya</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Fatih, İstanbul</span>
                            <span><i class="far fa-calendar-alt"></i> MS 537</span>
                        </div>
                        <p>Bizans İmparatoru I. Justinianus tarafından inşa ettirilen, dünya mimarlık tarihinin günümüze kadar ayakta kalmış en önemli anıtları arasında yer alan muhteşem mabet.</p>
                        <a href="pages/tarihi-yerler/ayasofya-detay.php" class="btn-details">
                            <span>Detayları Keşfet</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>


                <!-- 2. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/77/64/05/kizkalesi.jpg?w=1200&h=-1&s=1" alt="Kızkalesi">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Kızkalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Erdemli, Mersin</span>
                            <span><i class="far fa-calendar-alt"></i> MS 12. yüzyıl</span>
                        </div>
                        <p>Akdeniz'in küçük bir adası üzerinde yer alan, Bizans döneminde inşa edilmiş, efsanelere konu olmuş muhteşem deniz kalesi.</p>
                        <a href="pages/tarihi-yerler/kizkalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 3. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://muzeler.org/images/google-place-images/topkapi-sarayi-muzesi.jpg" alt="Topkapı Sarayı">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Topkapı Sarayı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Fatih, İstanbul</span>
                            <span><i class="far fa-calendar-alt"></i> 1478-1478</span>
                        </div>
                        <p>400 yıl boyunca Osmanlı İmparatorluğu'nun idare merkezi olan, devletin en önemli törenlerine ev sahipliği yapan ve padişahların yaşadığı saray kompleksi.</p>
                        <a href="pages/tarihi-yerler/topkapi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 4. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://cdn.villareyonu.com/uploads/200_Efes-antik-kenti-gezi-rehberi-villa-reyonu.jpg" alt="Efes Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Efes Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Selçuk, İzmir</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 6000</span>
                        </div>
                        <p>Antik Dünya'nın en önemli ticaret ve kültür merkezlerinden biri olan, Artemis Tapınağı ve Celsus Kütüphanesi gibi önemli yapıları barındıran UNESCO Dünya Mirası.</p>
                        <a href="pages/tarihi-yerler/efes-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 5. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.ktb.gov.tr/Resim/352574,zeugmapng.png?0" alt="Zeugma Mozaik Müzesi">
                        <span class="destination-category">Güneydoğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Zeugma Mozaik Müzesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Şehitkamil, Gaziantep</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 300</span>
                        </div>
                        <p>Antik Zeugma kentinden çıkarılan, dünyanın en büyük mozaik koleksiyonlarından birine ev sahipliği yapan, "Çingene Kızı" mozaiğiyle ünlü müze.</p>
                        <a href="pages/tarihi-yerler/zeugma-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 6. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.baskentankarameclisi.com/cdn/ankarakalesi_1606815710.jpg" alt="Ankara Kalesi">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ankara Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Altındağ, Ankara</span>
                            <span><i class="far fa-calendar-alt"></i> MS 7. yüzyıl</span>
                        </div>
                        <p>Galatlar döneminde kurulan, Romalılar ve Bizanslılar tarafından güçlendirilen, Ankara'nın tarihine tanıklık eden en önemli yapılardan biri.</p>
                        <a href="pages/tarihi-yerler/ankara-kalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 7. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://cdn.villacim.com.tr/uploads/197_aspendos.jpg" alt="Aspendos Antik Tiyatrosu">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Aspendos Antik Tiyatrosu</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Serik, Antalya</span>
                            <span><i class="far fa-calendar-alt"></i> MS 155</span>
                        </div>
                        <p>Roma döneminden günümüze kadar en iyi korunmuş antik tiyatro yapısı olarak kabul edilen, hala aktif olarak kullanılan muhteşem akustiğe sahip tiyatro.</p>
                        <a href="pages/tarihi-yerler/aspendos-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 8. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://lh5.googleusercontent.com/proxy/9OBvWxQ1JQJmcx_BEGslOeBopOIN7NfP6J7Tx301xS6fg49jkZ5WmE3SMVyQB-ptaIYd088nl5pVCKQxZMAJJkUsDCbZhG6MaNpt6bH1HQYxew" alt="Hattuşa">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Hattuşa Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Boğazkale, Çorum</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 1650</span>
                        </div>
                        <p>Hitit İmparatorluğu'nun başkenti, UNESCO Dünya Mirası Listesi'nde yer alan, görkemli surları ve tapınaklarıyla öne çıkan antik kent.</p>
                        <a href="pages/tarihi-yerler/hattusa-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 9. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://mirsoumhotels.com/wp-content/uploads/2021/06/mardin-kalesi-uzaktan-gorunum.jpg" alt="Mardin Kalesi">
                        <span class="destination-category">Güneydoğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Mardin Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Artuklu, Mardin</span>
                            <span><i class="far fa-calendar-alt"></i> MS 6. yüzyıl</span>
                        </div>
                        <p>Kartal Yuvası olarak da bilinen, Mezopotamya Ovası'na hakim konumu ile stratejik öneme sahip, birçok medeniyete ev sahipliği yapmış tarihi kale.</p>
                        <a href="pages/tarihi-yerler/mardin-kalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 10. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://diyanethabercomtr.teimg.com/diyanethaber-com-tr/uploads/2023/01/ishakpasa-sarayi-11.jpg" alt="İshak Paşa Sarayı">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>İshak Paşa Sarayı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Doğubayazit, Ağrı</span>
                            <span><i class="far fa-calendar-alt"></i> 1784</span>
                        </div>
                        <p>Ishak Paşa'nın, 1784 yılında kurduğu, UNESCO Dünya Mirası Listesi'nde yer alan, Osmanlı-Türk mimarlık tarihinin en önemli eserlerinden biri.</p>
                        <a href="pages/tarihi-yerler/ishak-pasa-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 11. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://guryapi.com/wp-content/uploads/2023/02/selimiye-camii-icerik.jpg" alt="Selimiye Camii">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Selimiye Camii</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Merkez, Edirne</span>
                            <span><i class="far fa-calendar-alt"></i> 1575</span>
                        </div>
                        <p>Mimar Sinan'ın "ustalık eserim" dediği, UNESCO Dünya Mirası Listesi'nde yer alan, Osmanlı-Türk mimarlık tarihinin en önemli eseri.</p>
                        <a href="pages/tarihi-yerler/selimiye-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 12. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.cappadociapage.com/wp-content/uploads/peribacalari-milli-park.jpg" alt=Kapadokya">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Kapadokya</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Göreme, Nevşehir</span>
                            <span><i class="far fa-calendar-alt"></i> MS 4. yüzyıl</span>
                        </div>
                        <p>Peribacaları, yer altı şehirleri, tarihi kiliseleri ve mağara otelleriyle ünlü, dünyanın en önemli doğal ve tarihi miraslarından biri.</p>
                        <a href="pages/tarihi-yerler/kapadokya-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 13. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://aslindacom.teimg.com/crop/1280x720/aslinda-com/uploads/2024/07/sumela-manastirina-nasil-gidilir-trabzonun-tarihi-ve-dogal-guzelligi.jpg" alt="Sümela Manastırı">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Sümela Manastırı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Maçka, Trabzon</span>
                            <span><i class="far fa-calendar-alt"></i> MS 386</span>
                        </div>
                        <p>Karadağ'ın sarp kayalıklarında inşa edilen, Meryem Ana'ya adanmış, benzersiz mimarisi ve freskleriyle ünlü Ortodoks manastır kompleksi.</p>
                        <a href="pages/tarihi-yerler/sumela-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 14. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://muzeler.org/images/google-place-images/hierapolis.jpg" alt="Hierapolis Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Hierapolis Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Pamukkale, Denizli</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 190</span>
                        </div>
                        <p>Pamukkale travertenlerinin üzerinde kurulu, şifa dağıtan sıcak su kaynaklarıyla ünlü, antik dönemin önemli sağlık ve dinlenme merkezi.</p>
                        <a href="pages/tarihi-yerler/hierapolis-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 15. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://arkeofili.com/wp-content/uploads/2016/07/ani1.jpg" alt="Ani Harabeleri">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ani Harabeleri</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Arpaçay, Kars</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 1. yüzyıl</span>
                        </div>
                        <p>Ortaçağ'da Ermeni Krallığı'nın başkenti, tarihi İpek Yolu üzerinde yer alan, surlarla çevrili, kiliseleri ve katedralleriyle ünlü antik kent.</p>
                        <a href="pages/tarihi-yerler/ani-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                
            </div>

            <!-- Sayfalama butonları için ayrı bir konteyner -->
            <div class="pagination-container">
                <div class="pagination"></div>
            </div>
        </div>
    </div>
</main>


<script src="js/filter.js"></script>

<?php include 'includes/footer.php'; ?>