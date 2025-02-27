<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Bildirim Sistemi</title>
    <meta name="theme-color" content="#404D5B">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Genel Stiller */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #404D5B, #202933); /* Renk paleti */
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden; /* Yatay kaymayı önle */
        }

        /* Belediye Logosu */
        .logo {
            width: 150px;
            margin-bottom: 30px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease, filter 0.3s ease; /* Efekt geçişi */
        }

        .logo:hover {
            transform: scale(1.1); /* Büyütme efekti */
            filter: drop-shadow(3px 3px 6px rgba(0, 0, 0, 0.5)); /* Gölgeyi belirginleştir */
        }

        /* Arka Plan Efekti (Daireler) */
        .bg-circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            /* En arkada kalması için */
        }

        .bg-circles div {
            position: absolute;
            display: block;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1); /* Dairelerin rengi */
            border-radius: 50%;
            animation: animate 25s linear infinite;
        }

        .bg-circles div:nth-child(1) {
            top: 10%;
            left: 20%;
            width: 120px;
            height: 120px;
            animation-delay: 0s;
        }

        .bg-circles div:nth-child(2) {
            top: 70%;
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 2s;
        }

        .bg-circles div:nth-child(3) {
            top: 40%;
            left: 60%;
            width: 100px;
            height: 100px;
            animation-delay: 4s;
        }

        .bg-circles div:nth-child(4) {
            top: 20%;
            left: 80%;
            width: 70px;
            height: 70px;
            animation-delay: 6s;
        }

        .bg-circles div:nth-child(5) {
            top: 67%;
            left: 10%;
            width: 90px;
            height: 90px;
            animation-delay: 8s;
        }

        .bg-circles div:nth-child(6) {
            top: 80%;
            left: 70%;
            width: 50px;
            height: 50px;
            animation-delay: 3s;
        }

        .bg-circles div:nth-child(7) {
            top: 30%;
            left: 30%;
            width: 60px;
            height: 60px;
            animation-delay: 7s;
        }

        .bg-circles div:nth-child(8) {
            top: 50%;
            left: 50%;
            width: 110px;
            height: 110px;
            animation-delay: 15s;
        }

        .bg-circles div:nth-child(9) {
            top: 40%;
            left: 10%;
            width: 40px;
            height: 40px;
            animation-delay: 0s;
        }

        .bg-circles div:nth-child(10) {
            top: 70%;
            left: 90%;
            width: 120px;
            height: 120px;
            animation-delay: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 50%;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 0;
            }
        }

        /* İçerik Alanı */
        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 40px;
            text-align: center;
            z-index: 1;
            /* Dairelerin üstünde olması için */
            box-sizing: border-box; /* Padding'in genişliği etkilememesi için */
        }

        /* Başlık ve Alt Başlık */
        h1 {
            font-size: 3.5em;
            font-weight: 600;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        p {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Butonlar */
        .menu {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .menu a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #826A56; /* Buton rengi */
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 500;
            transition: background-color 0.3s ease,
                color 0.3s ease,
                transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            box-sizing: border-box; /* Buton boyutlarının doğru hesaplanması için */
        }

        .menu a:hover {
            background-color: #463629; /* Hover rengi */
            color: #fff;
            transform: translateY(-3px);
        }

        .menu a i {
            margin-right: 8px;
        }

        /* Belediye Logosu */
        .logo {
            width: 150px;
            margin-bottom: 30px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
        }

        /* Alt Bilgi */
        footer {
            text-align: center;
            color: #666;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            /* Padding'in genişliği etkilememesi için */
        }

        .footer-text {
            font-size: 0.7em;
            color: rgba(255, 255, 255, 0.7);
            margin: 5px 0;
        }

        /* Responsive Tasarım */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.8em;
            }

            p {
                font-size: 1em;
            }

            .menu {
                flex-direction: column;
                align-items: center;
            }

            .menu a {
                width: 90%;
                /* Mobil cihazlarda butonları daha geniş yap */
                text-align: center;
                margin-bottom: 10px; /* Butonlar arasında boşluk bırak */
            }

            .logo {
                width: 120px;
            }

            .container {
                padding: 20px;
            }

            .footer-text {
                font-size: 0.6em;
                /* Daha küçük ekranlarda footer yazısını küçült */
            }
        }
    </style>
</head>

<body>
    <div class="bg-circles">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="container">
        <img src="https://www.kodkampusu.com/wp-content/uploads/2020/12/yenilogo.png" alt="logo" class="logo" id="logo">
        <h2>X Firması</h2>
        <h3>Talep Bildirim Sistemi</h3>
            <p>Sizin için en iyi hizmeti sunmak amacındayız!</p>
        <div class="menu">
            <a href="giris.php"><i class="fas fa-sign-in-alt"></i> Kullanıcı Girişi</a>
            <a href="admin_giris.php"><i class="fas fa-user-shield"></i> Yönetici Girişi</a>
        </div>
    </div>
    <footer>
        <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
        <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
    </footer>

    <script>
        // Logonun fareyle etkileşimi
        const logo = document.getElementById('logo');

        logo.addEventListener('mousemove', (e) => {
            const rect = logo.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;

            logo.style.transform = `rotateY(${x * 30}deg) rotateX(${y * -30}deg) translateZ(50px)`; /* Daha belirgin hareket */
        });

        logo.addEventListener('mouseleave', () => {
            logo.style.transform = 'rotateY(0deg) rotateX(0deg) translateZ(0)';
        });
    </script>
</body>
        <!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->
</html>