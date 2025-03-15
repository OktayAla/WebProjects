<?php include 'includes/header.php'; ?>

<main class="destinations-page">
    <section class="page-hero" style="background-image: url('https://cdnp.flypgs.com/files/Sehirler-long-tail/istanbul/istanbulda-nerede-yenir.jpg');">
        <div class="hero-overlay">
            <h1>Lezzet Durakları</h1>
            <p>Türkiye'nin eşsiz mutfak kültürünü ve yöresel lezzetlerini keşfedin</p>
        </div>
    </section>

    <div class="destinations-grid">
        <aside class="filter-sidebar">
            <div class="filter-section">
                <h3>Mutfak Türleri</h3>
                <div class="filter-options">
                    <label><input type="checkbox"> Kebap ve Et Yemekleri</label>
                    <label><input type="checkbox"> Deniz Mahsülleri</label>
                    <label><input type="checkbox"> Hamur İşleri</label>
                    <label><input type="checkbox"> Tatlılar</label>
                    <label><input type="checkbox"> Kahvaltı Kültürü</label>
                    <label><input type="checkbox"> Sokak Lezzetleri</label>
                </div>
            </div>
        </aside>

        <div class="destinations-list">
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="images/adana-kebap.jpg" alt="Adana Kebap">
                    <span class="cuisine-category">Kebap Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Adana Kebap Rotası</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Adana</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Meşhur Adana kebabının en iyi adresleri, geleneksel pişirme yöntemleri ve yanında servis edilen mezeler.</p>
                    <div class="famous-spots">
                        <h4>Meşhur Mekanlar:</h4>
                        <ul>
                            <li>Yüzevler Kebap</li>
                            <li>Ciğerci Mahmut</li>
                            <li>Kebapçı Şeyhmus</li>
                        </ul>
                    </div>
                    <a href="adana-kebap-rotasi.php" class="btn-details">Rotayı Keşfet</a>
                </div>
            </div>

            <div class="culinary-card">
                <div class="destination-image">
                    <img src="images/van-kahvalti.jpg" alt="Van Kahvaltısı">
                    <span class="cuisine-category">Kahvaltı Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Van Kahvaltı Rotası</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Van</span>
                        <span><i class="fas fa-clock"></i> Kahvaltı</span>
                    </div>
                    <p>Türkiye'nin kahvaltı başkenti Van'da otantik kahvaltı salonları ve yöresel kahvaltılıklar.</p>
                    <div class="famous-spots">
                        <h4>Meşhur Mekanlar:</h4>
                        <ul>
                            <li>Bak Hele Bak</li>
                            <li>Sütçü Kenan</li>
                            <li>Van Kahvaltı Evi</li>
                        </ul>
                    </div>
                    <a href="van-kahvalti-rotasi.php" class="btn-details">Rotayı Keşfet</a>
                </div>
            </div>

            <!-- Diğer gastronomi rotaları benzer şekilde devam edecek -->
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
