<?php
if (!defined('SITE_URL')) {
    require_once 'config.php';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?></title>
    <base href="/turkiyegezirehberi/">
    <link rel="stylesheet" href="/turkiyegezirehberi/css/layout.css">
    <link rel="stylesheet" href="/turkiyegezirehberi/css/style.css">
    <link rel="stylesheet" href="/turkiyegezirehberi/css/detail-page.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<body>
    <header class="site-header">
        <nav class="main-nav">
            <div class="logo">
                <a href="/turkiyegezirehberi/">
                    <i class="fas fa-paper-plane"></i>
                    Türkiye Gezi Rehberi
                </a>
            </div>
            
            <ul class="nav-links">
                <li><a href="/turkiyegezirehberi/">Ana Sayfa</a></li>
                <li><a href="/turkiyegezirehberi/tarihi-yerler.php">Tarihi Yerler</a></li>
                <li><a href="/turkiyegezirehberi/dogal-guzellikler.php">Doğal Güzellikler</a></li>
                <li><a href="/turkiyegezirehberi/lezzet-duraklari.php">Lezzet Durakları</a></li>
            </ul>

            <div class="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            this.classList.toggle('active');
            document.querySelector('.nav-links').classList.toggle('active');
            document.querySelector('.site-header').classList.toggle('menu-open');
            const spans = this.querySelectorAll('span');
            spans[0].classList.toggle('rotate-down');
            spans[1].classList.toggle('fade-out');
            spans[2].classList.toggle('rotate-up');
        });
    </script>
</body>
</html>
