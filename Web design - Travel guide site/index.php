<?php
include 'includes/header.php';
include 'includes/random_destinations.php';

$randomDestinations = getRandomDestinations();

$randomFoodDestinations = getRandomFoodDestinations();
?>

<main class="home-content">
    <section class="hero-slider">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="slide-content" style="background-image: url('img/index/header1.webp')">
                        <div class="slide-text">
                            <h1>Türkiye'yi Keşfet!</h1>
                            <p>
                                Türkiye, doğal güzellikleri, tarihi zenginlikleri ve kültürel çeşitliliğiyle her gezgine hitap eden bir cennet. 
                                Her köşesinde yeni bir macera ve keşif sizi bekliyor. Hayalinizdeki rotayı bulun ve unutulmaz bir yolculuğa çıkın!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content" style="background-image: url('img/index/header2.webp')">
                        <div class="slide-text">
                            <h1>Tarihin Doğal Güzellikle Buluşması</h1>
                            <p>
                                Türkiye, antik kentlerin büyüleyici atmosferi ile muhteşem doğal manzaraların buluştuğu bir ülke. 
                                Tarihin izlerini takip ederken bir yandan da dağların, denizlerin ve vadilerin tadını çıkarın. 
                                Her adımda yeni bir hikaye sizi bekliyor!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content" style="background-image: url('img/index/header3.webp')">
                        <div class="slide-text">
                            <h1>Lezzetler ve Kültürler Arası Yolculuk</h1>
                            <p>
                            Türkiye, dünyanın en zengin mutfak kültürlerinden birine ev sahipliği yapıyor. 
                            Geleneksel lezzetler, renkli pazarlar ve misafirperver insanlarla dolu bu yolculuk, damak tadınıza ve ruhunuza hitap edecek. 
                            Yemeklerin hikayesini keşfedin!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content" style="background-image: url('img/index/header4.webp')">
                        <div class="slide-text">
                        <h1>Kültürlerin Buluşma Noktası</h1>
                            <p>
                            Binlerce yıllık tarihiyle Türkiye, medeniyetlerin kesiştiği bir nokta. 
                            Antik kentler, camiler, kiliseler ve hanlar, geçmişin izlerini bugüne taşıyor. 
                            Bu topraklarda her taşın altında bir hikaye yatıyor.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content" style="background-image: url('img/index/header5.webp')">
                        <div class="slide-text">
                        <h1>Bir Ülke, Binbir Renk</h1>
                            <p>
                            Türkiye, her mevsim ayrı bir güzellik sunar. 
                            Yazın masmavi sahilleri, kışın karlı dağları, ilkbaharda yemyeşil vadileri ve sonbaharda renk cümbüşüyle dolu ormanlarıyla sizi büyüleyecek. 
                            Her mevsim ayrı bir macera sizi bekliyor!
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>

    <section class="blog-section">
        <h2>Buralara Uğramadan Geçme</h2>
        <div class="blog-container">
            <?php foreach ($randomDestinations as $destination): ?>
            <div class="blog-card">
                <img src="<?php echo $destination['image']; ?>" alt="<?php echo $destination['name']; ?>">
                <h3><?php echo $destination['name']; ?></h3>
                <p><?php echo mb_substr($destination['description'], 0, 100, 'UTF-8'); ?>...</p>
                <a href="<?php echo $destination['url']; ?>">Devamını Oku</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>



    <section class="food-section">
        <h2>Kesinlikle Tatman Gerek</h2>
        <div class="food-container">
            <?php foreach ($randomFoodDestinations as $food): ?>
            <div class="food-card">
                <a href="<?php echo $food['url']; ?>">
                <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>"></a>
                <h3><?php echo $food['name']; ?></h3>
                <p><?php echo mb_substr($food['description'], 0, 100, 'UTF-8'); ?>...</p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>