<?php
include 'includes/header.php';
?>

<main class="destinations-page">
    <section class="page-hero"
        style="background-image: url('https://voyageturkey.net/wp-content/uploads/2019/02/turkey_historical_places.png.webp');">
        <div class="hero-overlay">
            <h1>Tarihi Yerler</h1>
            <p>Türkiye'nin zengin tarihini keşfedin.</p>
        </div>
    </section>

    <div class="destinations-grid">
        <aside class="filter-sidebar">
            <div class="search-box">
                <h3>Tarihi Yer Ara</h3>
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
                        <p>Bizans İmparatoru I. Justinianus tarafından inşa ettirilen, dünya mimarlık tarihinin günümüze
                            kadar ayakta kalmış en önemli anıtları arasında yer alan muhteşem mabet.</p>
                        <a href="pages/tarihi-yerler/ayasofya-detay.php" class="btn-details">Detayları Gör</a>
                        </a>
                    </div>
                </div>


                <!-- 2. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/77/64/05/kizkalesi.jpg?w=1200&h=-1&s=1"
                            alt="Kızkalesi">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Kızkalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Erdemli, Mersin</span>
                            <span><i class="far fa-calendar-alt"></i> MS 12. yüzyıl</span>
                        </div>
                        <p>Akdeniz'in küçük bir adası üzerinde yer alan, Bizans döneminde inşa edilmiş, efsanelere konu
                            olmuş muhteşem deniz kalesi.</p>
                        <a href="pages/tarihi-yerler/kizkalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 3. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://muzeler.org/images/google-place-images/topkapi-sarayi-muzesi.jpg"
                            alt="Topkapı Sarayı">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Topkapı Sarayı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Fatih, İstanbul</span>
                            <span><i class="far fa-calendar-alt"></i> 1478-1478</span>
                        </div>
                        <p>400 yıl boyunca Osmanlı İmparatorluğu'nun idare merkezi olan, devletin en önemli törenlerine
                            ev sahipliği yapan ve padişahların yaşadığı saray kompleksi.</p>
                        <a href="pages/tarihi-yerler/topkapi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 4. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://cdn.villareyonu.com/uploads/200_Efes-antik-kenti-gezi-rehberi-villa-reyonu.jpg"
                            alt="Efes Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Efes Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Selçuk, İzmir</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 6000</span>
                        </div>
                        <p>Antik Dünya'nın en önemli ticaret ve kültür merkezlerinden biri olan, Artemis Tapınağı ve
                            Celsus Kütüphanesi gibi önemli yapıları barındıran UNESCO Dünya Mirası.</p>
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
                        <p>Antik Zeugma kentinden çıkarılan, dünyanın en büyük mozaik koleksiyonlarından birine ev
                            sahipliği yapan, "Çingene Kızı" mozaiğiyle ünlü müze.</p>
                        <a href="pages/tarihi-yerler/zeugma-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 6. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.baskentankarameclisi.com/cdn/ankarakalesi_1606815710.jpg"
                            alt="Ankara Kalesi">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Ankara Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Altındağ, Ankara</span>
                            <span><i class="far fa-calendar-alt"></i> MS 7. yüzyıl</span>
                        </div>
                        <p>Galatlar döneminde kurulan, Romalılar ve Bizanslılar tarafından güçlendirilen, Ankara'nın
                            tarihine tanıklık eden en önemli yapılardan biri.</p>
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
                        <p>Roma döneminden günümüze kadar en iyi korunmuş antik tiyatro yapısı olarak kabul edilen, hala
                            aktif olarak kullanılan muhteşem akustiğe sahip tiyatro.</p>
                        <a href="pages/tarihi-yerler/aspendos-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 8. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.turrehberin.com/wp-content/uploads/2020/09/Hattusas.jpeg" alt="Hattuşa">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Hattuşa Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Boğazkale, Çorum</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 1650</span>
                        </div>
                        <p>Hitit İmparatorluğu'nun başkenti, UNESCO Dünya Mirası Listesi'nde yer alan, görkemli surları
                            ve tapınaklarıyla öne çıkan antik kent.</p>
                        <a href="pages/tarihi-yerler/hattusa-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 9. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://mirsoumhotels.com/wp-content/uploads/2021/06/mardin-kalesi-uzaktan-gorunum.jpg"
                            alt="Mardin Kalesi">
                        <span class="destination-category">Güneydoğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Mardin Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Artuklu, Mardin</span>
                            <span><i class="far fa-calendar-alt"></i> MS 6. yüzyıl</span>
                        </div>
                        <p>Kartal Yuvası olarak da bilinen, Mezopotamya Ovası'na hakim konumu ile stratejik öneme sahip,
                            birçok medeniyete ev sahipliği yapmış tarihi kale.</p>
                        <a href="pages/tarihi-yerler/mardin-kalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 10. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://diyanethabercomtr.teimg.com/diyanethaber-com-tr/uploads/2023/01/ishakpasa-sarayi-11.jpg"
                            alt="İshak Paşa Sarayı">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>İshak Paşa Sarayı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Doğubayazit, Ağrı</span>
                            <span><i class="far fa-calendar-alt"></i> 1784</span>
                        </div>
                        <p>Ishak Paşa'nın, 1784 yılında kurduğu, UNESCO Dünya Mirası Listesi'nde yer alan, Osmanlı-Türk
                            mimarlık tarihinin en önemli eserlerinden biri.</p>
                        <a href="pages/tarihi-yerler/ishak-pasa-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 11. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://guryapi.com/wp-content/uploads/2023/02/selimiye-camii-icerik.jpg"
                            alt="Selimiye Camii">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Selimiye Camii</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Merkez, Edirne</span>
                            <span><i class="far fa-calendar-alt"></i> 1575</span>
                        </div>
                        <p>Mimar Sinan'ın "ustalık eserim" dediği, UNESCO Dünya Mirası Listesi'nde yer alan,
                            Osmanlı-Türk mimarlık tarihinin en önemli eseri.</p>
                        <a href="pages/tarihi-yerler/selimiye-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 12. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.cappadociapage.com/wp-content/uploads/peribacalari-milli-park.jpg"
                            alt=Kapadokya">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Kapadokya</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Göreme, Nevşehir</span>
                            <span><i class="far fa-calendar-alt"></i> MS 4. yüzyıl</span>
                        </div>
                        <p>Peribacaları, yer altı şehirleri, tarihi kiliseleri ve mağara otelleriyle ünlü, dünyanın en
                            önemli doğal ve tarihi miraslarından biri.</p>
                        <a href="pages/tarihi-yerler/kapadokya-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 13. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://aslindacom.teimg.com/crop/1280x720/aslinda-com/uploads/2024/07/sumela-manastirina-nasil-gidilir-trabzonun-tarihi-ve-dogal-guzelligi.jpg"
                            alt="Sümela Manastırı">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Sümela Manastırı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Maçka, Trabzon</span>
                            <span><i class="far fa-calendar-alt"></i> MS 386</span>
                        </div>
                        <p>Karadağ'ın sarp kayalıklarında inşa edilen, Meryem Ana'ya adanmış, benzersiz mimarisi ve
                            freskleriyle ünlü Ortodoks manastır kompleksi.</p>
                        <a href="pages/tarihi-yerler/sumela-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 14. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://muzeler.org/images/google-place-images/hierapolis.jpg"
                            alt="Hierapolis Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Hierapolis Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Pamukkale, Denizli</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 190</span>
                        </div>
                        <p>Pamukkale travertenlerinin üzerinde kurulu, şifa dağıtan sıcak su kaynaklarıyla ünlü, antik
                            dönemin önemli sağlık ve dinlenme merkezi.</p>
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
                        <p>Ortaçağ'da Ermeni Krallığı'nın başkenti, tarihi İpek Yolu üzerinde yer alan, surlarla
                            çevrili, kiliseleri ve katedralleriyle ünlü antik kent.</p>
                        <a href="pages/tarihi-yerler/ani-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 16. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://ankarakasderfed.com.tr/wp-content/uploads/2014/01/kastamonu_kalesi.jpg"
                            alt="Kastamonu Kalesi">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Kastamonu Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Merkez, Kastamonu</span>
                            <span><i class="far fa-calendar-alt"></i> MS 12. yüzyıl</span>
                        </div>
                        <p>Bizans döneminde inşa edilen, Kastamonu'nun en yüksek noktasında yer alan, şehrin tarihine
                            tanıklık eden ve muhteşem manzaralar sunan tarihi kale.</p>
                        <a href="pages/tarihi-yerler/kastamonu-kalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 17. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.kitaptansanattan.com/wp-content/uploads/2017/05/%C3%A7AR%C5%9EAMBA-k%C3%96PR%C3%9CS%C3%9C.jpg"
                            alt="Çarşamba Köprüsü">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Çarşamba Köprüsü</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Çarşamba, Samsun</span>
                            <span><i class="far fa-calendar-alt"></i> 1890</span>
                        </div>
                        <p>Osmanlı döneminde inşa edilen, 270 metre uzunluğundaki, 10 gözlü, taş kemerli, tarihi köprü.
                        </p>
                        <a href="pages/tarihi-yerler/carsamba-koprusu-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 18. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.hisglobal.com.tr/assets/images/1640594216.jpg" alt="Aizanoi Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Aizanoi Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Çavdarhisar, Kütahya</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 3000</span>
                        </div>
                        <p>Zeus Tapınağı, antik tiyatro, stadyum kompleksi ve dünyanın ilk ticaret borsası
                            kalıntılarıyla ünlü, Roma İmparatorluğu döneminin önemli ticaret ve kültür merkezlerinden
                            biri.</p>
                        <a href="pages/tarihi-yerler/aizanoi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>


                <!-- 19. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://image.hurimg.com/i/hurriyet/75/1110x740/5abb4775c03c0e0fdcf0eed8.jpg"
                            alt="Nemrut Dağı">
                        <span class="destination-category">Güneydoğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Nemrut Dağı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Kahta, Adıyaman</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 62</span>
                        </div>
                        <p>Kommagene Krallığı'ndan kalma dev heykelleri ve tümülüsü ile UNESCO Dünya Mirası Listesi'nde
                            yer alan, eşsiz gün doğumu ve batımı manzaralarıyla ünlü arkeolojik alan.</p>
                        <a href="pages/tarihi-yerler/nemrut-dagi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 20. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.fiberli.com.tr/Upload/Referanslar/f/1346/galata-kulesi-4.jpg"
                            alt="Galata Kulesi">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Galata Kulesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Beyoğlu, İstanbul</span>
                            <span><i class="far fa-calendar-alt"></i> 1348</span>
                        </div>
                        <p>Cenevizliler tarafından inşa edilen, İstanbul'un en eski ve önemli simgelerinden biri olan,
                            Haliç ve Boğaz'ın muhteşem manzarasını sunan tarihi kule.</p>
                        <a href="pages/tarihi-yerler/galata-kulesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 21. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://kvmgm.ktb.gov.tr/Resim/216757,afrodisias03jpg.png?0"
                            alt="Afrodisias Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Afrodisias Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Karacasu, Aydın</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 5. yüzyıl</span>
                        </div>
                        <p>Antik dönemin en önemli heykel okullarından birine ev sahipliği yapan, Afrodit Tapınağı ve
                            stadyumu ile ünlü, UNESCO Dünya Mirası Listesi'ndeki antik kent.</p>
                        <a href="pages/tarihi-yerler/afrodisias-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 22. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.stonewrap.com/media/2025/1/1836/divrigi-ulu-camii-nerededir-divrigi-ulu-cami-hakkinda-bilgi-1558783646-9408.jpg"
                            alt="Divriği Ulu Cami">
                        <span class="destination-category">Doğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Divriği Ulu Cami ve Darüşşifası</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Divriği, Sivas</span>
                            <span><i class="far fa-calendar-alt"></i> 1228</span>
                        </div>
                        <p>Mengücekliler döneminden kalma, benzersiz taş işçiliği ve mimari özellikleriyle UNESCO Dünya
                            Mirası Listesi'nde yer alan cami ve hastane kompleksi.</p>
                        <a href="pages/tarihi-yerler/divrigi-ulu-cami-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 23. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://yoldaolmak.com/wp-content/uploads/2017/10/Bergama-Akropol.jpg"
                            alt="Bergama Antik Kenti">
                        <span class="destination-category">Ege</span>
                    </div>
                    <div class="destination-info">
                        <h2>Bergama Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Bergama, İzmir</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 3. yüzyıl</span>
                        </div>
                        <p>Helenistik dönemin en önemli merkezlerinden biri olan, dünyanın en dik antik tiyatrosuna
                            sahip, UNESCO Dünya Mirası Listesi'ndeki antik kent.</p>
                        <a href="pages/tarihi-yerler/bergama-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 24. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.kulturportali.gov.tr/contents/images/Alaaddin_Camii%20FOTO%20Fatih%20K%C4%B1z%C4%B1lkaya%20logolu.jpg"
                            alt="Alaeddin Camii">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Alaeddin Camii</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Selçuklu, Konya</span>
                            <span><i class="far fa-calendar-alt"></i> 1221</span>
                        </div>
                        <p>Anadolu Selçuklu Sultanlarının türbelerinin bulunduğu, Selçuklu mimarisinin en önemli
                            örneklerinden biri olan tarihi cami.</p>
                        <a href="pages/tarihi-yerler/alaeddin-camii-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 25. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://www.kulturportali.gov.tr/contents/images/20190730120006864_myra%201%20yeni.jpeg"
                            alt="Myra Antik Kenti">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Myra Antik Kenti</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Demre, Antalya</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 5. yüzyıl</span>
                        </div>
                        <p>Noel Baba'nın (Aziz Nikolaos) yaşadığı yer olarak bilinen, kaya mezarları ve antik
                            tiyatrosuyla ünlü Likya döneminin önemli kenti.</p>
                        <a href="pages/tarihi-yerler/myra-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 26. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://arkeofili.com/wp-content/uploads/2015/08/harran1.jpg" alt="Harran Örenyeri">
                        <span class="destination-category">Güneydoğu Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Harran Örenyeri</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Harran, Şanlıurfa</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 2000</span>
                        </div>
                        <p>Geleneksel kubbe evleri, İslam'ın ilk üniversitesi kalıntıları ve surlarıyla ünlü, tarih
                            boyunca önemli bir ticaret ve bilim merkezi.</p>
                        <a href="pages/tarihi-yerler/harran-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 27. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://antalya.com.tr/Uploaded/listing/1-426/antalya_alanya_kalesi.jpg"
                            alt="Alanya Kalesi">
                        <span class="destination-category">Akdeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Alanya Kalesi</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Alanya, Antalya</span>
                            <span><i class="far fa-calendar-alt"></i> MS 13. yüzyıl</span>
                        </div>
                        <p>Selçuklular döneminde inşa edilen, Kızılkule, tersane ve surlarıyla birlikte UNESCO Dünya
                            Mirası Geçici Listesi'nde yer alan muhteşem kale kompleksi.</p>
                        <a href="pages/tarihi-yerler/alanya-kalesi-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 28. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://trthaberstatic.cdn.wp.trt.com.tr/resimler/922000/922820.jpg"
                            alt="Amasya Kral Kaya Mezarları">
                        <span class="destination-category">Karadeniz</span>
                    </div>
                    <div class="destination-info">
                        <h2>Amasya Kral Kaya Mezarları</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Merkez, Amasya</span>
                            <span><i class="far fa-calendar-alt"></i> MÖ 3. yüzyıl</span>
                        </div>
                        <p>Pontus Krallığı döneminde kayalara oyulmuş, Yeşilırmak Nehri kıyısındaki sarp kayalıklarda
                            yer alan etkileyici kral mezarları.</p>
                        <a href="pages/tarihi-yerler/amasya-kaya-mezarlari-detay.php" class="btn-details">Detayları
                            Gör</a>
                    </div>
                </div>


                <!-- 29. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Haydarpa%C5%9Fa_Gari_-_panoramio.jpg/1200px-Haydarpa%C5%9Fa_Gari_-_panoramio.jpg"
                            alt="Haydarpaşa Garı">
                        <span class="destination-category">Marmara</span>
                    </div>
                    <div class="destination-info">
                        <h2>Haydarpaşa Garı</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Kadıköy, İstanbul</span>
                            <span><i class="far fa-calendar-alt"></i> 1872</span>
                        </div>
                        <p>Kadıköy'de yer alan, 1872 yılında ilk defa insa edilen, muhteşem mimarisiyle göz kamaştıran
                            tarihi tren istasyon garı.</p>
                        <a href="pages/tarihi-yerler/haydarpasa-gari-detay.php" class="btn-details">Detayları Gör</a>
                    </div>
                </div>

                <!-- 30. Tarihi Yer -->
                <div class="destination-card">
                    <div class="destination-image">
                        <img src="https://i.pinimg.com/736x/2d/c9/9d/2dc99d34220551cea0fbe2ef2de4bb14--mosques-turkey.jpg"
                            alt="Eşrefoğlu Camii">
                        <span class="destination-category">İç Anadolu</span>
                    </div>
                    <div class="destination-info">
                        <h2>Eşrefoğlu Camii</h2>
                        <div class="destination-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Beyşehir, Konya</span>
                            <span><i class="far fa-calendar-alt"></i> 1297</span>
                        </div>
                        <p>Anadolu Konya Sultanlarının türbelerinin bulunduğu, Osmanlı mimarisi ve süslemeleriyle dikkat
                            çeken, ahşap direkler üzerine inşaa edilen tarihi cami.</p>
                        <a href="pages/tarihi-yerler/esreoglu-cami-detay.php" class="btn-details">Detayları Gör</a>
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