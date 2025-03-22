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
            <div class="search-box">
                <h3>Lezzet Ara</h3>
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Ara..">
                </div>
            </div>

            <div class="filter-section">
                <h3>Mutfak Türleri</h3>
                <div class="filter-options">
                    <label><input type="checkbox" value="Kebap ve Et Yemekleri"> Kebap ve Et Yemekleri</label>
                    <label><input type="checkbox" value="Deniz Mahsülleri"> Deniz Mahsülleri</label>
                    <label><input type="checkbox" value="Hamur İşleri"> Hamur İşleri</label>
                    <label><input type="checkbox" value="Tatlılar"> Tatlılar</label>
                    <label><input type="checkbox" value="Kahvaltı Kültürü"> Kahvaltı Kültürü</label>
                    <label><input type="checkbox" value="Sokak Lezzetleri"> Sokak Lezzetleri</label>
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
            <!-- Adana Kebabı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/11/09/17/02/kebab-4614070_1280.jpg" alt="Adana Kebabı">
                    <span class="cuisine-category">Kebap Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Adana Kebabı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Adana</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Dünyaca ünlü lezzeti, özel baharatları ve pişirme tekniğiyle damak çatlatan bir efsane. Zırh adı verilen özel bir bıçakla kıyılan kuzu eti, çeşitli baharatlar ve yağ ile yoğrulup şişlere dizilerek mangalda pişirilir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Yüzevler Kebap</li>
                            <li>Ciğerci Mahmut</li>
                            <li>Kebapçı Şeyhmus</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/adana.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Gaziantep Baklavası -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/05/11/15/06/food-5158702_1280.jpg" alt="Antep Baklavası">
                    <span class="cuisine-category">Tatlılar</span>
                </div>
                <div class="destination-info">
                    <h2>Baklava</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Gaziantep</span>
                        <span><i class="fas fa-cookie"></i> Tatlılar</span>
                    </div>
                    <p>Dünyaca ünlü tatlısı, ince yufkası, Antep fıstığı ve özel şerbetiyle damakları şenlendiren bir şaheser. 2013 yılında Avrupa Birliği tarafından Türkiye'nin ilk coğrafi işaretli ürünü olarak tescillenmiştir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>İmam Çağdaş</li>
                            <li>Koçak Baklava</li>
                            <li>Güllüoğlu</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/gaziantep.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Bursa İskender Kebabı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.yemek.com/uploads/2016/05/iskender-kebap-yemekcom.jpg" alt="İskender Kebap">
                    <span class="cuisine-category">Kebap Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>İskender Kebabı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Bursa</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Dünyaca ünlü lezzeti, eşsiz tadı ve sunumuyla Türk mutfağının en önemli temsilcilerinden biri. İnce dilimlenmiş döner etinin tereyağı gezdirilmiş pide üzerine yerleştirilmesi ve yanında yoğurt ile servis edilmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>İskender Kebapçısı</li>
                            <li>Kebapçı İskender</li>
                            <li>Uludağ Kebapçısı</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/bursa.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Aydın İncir Tatlısı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2018/08/02/23/58/figs-3580568_1280.jpg" alt="İncir Tatlısı">
                    <span class="cuisine-category">Tatlılar</span>
                </div>
                <div class="destination-info">
                    <h2>İncir Tatlısı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Aydın</span>
                        <span><i class="fas fa-cookie"></i> Tatlılar</span>
                    </div>
                    <p>Altın değerindeki incirlerinden yapılan, ceviz dolgulu, bal şerbetli muhteşem bir lezzet. Kurutulmuş incirlerin içine ceviz konularak, bal ve şeker şerbetiyle tatlandırılmasıyla hazırlanan geleneksel bir Ege tatlısıdır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Aydın Tatlıcısı</li>
                            <li>İncir Evi</li>
                            <li>Ege Lezzetleri</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/aydin.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Erzurum Cağ Kebabı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/09/15/08/06/meat-4477748_1280.jpg" alt="Cağ Kebabı">
                    <span class="cuisine-category">Kebap Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Cağ Kebabı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Erzurum</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel pişirme tekniği ve yüzyıllık geleneğiyle damakları şenlendiren bir efsane. Kuzu etinin özel bir şekilde marine edilip, yatay şişlere dizilerek odun ateşinde pişirilmesiyle hazırlanan geleneksel bir Doğu Anadolu yemeğidir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Tortum Cağ Kebabı</li>
                            <li>Şehzade Cağ Kebabı</li>
                            <li>Dadaş Cağ Kebap</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/erzurum.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Diyarbakır Kaburga Dolması -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/06/03/22/06/meat-4250412_1280.jpg" alt="Kaburga Dolması">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Kaburga Dolması</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Diyarbakır</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel baharatları ve pişirme tekniğiyle damaklarda iz bırakan bir şölen. Kuzu kaburgasının içinin özel bir teknikle boşaltılıp, pirinç, kıyma, baharatlar ve kuruyemişlerle doldurularak fırında pişirilmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Hasan Paşa Hanı</li>
                            <li>Kebapçı Hacı Baba</li>
                            <li>Sur Ocakbaşı</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/diyarbakir.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Denizli Kebabı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.getiryemek.com/restaurants/1683796075177_1125x522.jpeg" alt="Denizli Kebabı">
                    <span class="cuisine-category">Kebap Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Denizli Kebabı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Denizli</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Geleneksel lezzeti, özel pişirme tekniği ve sunumuyla damakları şenlendiren bir klasik. Kuzu etinin özel bir şekilde hazırlanıp, çömlek içinde odun ateşinde pişirilmesiyle hazırlanan geleneksel bir Ege yemeğidir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Denizli Kebapçısı</li>
                            <li>Çömlek Kebabı</li>
                            <li>Ege Sofrası</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/denizli.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Edirne Ciğer Tavası -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.yemek.com/uploads/2019/01/edirne-cigeri-yemekcom.jpg" alt="Edirne Ciğer Tavası">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Ciğer Tavası</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Edirne</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Simgesi haline gelmiş, kendine özgü pişirme tekniği ve sunumuyla eşsiz bir lezzet. Kuzu ciğerinin ince dilimler halinde kesilerek kızgın yağda kıtır hale gelene kadar kızartılması ve yanında özel garnitürlerle servis edilir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Niyazi Usta</li>
                            <li>Edirne Ciğercisi</li>
                            <li>Köfteci Osman</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/edirne.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Ankara Tava -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/02/11/15/51/meat-4839763_1280.jpg" alt="Ankara Tava">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Ankara Tava</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Ankara</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Başkentin eşsiz lezzeti, özel pişirme tekniği ve zengin içeriğiyle damaklarda iz bırakan bir tat. Kuzu etinin kemikli parçalarının patates, soğan ve biber gibi sebzelerle birlikte fırında pişirilmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Ankara Kebapçısı</li>
                            <li>Hacı Arif Bey</li>
                            <li>Kızılcahamam Sofrası</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/ankara.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Antalya Piyazı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/08/31/12/15/piyaz-5532473_1280.jpg" alt="Antalya Piyazı">
                    <span class="cuisine-category">Mezeler</span>
                </div>
                <div class="destination-info">
                    <h2>Piyaz</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Antalya</span>
                        <span><i class="fas fa-utensils"></i> Mezeler</span>
                    </div>
                    <p>Akdeniz'in incisi Antalya'nın eşsiz lezzeti, özel tahin sosu ve sunumuyla damakları şenlendiren bir klasik. Haşlanmış kuru fasulye, soğan, maydanoz ve domates gibi malzemelerin tahin, sirke, limon suyu ve zeytinyağı ile hazırlanan özel bir sos ile harmanlanmasıyla yapılır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Şişçi Ramazan</li>
                            <li>Piyazcı Ahmet</li>
                            <li>Akdeniz Piyaz Evi</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/antalya.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Kayseri Mantısı -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/03/22/16/18/food-4957650_1280.jpg" alt="Kayseri Mantısı">
                    <span class="cuisine-category">Hamur İşleri</span>
                </div>
                <div class="destination-info">
                    <h2>Kayseri Mantısı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Kayseri</span>
                        <span><i class="fas fa-utensils"></i> Hamur İşleri</span>
                    </div>
                    <p>Dünyaca ünlü lezzeti, ince hamuru ve özel sunumuyla damakları şenlendiren bir klasik. İnce açılan hamurun içine kıyma konulup küçük bohçalar şeklinde kapatılması ve özel soslarla servis edilmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Kayseri Mutfağı</li>
                            <li>Hacı Mantı Evi</li>
                            <li>Erciyes Mantı Salonu</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/kayseri.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Konya Etli Ekmeği -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/06/18/10/46/pide-4282388_1280.jpg" alt="Etli Ekmek">
                    <span class="cuisine-category">Hamur İşleri</span>
                </div>
                <div class="destination-info">
                    <h2>Etli Ekmek</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Konya</span>
                        <span><i class="fas fa-utensils"></i> Hamur İşleri</span>
                    </div>
                    <p>Eşsiz lezzeti, ince hamuru ve özel kıymalı harcıyla damakları şenlendiren bir klasik. İnce açılmış hamurun üzerine kıyma, domates, biber ve baharatlardan oluşan bir harç yayılarak taş fırında pişirilmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Konya Etli Ekmek Salonu</li>
                            <li>Mevlana Etli Ekmek</li>
                            <li>Cemo Etli Ekmek</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/konya.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

             <!-- Malatya Kayısısı -->
             <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2018/06/10/17/40/apricots-3466819_1280.jpg" alt="Malatya Kayısısı">
                    <span class="cuisine-category">Meyveler</span>
                </div>
                <div class="destination-info">
                    <h2>Kayısı Tatlısı</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Malatya</span>
                        <span><i class="fas fa-seedling"></i> Meyveler</span>
                    </div>
                    <p>Altın değerindeki kayısıları, eşsiz aroması ve besin değeriyle dünya çapında tanınan bir lezzet. Malatya'nın kendine özgü iklim koşulları ve toprak yapısı sayesinde, burada yetişen kayısılar eşsiz bir tada sahiptir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Malatya Kayısı Pazarı</li>
                            <li>Kayısı Evi</li>
                            <li>Anadolu Lezzetleri</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/malatya.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Elazığ Harput Köftesi -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/06/30/20/09/kofte-4308873_1280.jpg" alt="Harput Köftesi">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Harput Köftesi</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Elazığ</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel baharatları ve pişirme tekniğiyle damaklarda iz bırakan geleneksel bir tat. Yağsız dana kıymasının özel baharatlarla yoğrulup, içine ceviz konularak hazırlanan ve genellikle fırında pişirilen bir yemektir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Harput Köftecisi</li>
                            <li>Elazığ Sofrası</li>
                            <li>Fırat Lezzetleri</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/elazig.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Mersin Tantunisi -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/02/06/14/58/kebab-4824859_1280.jpg" alt="Mersin Tantunisi">
                    <span class="cuisine-category">Sokak Lezzetleri</span>
                </div>
                <div class="destination-info">
                    <h2>Tantuni</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Mersin</span>
                        <span><i class="fas fa-utensils"></i> Sokak Lezzetleri</span>
                    </div>
                    <p>Eşsiz sokak lezzeti, özel baharatları ve hazırlanış tekniğiyle damaklarda iz bırakan bir klasik. Küçük parçalar halinde doğranmış etin özel bir tavada pişirilip, ince lavaş ekmeğine sarılmasıyla hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Tantuni Ustası</li>
                            <li>Mersin Tantuni Evi</li>
                            <li>Akdeniz Tantuni</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/mersin.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Rize Muhlaması -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/05/11/15/06/food-5158702_1280.jpg" alt="Rize Muhlaması">
                    <span class="cuisine-category">Kahvaltı Kültürü</span>
                </div>
                <div class="destination-info">
                    <h2>Muhlama (Kuymak)</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Rize</span>
                        <span><i class="fas fa-utensils"></i> Kahvaltı Kültürü</span>
                    </div>
                    <p>Eşsiz lezzeti, peynir ve mısır ununun muhteşem uyumuyla damaklarda iz bırakan geleneksel bir Karadeniz lezzeti. Mısır unu, tereyağı ve yöresel peynirin bir araya gelmesiyle hazırlanır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Rize Lezzet Konağı</li>
                            <li>Karadeniz Sofrası</li>
                            <li>Muhlama Evi</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/rize.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Trabzon Akçaabat Köftesi -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/06/30/20/09/kofte-4308873_1280.jpg" alt="Akçaabat Köftesi">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Akçaabat Köftesi</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Trabzon</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel kıyması ve pişirme tekniğiyle damakları şenlendiren bir klasik. Yağsız dana etinin özel bir şekilde yoğrulup, kömür ateşinde pişirilmesiyle hazırlanan geleneksel bir Karadeniz yemeğidir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Akçaabat Köftecisi</li>
                            <li>Trabzon Lezzet Durağı</li>
                            <li>Karadeniz Köfte Evi</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/trabzon.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Şanlıurfa Çiğ Köftesi -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/11/16/13/13/meat-4629313_1280.jpg" alt="Çiğ Köfte">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Çiğ Köfte</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Şanlıurfa</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Dünyaca ünlü lezzeti, özel baharatları ve yoğurma tekniğiyle damakları şenlendiren bir klasik. Bulgur, isot (Urfa biberi) ve çeşitli baharatların yoğrulmasıyla hazırlanan geleneksel bir Güneydoğu Anadolu yemeğidir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Urfa Çiğköftecisi</li>
                            <li>İsot Lezzetleri</li>
                            <li>Güneydoğu Sofrası</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/sanliurfa.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

            <!-- Samsun Pide -->
            <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/05/17/18/01/pide-5181547_1280.jpg" alt="Samsun Pidesi">
                    <span class="cuisine-category">Hamur İşleri</span>
                </div>
                <div class="destination-info">
                    <h2>Samsun Pidesi</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Samsun</span>
                        <span><i class="fas fa-utensils"></i> Hamur İşleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel hamuru ve çeşitli iç malzemeleriyle damakları şenlendiren bir klasik. Karadeniz mutfağının en önemli temsilcilerinden olan Samsun pidesi, uzun şekli ve özel pişirme tekniğiyle bilinir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Samsun Pide Salonu</li>
                            <li>Karadeniz Pidecisi</li>
                            <li>Pide Ustası</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/samsun.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

             <!-- Sakarya Islama Köftesi -->
             <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2019/06/30/20/09/kofte-4308873_1280.jpg" alt="Islama Köfte">
                    <span class="cuisine-category">Et Yemekleri</span>
                </div>
                <div class="destination-info">
                    <h2>Islama Köfte</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Sakarya</span>
                        <span><i class="fas fa-utensils"></i> Et Yemekleri</span>
                    </div>
                    <p>Eşsiz lezzeti, özel köftesi ve sunumuyla damakları şenlendiren bir klasik. Köftelerin özel bir ekmek üzerine yerleştirilip, et suyu ile ıslatılmasıyla hazırlanan geleneksel bir Marmara yemeğidir.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Adapazarı Köftecisi</li>
                            <li>Sakarya Lezzetleri</li>
                            <li>Islama Köfte Evi</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/sakarya.php" class="btn-details">Lezzeti Keşfet</a>
                </div>
            </div>

             <!-- Manisa Mesir Macunu -->
             <div class="culinary-card">
                <div class="destination-image">
                    <img src="https://cdn.pixabay.com/photo/2020/05/11/15/06/food-5158702_1280.jpg" alt="Mesir Macunu">
                    <span class="cuisine-category">Tatlılar</span>
                </div>
                <div class="destination-info">
                    <h2>Mesir Macunu</h2>
                    <div class="cuisine-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Manisa</span>
                        <span><i class="fas fa-cookie"></i> Tatlılar</span>
                    </div>
                    <p>Eşsiz lezzeti, 41 çeşit baharat ve şifalı otlardan yapılan geleneksel bir macun. Hem lezzeti hem de şifa kaynağı olarak bilinen Mesir Macunu, UNESCO Somut Olmayan Kültürel Miras Listesi'nde yer almaktadır.</p>
                    <div class="famous-spots">
                        <h4>Mekanlar:</h4>
                        <ul>
                            <li>Manisa Mesir Macunu</li>
                            <li>Sultan Şifahanesi</li>
                            <li>Mesir Baharat Evi</li>
                        </ul>
                    </div>
                    <a href="pages/lezzet-duraklari/manisa.php" class="btn-details">Lezzeti Keşfet</a>
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

                    <div class="page-numbers"></div>

                    <button class="page-btn next-btn">
                        Sonraki
                        <i class="fas fa-chevron-right"></i>
                        </button>
                </div>
            </div>
    </div>
</main>

<script src="js/pagination.js"></script>

<script src="js/filter.js"></script>

<?php include 'includes/footer.php'; ?>