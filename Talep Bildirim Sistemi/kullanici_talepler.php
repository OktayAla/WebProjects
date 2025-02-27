<?php
session_start();

// Oturum kontrolünü düzelt
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: giris.php");
    exit;
} elseif (isset($_SESSION['admin_id'])) {
    // Admin için ekstra kontrol
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: admin_giris.php");
        exit;
    }
}

// Veritabanı bağlantısı
$conn = mysqli_connect(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');

// Bağlantı hatasını kontrol et
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// Karakter setini UTF-8 olarak ayarla
mysqli_set_charset($conn, "utf8");

// Kullanıcı ID'sini belirle (admin veya normal kullanıcı)
$kullanici_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : $_SESSION['user_id'];

// Filtreleme için POST verisini al
$filtre_durum = isset($_POST['durum']) ? mysqli_real_escape_string($conn, $_POST['durum']) : '';
$filtre_talep_turu = isset($_POST['talepTuru']) ? mysqli_real_escape_string($conn, $_POST['talepTuru']) : '';

// Sayfa başına gösterilecek kayıt sayısı
$sayfa_basina_kayit = 6;

// Mevcut sayfa numarası
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$baslangic = ($sayfa - 1) * $sayfa_basina_kayit;

// SQL sorgusunu filtreye göre düzenle
$sql = "SELECT id, tarih_saat, personel_adi, personel_birimi, talep_turu, detay, durum FROM bildirimler WHERE kullanici_id = ?";

// Durum filtresi uygulanmışsa, sorguya ekle
if (!empty($filtre_durum)) {
    $sql .= " AND durum = '$filtre_durum'";
}


// Talep türü filtresi uygulanmışsa, sorguya ekle
if (!empty($filtre_talep_turu)) {
    $sql .= " AND talep_turu = '$filtre_talep_turu'";
}

$sql .= " ORDER BY tarih_saat DESC LIMIT $baslangic, $sayfa_basina_kayit";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    die("SQL hazırlama hatası: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $kullanici_id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $tarih_saat,
            $personel_adi,
            $personel_birimi,
            $talep_turu,
            $detay,
            $durum
        );

        $result = array();
        while (mysqli_stmt_fetch($stmt)) {
            $result[] = array(
                'id' => $id,
                'tarih_saat' => $tarih_saat,
                'personel_adi' => $personel_adi,
                'personel_birimi' => $personel_birimi,
                'talep_turu' => $talep_turu,
                'detay' => $detay,
                'durum' => $durum
            );
        }
    } else {
        $result = array(); // Sonuç yoksa boş dizi ata
    }
} else {
    die("Sorgu yürütme hatası: " . mysqli_error($conn));
}

mysqli_stmt_close($stmt);

// Toplam kayıt sayısını hesapla
$sql_toplam = "SELECT COUNT(*) as toplam FROM bildirimler WHERE kullanici_id = ?";
if (!empty($filtre_durum)) {
    $sql_toplam .= " AND durum = '$filtre_durum'";
}

$stmt_toplam = mysqli_prepare($conn, $sql_toplam);
mysqli_stmt_bind_param($stmt_toplam, "i", $kullanici_id);
mysqli_stmt_execute($stmt_toplam);
mysqli_stmt_bind_result($stmt_toplam, $toplam_kayit);
mysqli_stmt_fetch($stmt_toplam);
mysqli_stmt_close($stmt_toplam);

