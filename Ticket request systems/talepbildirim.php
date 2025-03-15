<?php
session_start();

// Kullanıcı veya admin girişi kontrolü
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: giris.php");
    exit;
}

// Veritabanı bağlantısı
$conn = mysqli_connect(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');

// Bağlantı hatasını kontrol et
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// Karakter setini UTF-8 olarak ayarla
mysqli_set_charset($conn, "utf8");
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Bildirim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="theme-color" content="#222930">
    <style>
        /* Genel stil */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #ededed, #ececec);
            flex-direction: column;
            padding: 20px;
            padding-top: 50px;
            /* Menü yüksekliğine göre ayarlandı */
        }

        .container {
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            width: 95%;
            /* Ekranın %95'ini kapla */
            max-width: 500px;
            /* Maksimum genişlik */
            margin: 20px auto;
            position: relative;
            top: -30px;
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
                flex-direction: row;
                /* Öğeleri yatayda sırala */
                justify-content: space-around;
                /* Öğeleri eşit aralıklarla yerleştir */
                align-items: center;
                /* Dikeyde ortala */
            }

            .menu-button {
                font-size: 12px;
                padding: 6px 12px;
            }


        }

        /* Diğer stilleriniz */
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
            border-color: #5cb85c;
            outline: none;
            box-shadow: 0 0 5px rgba(92, 184, 92, 0.5);
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #5cb85c;
            outline: none;
            box-shadow: 0 0 5px rgba(92, 184, 92, 0.5);
        }

        .logo {
            width: 120px;
            max-width: 80%;
            height: auto;
            margin: 0 auto 20px auto;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            pointer-events: none;
        }

        .baslik {
            font-size: clamp(18px, 4vw, 28px);
            font-weight: 100;
            text-align: center;
            margin: 10px 0;
            word-wrap: break-word;
            color: #333;
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
                margin-top: -10px;
                padding: 5px 0;
            }
        }

        @media screen and (max-height: 650px) {
            body {
                padding: 5px;
            }

            .container {
                margin: 5px auto;
            }

            .footer-text {
                margin-top: -8px;
                padding: 5px 0;
            }
        }

        #tarihSaatGoster {
            display: none;
        }

        footer {
            margin-top: -30px;
        }

        #karakterSayaci {
            font-size: 12px;
            color: #666;
            display: block;
            text-align: right;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="top-menu">
        <div class="menu-container">
            <a href="kullanici_talepler.php" class="menu-button">
                <i class="fas fa-history"></i> Taleplerim
            </a>
            <a href="cikis.php" class="menu-button menu-button-danger">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>
    <div class="container">
        <a href="https://test.kodkampusu.com/talepbildirimsistemi/" target="_blank">
            <img class="logo" src="https://www.kodkampusu.com/wp-content/uploads/2020/12/yenilogo.png">
        </a>
        <p class="baslik">Talep Bildirim Formu</p>
        <form id="talepForm" method="POST">
            <div>
                <span id="tarihSaatGoster"></span>
                <input type="hidden" id="tarihSaat" name="tarihSaat">
            </div>
            <div>
                <label for="personelBirimi">Görev Yaptığınız Mahalle:</label>
                <?php
                // Kullanıcı ID'sini belirle (admin veya normal kullanıcı)
                $kullanici_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : $_SESSION['user_id'];

                // Kullanıcının mahalle bilgisini al
                $sql = "SELECT mahalle FROM kullanicilar WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt === false) {
                    die("SQL hazırlama hatası: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt, "i", $kullanici_id);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt); // Sonuçları sakla

                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $mahalle); // Sonuçları değişkenlere bağla
                        mysqli_stmt_fetch($stmt); // Sonucu getir

                        if ($mahalle) {
                            // Kullanıcının atanmış mahallesi varsa, readonly input göster
                            echo '<input type="text" id="personelBirimi" name="personelBirimi" value="' . htmlspecialchars($mahalle, ENT_QUOTES, 'UTF-8') . '" readonly>';
                        } else {
                            // Admin veya mahallesi atanmamış kullanıcı için dropdown göster
                            echo '<select id="personelBirimi" name="personelBirimi" required>
                                <option value="" disabled selected>Mahallenizi seçin</option>
                                <option value="Alaçam Mahallesi">Alaçam Mahallesi</option>
                                <option value="Atakent Mahallesi">Atakent Mahallesi</option>
                                <option value="Aydoğdu Mahallesi">Aydoğdu Mahallesi</option>
                                <option value="Bağcağız Mahallesi">Bağcağız Mahallesi</option>
                                <option value="Bahçeköy Mahallesi">Bahçeköy Mahallesi</option>
                                <option value="Bozkır Mahallesi">Bozkır Mahallesi</option>
                                <option value="Cumhuriyet Mahallesi">Cumhuriyet Mahallesi</option>
                                <option value="Çaltılı Mahallesi">Çaltılı Mahallesi</option>
                                <option value="Çamlıca Mahallesi">Çamlıca Mahallesi</option>
                                <option value="Çınarlı Mahallesi">Çınarlı Mahallesi</option>
                                <option value="Çivril Mahallesi">Çivril Mahallesi</option>
                                <option value="Değirmenli Mahallesi">Değirmenli Mahallesi</option>
                                <option value="Dereköy Mahallesi">Dereköy Mahallesi</option>
                                <option value="Fatih Mahallesi">Fatih Mahallesi</option>
                                <option value="Göksu Mahallesi">Göksu Mahallesi</option>
                                <option value="Güme Mahallesi">Güme Mahallesi</option>
                                <option value="Güzelköy Mahallesi">Güzelköy Mahallesi</option>
                                <option value="Hisar Mahallesi">Hisar Mahallesi</option>
                                <option value="İstiklal Mahallesi">İstiklal Mahallesi</option>
                                <option value="Karaağaç Mahallesi">Karaağaç Mahallesi</option>
                                <option value="Kavaklı Mahallesi">Kavaklı Mahallesi</option>
                                <option value="Kışla Mahallesi">Kışla Mahallesi</option>
                                <option value="Köselerli Mahallesi">Köselerli Mahallesi</option>
                                <option value="Köycük Mahallesi">Köycük Mahallesi</option>
                                <option value="Küçük Köy Mahallesi">Küçük Köy Mahallesi</option>
                                <option value="Merkez Mahallesi">Merkez Mahallesi</option>
                                <option value="Meydan Mahallesi">Meydan Mahallesi</option>
                                <option value="Narlı Mahallesi">Narlı Mahallesi</option>
                                <option value="Ortaören Mahallesi">Ortaören Mahallesi</option>
                                <option value="Özlü Mahallesi">Özlü Mahallesi</option>
                                <option value="Pınarbaşı Mahallesi">Pınarbaşı Mahallesi</option>
                                <option value="Sarıkavak Mahallesi">Sarıkavak Mahallesi</option>
                                <option value="Selamlı Mahallesi">Selamlı Mahallesi</option>
                                <option value="Taşhan Mahallesi">Taşhan Mahallesi</option>
                                <option value="Yalnızcabağ Mahallesi">Yalnızcabağ Mahallesi</option>
                                <option value="Yeniköy Mahallesi">Yeniköy Mahallesi</option>
                                <option value="Yeşilyurt Mahallesi">Yeşilyurt Mahallesi</option>
                                <option value="Yukarıköselerli Mahallesi">Yukarıköselerli Mahallesi</option>
                            </select>';
                        }
                    } else {
                        // Kullanıcı bulunamadı veya mahalle bilgisi yok
                        echo '<select id="personelBirimi" name="personelBirimi" required>
                            <option value="" disabled selected>Mahallenizi seçin</option>
                            <option value="Alaçam Mahallesi">Alaçam Mahallesi</option>
                            <option value="Atakent Mahallesi">Atakent Mahallesi</option>
                            <option value="Aydoğdu Mahallesi">Aydoğdu Mahallesi</option>
                            <option value="Bağcağız Mahallesi">Bağcağız Mahallesi</option>
                            <option value="Bahçeköy Mahallesi">Bahçeköy Mahallesi</option>
                            <option value="Bozkır Mahallesi">Bozkır Mahallesi</option>
                            <option value="Cumhuriyet Mahallesi">Cumhuriyet Mahallesi</option>
                            <option value="Çaltılı Mahallesi">Çaltılı Mahallesi</option>
                            <option value="Çamlıca Mahallesi">Çamlıca Mahallesi</option>
                            <option value="Çınarlı Mahallesi">Çınarlı Mahallesi</option>
                            <option value="Çivril Mahallesi">Çivril Mahallesi</option>
                            <option value="Değirmenli Mahallesi">Değirmenli Mahallesi</option>
                            <option value="Dereköy Mahallesi">Dereköy Mahallesi</option>
                            <option value="Fatih Mahallesi">Fatih Mahallesi</option>
                            <option value="Göksu Mahallesi">Göksu Mahallesi</option>
                            <option value="Güme Mahallesi">Güme Mahallesi</option>
                            <option value="Güzelköy Mahallesi">Güzelköy Mahallesi</option>
                            <option value="Hisar Mahallesi">Hisar Mahallesi</option>
                            <option value="İstiklal Mahallesi">İstiklal Mahallesi</option>
                            <option value="Karaağaç Mahallesi">Karaağaç Mahallesi</option>
                            <option value="Kavaklı Mahallesi">Kavaklı Mahallesi</option>
                            <option value="Kışla Mahallesi">Kışla Mahallesi</option>
                            <option value="Köselerli Mahallesi">Köselerli Mahallesi</option>
                            <option value="Köycük Mahallesi">Köycük Mahallesi</option>
                            <option value="Küçük Köy Mahallesi">Küçük Köy Mahallesi</option>
                            <option value="Merkez Mahallesi">Merkez Mahallesi</option>
                            <option value="Meydan Mahallesi">Meydan Mahallesi</option>
                            <option value="Narlı Mahallesi">Narlı Mahallesi</option>
                            <option value="Ortaören Mahallesi">Ortaören Mahallesi</option>
                            <option value="Özlü Mahallesi">Özlü Mahallesi</option>
                            <option value="Pınarbaşı Mahallesi">Pınarbaşı Mahallesi</option>
                            <option value="Sarıkavak Mahallesi">Sarıkavak Mahallesi</option>
                            <option value="Selamlı Mahallesi">Selamlı Mahallesi</option>
                            <option value="Taşhan Mahallesi">Taşhan Mahallesi</option>
                            <option value="Yalnızcabağ Mahallesi">Yalnızcabağ Mahallesi</option>
                            <option value="Yeniköy Mahallesi">Yeniköy Mahallesi</option>
                            <option value="Yeşilyurt Mahallesi">Yeşilyurt Mahallesi</option>
                            <option value="Yukarıköselerli Mahallesi">Yukarıköselerli Mahallesi</option>
                        </select>';
                    }
                } else {
                    echo "Sorgu yürütme hatası: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
                ?>
            </div>
            <div>
                <label for="talepTuru">Talep Edilen Birim:</label>
                <select id="talepTuru" name="talepTuru" required>
                    <option value="" disabled selected>Hangi birimden talep edileceğini seçin.</option>
                    <option value="Belediye Başkanlığı">Belediye Başkanlığı</option>
                    <option value="Basın Yayın ve Halkla İlişkiler Müdürlüğü">Basın Yayın ve Halkla İlişkiler Müdürlüğü
                    </option>
                    <option value="Bilgi İşlem Daire Başkanlığı">Bilgi İşlem Daire Başkanlığı</option>
                    <option value="Destek Hizmetleri Müdürlüğü">Destek Hizmetleri Müdürlüğü</option>
                    <option value="Fen İşleri Müdürlüğü">Fen İşleri Müdürlüğü</option>
                    <option value="Hukuk İşleri Müdürlüğü">Hukuk İşleri Müdürlüğü</option>
                    <option value="İmar ve Şehircilik Müdürlüğü">İmar ve Şehircilik Müdürlüğü</option>
                    <option value="İnsan Kaynakları ve Eğitim Müdürlüğü">İnsan Kaynakları ve Eğitim Müdürlüğü</option>
                    <option value="İşletme ve İştirakler Müdürlüğü">İşletme ve İştirakler Müdürlüğü</option>
                    <option value="Kültür ve Sosyal İşler Müdürlüğü">Kültür ve Sosyal İşler Müdürlüğü</option>
                    <option value="Mali Hizmetler Müdürlüğü">Mali Hizmetler Müdürlüğü</option>
                    <option value="Muhtarlık İşleri Müdürlüğü">Muhtarlık İşleri Müdürlüğü</option>
                    <option value="Park ve Bahçeler Müdürlüğü">Park ve Bahçeler Müdürlüğü</option>
                    <option value="Tarımsal Hizmetler Müdürlüğü">Tarımsal Hizmetler Müdürlüğü</option>
                    <option value="Temizlik İşleri Müdürlüğü">Temizlik İşleri Müdürlüğü</option>
                    <option value="Yapı Kontrol Müdürlüğü">Yapı Kontrol Müdürlüğü</option>
                    <option value="Yazı İşleri Müdürlüğü">Yazı İşleri Müdürlüğü</option>
                    <option value="Zabıta Müdürlüğü">Zabıta Müdürlüğü</option>
                </select>
            </div>
            <div>
                <label for="detay">Talep Detayı:</label>
                <span id="karakterSayaci">0/800</span>
                <textarea id="detay" name="detay" rows="4" required maxlength="800"></textarea>
            </div>
            <button type="submit">Gönder</button>
        </form>
    </div>

    <div id="successPopup" class="popup">
        <h3>Başarıyla gönderildi!</h3>
        <p>İlgili birim sizinle en kısa sürede iletişime geçecektir.</p>
        <button onclick="closePopup()">Kapat</button>
    </div>

    <script>
        // Sayfa yüklendiğinde tarih ve saati göster ve güncelle
        function guncelTarihSaatiGoster() {
            const simdi = new Date();
            const tarihSaatStr = simdi.toLocaleString('tr-TR');
            const mysqlFormat = simdi.toLocaleString('sv-SE', {
                timeZone: 'Europe/Istanbul'
            }).replace(',', '');

            document.getElementById('tarihSaatGoster').textContent = tarihSaatStr;
            document.getElementById('tarihSaat').value = mysqlFormat;
        }

        // Her saniye tarih ve saati güncelle
        setInterval(guncelTarihSaatiGoster, 1000);
        guncelTarihSaatiGoster(); // Sayfa yüklendiğinde ilk çağrı

        document.getElementById('talepForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('talep_kaydet.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Server response:', data);
                    if (data.trim() === 'success') {
                        document.getElementById('successPopup').style.display = 'block';
                        document.getElementById('talepForm').reset();
                        guncelTarihSaatiGoster();
                    } else {
                        console.error('Server error:', data);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });

        function closePopup() {
            document.getElementById('successPopup').style.display = 'none';
        }

        // Karakter sayacı güncelleme fonksiyonu
        function karakterSayaciGuncelle() {
            const textarea = document.getElementById('detay');
            const sayac = document.getElementById('karakterSayaci');
            const uzunluk = textarea.value.length;
            const kalan = 800 - uzunluk;

            sayac.textContent = uzunluk + '/800';

            if (kalan <= 0) {
                sayac.style.color = 'red';
            } else {
                sayac.style.color = ''; // Varsayılan rengi sıfırla
            }
        }

        // Textarea her değiştiğinde karakter sayacını güncelle
        document.getElementById('detay').addEventListener('input', karakterSayaciGuncelle);

        // Sayfa yüklendiğinde sayacı başlat
        karakterSayaciGuncelle();
    </script>
</body>

<footer>
    <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
    <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
</footer>
        <!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->
</html>