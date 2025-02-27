<?php
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: yonetim.php");
    exit;
}

$conn = new mysqli(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');


if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, sifre FROM kullanicilar WHERE kullanici_adi = ? AND rol = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $sifre);
    $stmt->fetch();

    if ($id) {
        if ($password === $sifre) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['logged_in'] = true;
            header("Location: yonetim.php");
            exit;
        } else {
            $error_message = "Kullanıcı adı veya şifre hatalı!";
        }
    } else {
        $error_message = "Kullanıcı adı veya şifre hatalı!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#222930">
    <title>Yönetici Girişi</title>
    <style>
        /* Genel stil */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* height yerine min-height */
            margin: 0;
            background: linear-gradient(135deg, #ededed, #ececec);
            flex-direction: column;
            padding: 20px;
            padding-top: 60px;
            box-sizing: border-box; /* box-sizing eklendi */
            overflow-x: hidden;
            /* Yatay kaydırma çubuğunu gizle */
        }

        .container {
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            width: 95%;
            max-width: 500px;
            margin: 20px auto;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .input-field {
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
            color: #404d5b;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Genel buton stili (çıkış butonu hariç) */
        button:not(.menu-button-danger),
        input[type="submit"],
        input[type="reset"],
        .btn {
            background-color: #826A56;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-sizing: border-box; /* box-sizing eklendi */
        }

        button:not(.menu-button-danger):hover,
        input[type="submit"]:hover,
        input[type="reset"]:hover,
        .btn:hover {
            background-color: #463629;
        }

        /* Popup stili */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            z-index: 10;
            animation: bounceIn 0.6s ease forwards;
            width: 90%;
            max-width: 400px;
        }

        .popup p {
            font-size: 14px;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        /* Genel buton stili (çıkış butonu hariç) */
        .popup button {
            background-color: #826A56;
            color: white;
            padding: 8px 15px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: auto;
            min-width: 100px;
        }

        .popup button:hover {
            background-color: #463629;
        }

        @keyframes bounceIn {
            0% {
                transform: translate(-50%, -50%) scale(0);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.1);
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }

        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        select:focus {
            border-color: #463629;
            outline: none;
            box-shadow: 0 0 5px rgba(92, 184, 92, 0.5);
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #463629;
            outline: none;
            box-shadow: 0 0 5px rgba(92, 184, 92, 0.5);
        }

        .logo {
            width: 150px;
            margin-bottom: 30px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease, filter 0.3s ease;
            /* Efekt geçişi */
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .logo:hover {
            transform: scale(1.1);
            /* Büyütme efekti */
            filter: drop-shadow(3px 3px 6px rgba(0, 0, 0, 0.5));
            /* Gölgeyi belirginleştir */
        }

        .baslik {
            font-size: clamp(18px, 4vw, 28px);
            font-weight: 100;
            text-align: center;
            margin: 10px 0;
            word-wrap: break-word;
            color: #404d5b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .footer-text {
            text-align: center;
            font-size: 10px;
            width: 100%;
            padding: 1px;
            margin-top: 5px;
            position: relative;
            bottom: 5px;
            color: #9d9d9d;
            line-height: 1;
        }

        /* Responsive tasarım için medya sorguları */
        @media screen and (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                margin: 10px auto;
            }

            input,
            select,
            textarea,
            button {
                font-size: 14px;
                padding: 8px;
            }

            .popup {
                width: 85%;
                padding: 15px;
            }

            form {
                gap: 10px;
            }

            .baslik {
                font-size: 20px;
            }

            .footer-text {
                margin-top: 10px;
                padding: 5px 0;
            }
        }

        @media screen and (max-height: 700px) {
            body {
                padding: 10px;
            }

            .container {
                margin: 5px auto;
            }

            .footer-text {
                margin-top: 8px;
                padding: 5px 0;
            }
        }

        #tarihSaatGoster {
            display: none;
        }

        /* Üst menü stilleri */
        .top-menu {
            background-color: #222930;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .menu-button {
            background-color: #826A56;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .menu-button:hover {
            background-color: #463629;
        }

        .menu-button-danger {
            background-color: #dc3545;
        }

        .menu-button-danger:hover {
            background-color: #c82333;
        }

        .menu-button i {
            font-size: 16px;
        }

        /* Responsive düzenlemeler */
        @media screen and (max-width: 768px) {
            body {
                padding-top: 80px;
                /* Menü yüksekliği kadar boşluk bırak */
            }

            .menu-container {
                padding: 0 10px;
            }

            .menu-button {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="https://test.kodkampusu.com/talepbildirimsistemi/" target="_blank">
        <img src="https://www.kodkampusu.com/wp-content/uploads/2020/12/yenilogo.png" alt="logo" class="logo" id="logo">
        </a>
        <p class="baslik">Yönetici Girişi</p>

        <?php if (isset($error_message)): ?>
            <div style="color: #dc3545; text-align: center; margin-bottom: 15px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-field">
                <label for="username">Yönetici Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-field">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Giriş Yap</button>
        </form>
    </div>

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

    <footer>
        <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
        <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
    </footer>
</body>
        <!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->
</html>

