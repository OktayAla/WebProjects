<?php
goto FnuaD;
FnuaD:
if (!defined("\123\x49\x54\105\x5f\125\x52\x4c")) {
    require_once "\143\157\x6e\x66\x69\x67\x2e\160\x68\x70";
}
goto l64sN;
mBvX9: ?>
</title>
<base href="/turkiyegezirehberi/">
<link href="/turkiyegezirehberi/css/layout.css" rel="stylesheet">
<link href="/turkiyegezirehberi/css/style.css" rel="stylesheet">
<link href="/turkiyegezirehberi/css/detail-page.css" rel="stylesheet"><?php goto Fd3BP;
l64sN: ?>
<!doctypehtml>
    <html lang="tr">

    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width,initial-scale=1" name="viewport">
        <title>
            <?php goto bzfqH;
            bzfqH:
            echo SITE_TITLE;
            goto mBvX9;
            Fd3BP:
            if (basename($_SERVER["\x50\x48\120\137\x53\x45\x4c\x46"]) == "\151\x6e\144\x65\170\56\x70\150\x70") { ?>
                <link href="/turkiyegezirehberi/css/index.css" rel="stylesheet"><?php }
            goto WUORV;
            WUORV: ?>
            <link href="/turkiyegezirehberi/css/responsive.css" rel="stylesheet">
            <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
                rel="stylesheet">
            <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet">
    </head>

    <body>
        <header class="site-header">
            <nav class="main-nav">
                <div class="logo"><a href="/turkiyegezirehberi/"><i class="fa-paper-plane fas"></i> Türkiye Gezi
                        Rehberi</a></div>
                <ul class="nav-links">
                    <li><a href="/turkiyegezirehberi/">Ana Sayfa</a></li>
                    <li><a href="/turkiyegezirehberi/tarihi-yerler.php">Tarihi Yerler</a></li>
                    <li><a href="/turkiyegezirehberi/dogal-guzellikler.php">Doğal Güzellikler</a></li>
                    <li><a href="/turkiyegezirehberi/lezzet-duraklari.php">Lezzet Durakları</a></li>
                </ul>
                <div class="mobile-menu"><span></span> <span></span> <span></span></div>
            </nav>
        </header>
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script src="js/main.js"></script>
        <script>document.querySelector('.mobile-menu').addEventListener('click', function () {
                this.classList.toggle('active');
                document.querySelector('.nav-links').classList.toggle('active');
                document.querySelector('.site-header').classList.toggle('menu-open');
                const spans = this.querySelectorAll('span');
                spans[0].classList.toggle('rotate-down');
                spans[1].classList.toggle('fade-out');
                spans[2].classList.toggle('rotate-up');
            });</script>
    </body>

    </html>