// Toplam sayfa sayısını hesapla
$toplam_sayfa = ceil($toplam_kayit / $sayfa_basina_kayit);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taleplerim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="theme-color" content="#222930">
    <style>
    * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #ededed, #ececec);
            margin: 0;
            padding: 0;
        }

        .baslik {
            font-size: clamp(18px, 4vw, 28px);
            font-weight: 100;
            text-align: center;
            margin: 10px 0;
            word-wrap: break-word;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Genel buton stili (çıkış butonu hariç) */
        button:not(.menu-button-danger),
        input[type="submit"],
        input[type="reset"],
        .btn {
             background-color: #826A56;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:not(.menu-button-danger):hover,
        input[type="submit"]:hover,
        input[type="reset"]:hover,
        .btn:hover {
            background-color: #463629;
        }


        .araBtn {
             background-color: #826A56;
            color: white;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .araBtn:hover {
            background-color: #463629;
        }

        .arama-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }

        .arama-container input,
        .arama-container select {
            padding: 10px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px;
            box-sizing: border-box;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-top: 60px;
        }

        .bildirimler-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .bildirim {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .bildirim:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
        }

        .bildirim p {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .bildirim p strong {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            width: 100%;
            padding: -35px 0;
            /* üst ve alttan boşluk ekliyoruz */
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: 20px;
            /* üstten boşluk */
        }

        /* Durum badge'leri için stiller */
        .durum-badge {
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
        }

        .durum-beklemede {
            background-color: #ffc107;
            color: white;
        }

        .durum-yapildi,
        .durum-yapıldı {
            background-color: #28a745;
            color: white;
        }

        .durum-yapilmadi,
        .durum-yapılmadı {
            background-color: #dc3545;
            color: white;
        }

        .filter-divider {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ddd;
            width: 100%;
        }

        .durum-filter {
            margin-top: 10px;
        }

        .durum-filter select {
            min-width: 200px;
        }

        .arama-container input[type="date"] {
            padding: 10px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
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
                 padding-top: 80px; /* Menü yüksekliği kadar boşluk bırak */
                }
            .menu-container {
                padding: 0 10px;
            }

            .menu-button {
                font-size: 12px;
                padding: 6px 12px;
            }
        }

        /* Eski butonları kaldır */
        .button-container,
        .user-info,
        .logout-btn {
            display: none;
        }

        /* Mevcut CSS'e eklenecek yeni stiller */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            margin: 0;
            color: #666;
            font-size: 16px;
        }

        .stat-card p {
            margin: 10px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #4e73df;
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-box h3 {
            margin: 0 0 20px;
            color: #333;
            font-size: 18px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .page-link {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #222930;
            background-color: white;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: #222930;
            color: white;
        }

        .page-link.active {
            background-color: #222930;
            color: white;
            border-color: #222930;
        }

        /* Arama container stil */
        .arama-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Arama form stil */
        .arama-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        /* Form grup stil */
        .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }

        /* Label stil */
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        /* Input ve select stil */
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }

        /* Arama butonu stil */
        .araBtn {
             background-color: #826A56;
            color: white;
            padding: 9px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            height: 38px;
        }

        .araBtn:hover {
            background-color: #463629;
        }

        /* Responsive düzenlemeler */
        @media (max-width: 768px) {
            .arama-form {
                flex-direction: column;
            }

            .form-group {
                width: 100%;
                margin-bottom: 10px;
            }

            .araBtn {
                width: 100%;
            }
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
    </style>

</head>

<body>
    <div class="top-menu">
        <div class="menu-container">
            <a href="talepbildirim.php" class="menu-button">
                <i class="fas fa-plus"></i> Yeni Talep Bildirimi
            </a>
            <a href="cikis.php" class="menu-button menu-button-danger">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>

    <div class="container">
        <h1 class="baslik">Bildirdiğim Talepler</h1>
        <div class="arama-container">
            <form method="POST" class="arama-form">

        <div class="form-group">
                    <label for="talepTuru">Talep Edilen Birim:</label>
                    <select id="talepTuru" name="talepTuru">
                        <option value="">Tüm Birimler</option>
                        <option value="Belediye Başkanlığı" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Belediye Başkanlığı' ? 'selected' : ''; ?>>Belediye Başkanlığı</option>
                        <option value="Basın Yayın ve Halkla İlişkiler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Basın Yayın ve Halkla İlişkiler Müdürlüğü' ? 'selected' : ''; ?>>Basın Yayın ve Halkla İlişkiler Müdürlüğü</option>
                        <option value="Bilgi İşlem Daire Başkanlığı" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Bilgi İşlem Daire Başkanlığı' ? 'selected' : ''; ?>>Bilgi İşlem Daire Başkanlığı</option>
                        <option value="Destek Hizmetleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Destek Hizmetleri Müdürlüğü' ? 'selected' : ''; ?>>Destek Hizmetleri Müdürlüğü</option>
                        <option value="Fen İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Fen İşleri Müdürlüğü' ? 'selected' : ''; ?>>Fen İşleri Müdürlüğü</option>
                        <option value="Hukuk İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Hukuk İşleri Müdürlüğü' ? 'selected' : ''; ?>>Hukuk İşleri Müdürlüğü</option>
                        <option value="İmar ve Şehircilik Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İmar ve Şehircilik Müdürlüğü' ? 'selected' : ''; ?>>İmar ve Şehircilik Müdürlüğü</option>
                        <option value="İnsan Kaynakları ve Eğitim Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İnsan Kaynakları ve Eğitim Müdürlüğü' ? 'selected' : ''; ?>>İnsan Kaynakları ve Eğitim Müdürlüğü</option>
                        <option value="İşletme ve İştirakler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İşletme ve İştirakler Müdürlüğü' ? 'selected' : ''; ?>>İşletme ve İştirakler Müdürlüğü</option>
                        <option value="Kültür ve Sosyal İşler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Kültür ve Sosyal İşler Müdürlüğü' ? 'selected' : ''; ?>>Kültür ve Sosyal İşler Müdürlüğü</option>
                        <option value="Mali Hizmetler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Mali Hizmetler Müdürlüğü' ? 'selected' : ''; ?>>Mali Hizmetler Müdürlüğü</option>
                        <option value="Muhtarlık İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Muhtarlık İşleri Müdürlüğü' ? 'selected' : ''; ?>>Muhtarlık İşleri Müdürlüğü</option>
                        <option value="Park ve Bahçeler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Park ve Bahçeler Müdürlüğü' ? 'selected' : ''; ?>>Park ve Bahçeler Müdürlüğü</option>
                        <option value="Tarımsal Hizmetler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Tarımsal Hizmetler Müdürlüğü' ? 'selected' : ''; ?>>Tarımsal Hizmetler Müdürlüğü</option>
                        <option value="Temizlik İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Temizlik İşleri Müdürlüğü' ? 'selected' : ''; ?>>Temizlik İşleri Müdürlüğü</option>
                        <option value="Yapı Kontrol Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Yapı Kontrol Müdürlüğü' ? 'selected' : ''; ?>>Yapı Kontrol Müdürlüğü</option>
                        <option value="Yazı İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Yazı İşleri Müdürlüğü' ? 'selected' : ''; ?>>Yazı İşleri Müdürlüğü</option>
                        <option value="Zabıta Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Zabıta Müdürlüğü' ? 'selected' : ''; ?>>Zabıta Müdürlüğü</option>
                    </select>
                </div>

            <div class="form-group">
                <label for="durum">Durum:</label>
                    <select id="durum" name="durum">
                        <option value="">Tüm Durumlar</option>
                        <option value="Beklemede" <?php echo isset($_POST['durum']) && $_POST['durum'] == 'Beklemede' ? 'selected' : ''; ?>>Beklemede</option>
                        <option value="Yapıldı" <?php echo isset($_POST['durum']) && $_POST['durum'] == 'Yapıldı' ? 'selected' : ''; ?>>Yapıldı</option>
                        <option value="Yapılmadı" <?php echo isset($_POST['durum']) && $_POST['durum'] == 'Yapılmadı' ? 'selected' : ''; ?>>Yapılmadı</option>
                    </select>
            </div>   

            <button type="submit" class="araBtn">Ara</button>
            </form>
        </div>

        <div class="bildirimler-container">
            <?php
            if (!empty($result)) {
                foreach ($result as $row) {
                    $tarihSaat = date('d.m.Y H:i', strtotime($row['tarih_saat']));
                    $durum_sinifi = 'durum-' . strtolower(str_replace(
                        ['İ', 'ı', 'Ğ', 'ğ', 'Ü', 'ü', 'Ş', 'ş', 'Ö', 'ö', 'Ç', 'ç', ' ', '-'],
                        ['i', 'i', 'g', 'g', 'u', 'u', 's', 's', 'o', 'o', 'c', 'c', '-', '-'],
                        $row['durum']
                    ));

                    echo "<div class='bildirim'>
                            <p><strong>Tarih ve Saat:</strong> {$tarihSaat}</p>
                            <p><strong>Talep Eden Kişi:</strong> " . htmlspecialchars($row['personel_adi'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p><strong>Mahalle:</strong> " . htmlspecialchars($row['personel_birimi'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p><strong>Talep Edilen Birim:</strong> " . htmlspecialchars($row['talep_turu'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p><strong>Talep Numarası:</strong> " . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</p>                                
                            <p><strong>Talep Detayı:</strong> " . htmlspecialchars($row['detay'], ENT_QUOTES, 'UTF-8') . "</p>
                            <p><strong>Durum:</strong> <span class='durum-badge {$durum_sinifi}'>" . htmlspecialchars($row['durum'], ENT_QUOTES, 'UTF-8') . "</span></p>
                        </div>";
                }
            } else {
                echo "<p>Henüz talep bildiriminiz bulunmamaktadır.</p>";
            }
            ?>
        </div>

        <div class="pagination">
            <?php
            for ($i = 1; $i <= $toplam_sayfa; $i++) {
                $active = ($i == $sayfa) ? 'active' : '';
                // Mevcut filtreleri URL'de koru
                $params = array_merge($_GET, ['sayfa' => $i]);
                if (isset($_POST['personelAdi']))
                    $params['personelAdi'] = $_POST['personelAdi'];
                if (isset($_POST['personelBirimi']))
                    $params['personelBirimi'] = $_POST['personelBirimi'];
                if (isset($_POST['talepTuru']))
                    $params['talepTuru'] = $_POST['talepTuru'];
                if (isset($_POST['durum']))
                    $params['durum'] = $_POST['durum'];

                $query_string = http_build_query($params);
                echo "<a href='?{$query_string}' class='page-link {$active}'>{$i}</a>";
            }
            ?>
        </div>
    </div>
</body>
<footer>
    <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
    <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
</footer>
<!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->
</html>