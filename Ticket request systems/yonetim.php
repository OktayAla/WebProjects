<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('default_charset', 'utf-8');
mb_internal_encoding('UTF-8');

session_start();

// Oturum kontrolü
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: admin_giris.php");
    exit;
}

// Veritabanı bağlantısı
$conn = new mysqli(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');
$conn->set_charset("utf8");

// Bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Durum güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['durum_guncelle'])) {
    $bildirim_id = $_POST['bildirim_id'];
    $yeni_durum = $_POST['yeni_durum'];

    // Mevcut filtreleri sakla
    $personelAdi = isset($_POST['current_personelAdi']) ? $_POST['current_personelAdi'] : '';
    $personelBirimi = isset($_POST['current_personelBirimi']) ? $_POST['current_personelBirimi'] : '';
    $talepTuru = isset($_POST['current_talepTuru']) ? $_POST['current_talepTuru'] : '';

    $guncelle_sql = "UPDATE bildirimler SET durum = ? WHERE id = ?";
    $stmt = $conn->prepare($guncelle_sql);
    $stmt->bind_param("si", $yeni_durum, $bildirim_id);
    $stmt->execute();
    $stmt->close();

    // POST verilerini oluştur
    $post_data = array();
    if (!empty($personelAdi))
        $post_data['personelAdi'] = $personelAdi;
    if (!empty($personelBirimi))
        $post_data['personelBirimi'] = $personelBirimi;
    if (!empty($talepTuru))
        $post_data['talepTuru'] = $talepTuru;

    // Filtreleri koruyarak form submit et
    echo "<form id='redirectForm' method='POST' action='yonetim.php'>";
    foreach ($post_data as $key => $value) {
        echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
    }
    echo "</form>";
    echo "<script>document.getElementById('redirectForm').submit();</script>";
    exit;
}

// Fonksiyon: Filtreleri kullanarak SQL sorgusunu oluştur
function buildSqlQuery($conn) {
    $sql = "SELECT * FROM bildirimler WHERE 1=1";

    // GET metoduyla gelen parametreleri kontrol et
    if (isset($_GET['personelAdi']) && !empty($_GET['personelAdi'])) {
        $personelAdi = mysqli_real_escape_string($conn, $_GET['personelAdi']);
        $sql .= " AND personel_adi LIKE '%$personelAdi%'";
    }

    if (isset($_GET['personelBirimi']) && !empty($_GET['personelBirimi'])) {
        $personelBirimi = mysqli_real_escape_string($conn, $_GET['personelBirimi']);
        $sql .= " AND personel_birimi LIKE '%$personelBirimi%'";
    }

    if (isset($_GET['talepTuru']) && !empty($_GET['talepTuru'])) {
        $talepTuru = mysqli_real_escape_string($conn, $_GET['talepTuru']);
        $sql .= " AND talep_turu LIKE '%$talepTuru%'";
    }

    if (isset($_GET['durum']) && !empty($_GET['durum'])) {
        $durum = mysqli_real_escape_string($conn, $_GET['durum']);
        $sql .= " AND durum = '$durum'";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" || (isset($_GET['excel']) && $_GET['excel'] == 'true')) {
        $request = ($_SERVER["REQUEST_METHOD"] == "POST") ? $_POST : $_GET;

        if (!empty($request['personelAdi'])) {
            $personelAdi = mysqli_real_escape_string($conn, $request['personelAdi']);
            $sql .= " AND personel_adi LIKE '%$personelAdi%'";
        }

        if (!empty($request['personelBirimi'])) {
            $personelBirimi = mysqli_real_escape_string($conn, $request['personelBirimi']);
            $sql .= " AND personel_birimi LIKE '%$personelBirimi%'";
        }

        if (!empty($request['talepTuru'])) {
            $talepTuru = mysqli_real_escape_string($conn, $request['talepTuru']);
            $sql .= " AND talep_turu LIKE '%$talepTuru%'";
        }

        if (!empty($request['tarihBaslangic']) && !empty($request['tarihBitis'])) {
            $tarihBaslangic = mysqli_real_escape_string($conn, $request['tarihBaslangic']);
            $tarihBitis = mysqli_real_escape_string($conn, $request['tarihBitis']);
            $sql .= " AND tarih_saat BETWEEN '$tarihBaslangic' AND '$tarihBitis'";
        }

        if (!empty($request['durum'])) {
            $durum = mysqli_real_escape_string($conn, $request['durum']);
            $sql .= " AND durum = '$durum'";
        }

        if (!empty($request['talepId'])) {
            $talepId = mysqli_real_escape_string($conn, $request['talepId']);
            $sql .= " AND id = '$talepId'";
        }
    }

    $sql .= " ORDER BY tarih_saat DESC";
    return $sql;
}

// Ana SQL sorgusunu oluştur
$sql = buildSqlQuery($conn);

// SQL sorgusunu kopyala (sayfalama için)
$sql_for_display = $sql;

// Sayfa numarası ve sayfa başına gösterilecek kayıt sayısı
$sayfa = isset($_GET['sayfa']) ? (int) $_GET['sayfa'] : 1;
$kayit_sayisi = 6;
$baslangic = ($sayfa - 1) * $kayit_sayisi;

// Toplam kayıt sayısını al
$toplam_kayit_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$toplam_kayit_result = $conn->query($toplam_kayit_sql);
$toplam_kayit = $toplam_kayit_result->fetch_assoc()['total'];
$toplam_sayfa = ceil($toplam_kayit / $kayit_sayisi);

// SQL sorgusuna LIMIT ekle
$sql .= " LIMIT $baslangic, $kayit_sayisi";
$result = $conn->query($sql);

// Eğer 'Bütün Sonuçları Göster' butonuna tıklanmışsa, filtreleri temizleyip tüm verileri al
if (isset($_POST['resetFilter'])) {
    $sql = "SELECT * FROM bildirimler ORDER BY tarih_saat DESC";
    $result = $conn->query($sql);
}

// SQL sorgusunu kopyala
$sql_for_display = $sql;

// Excel'e aktarılacak verileri bir diziye at (sadece mevcut sayfadaki veriler)
$excel_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $excel_data[] = [
            'Talep Numarası' => $row['id'],
            'Tarih ve Saat' => date('d.m.Y H:i', strtotime($row['tarih_saat'])),
            'Talep Eden Kişi' => htmlspecialchars($row['personel_adi'] ?? ''),
            'Mahalle' => htmlspecialchars($row['personel_birimi'] ?? ''),
            'Talep Edilen Birim' => htmlspecialchars($row['talep_turu'] ?? ''),
            'Talep Detayı' => htmlspecialchars($row['detay'] ?? ''),
            'Durum' => htmlspecialchars($row['durum'] ?? '')
        ];
    }
}

// Veriyi JSON formatına dönüştür (mevcut sayfadaki veriler)
$json_data = json_encode($excel_data, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Takip Yönetim Paneli</title>
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

        .search-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-container input,
        .search-container select {
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

        .search-container input[type="date"] {
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

        .durum-select {
            padding: 5px;
            margin: 5px;
            border-radius: 4px;
        }

        .durum-form {
            display: inline-block;
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

            <a href="index.php" class="menu-button">
                <i class="fas fa-home"></i> Anasayfa
            </a>

            <a href="taleptakip.php" class="menu-button">
                <i class="fas fa-table"></i> Tüm Talepler
            </a>

            <a href="talepistatistik.php" class="menu-button">
                <i class="fas fa-chart-line"></i> Talep İstatistik Sayfası
            </a>

            <a href="yonetim.php" class="menu-button">
                <i class="fas fa-tasks"></i> Yönetim Sayfası
            </a>

            <a href="talepbildirim.php" class="menu-button">
                <i class="fas fa-plus-circle"></i> Yeni Talep Bildirimi
            </a>

            <a href="cikis.php" class="menu-button menu-button-danger">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>

    <div class="container">
        <h1 class="baslik">Talep Takip Yönetim Paneli</h1>

        <div class="arama-container">
            <form method="POST" class="arama-form">
                <div class="form-group">
                    <label for="personelAdi">Kullanıcı Adı:</label>
                    <input type="text" id="personelAdi" name="personelAdi"
                        value="<?php echo isset($_POST['personelAdi']) ? htmlspecialchars($_POST['personelAdi']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="personelBirimi">Mahalle:</label>
                    <select id="personelBirimi" name="personelBirimi">
                        <option value="">Tüm Mahalleler</option>
                        <option value="Alaçam Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Alaçam Mahallesi' ? 'selected' : ''; ?>>Alaçam Mahallesi</option>
                        <option value="Atakent Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Atakent Mahallesi' ? 'selected' : ''; ?>>Atakent Mahallesi
                        </option>
                        <option value="Aydoğdu Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Aydoğdu Mahallesi' ? 'selected' : ''; ?>>Aydoğdu Mahallesi
                        </option>
                        <option value="Bağcağız Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Bağcağız Mahallesi' ? 'selected' : ''; ?>>Bağcağız Mahallesi
                        </option>
                        <option value="Bahçeköy Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Bahçeköy Mahallesi' ? 'selected' : ''; ?>>Bahçeköy Mahallesi
                        </option>
                        <option value="Bozkır Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Bozkır Mahallesi' ? 'selected' : ''; ?>>Bozkır Mahallesi</option>
                        <option value="Cumhuriyet Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Cumhuriyet Mahallesi' ? 'selected' : ''; ?>>Cumhuriyet Mahallesi
                        </option>
                        <option value="Çaltılı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Çaltılı Mahallesi' ? 'selected' : ''; ?>>Çaltılı Mahallesi
                        </option>
                        <option value="Çamlıca Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Çamlıca Mahallesi' ? 'selected' : ''; ?>>Çamlıca Mahallesi
                        </option>
                        <option value="Çınarlı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Çınarlı Mahallesi' ? 'selected' : ''; ?>>Çınarlı Mahallesi
                        </option>
                        <option value="Çivril Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Çivril Mahallesi' ? 'selected' : ''; ?>>Çivril Mahallesi</option>
                        <option value="Değirmenli Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Değirmenli Mahallesi' ? 'selected' : ''; ?>>Değirmenli Mahallesi
                        </option>
                        <option value="Dereköy Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Dereköy Mahallesi' ? 'selected' : ''; ?>>Dereköy Mahallesi
                        </option>
                        <option value="Fatih Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Fatih Mahallesi' ? 'selected' : ''; ?>>Fatih Mahallesi</option>
                        <option value="Göksu Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Göksu Mahallesi' ? 'selected' : ''; ?>>Göksu Mahallesi</option>
                        <option value="Güme Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Güme Mahallesi' ? 'selected' : ''; ?>>Güme Mahallesi</option>
                        <option value="Güzelköy Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Güzelköy Mahallesi' ? 'selected' : ''; ?>>Güzelköy Mahallesi
                        </option>
                        <option value="Hisar Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Hisar Mahallesi' ? 'selected' : ''; ?>>Hisar Mahallesi</option>
                        <option value="İstiklal Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'İstiklal Mahallesi' ? 'selected' : ''; ?>>İstiklal Mahallesi
                        </option>
                        <option value="Karaağaç Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Karaağaç Mahallesi' ? 'selected' : ''; ?>>Karaağaç Mahallesi
                        </option>
                        <option value="Kavaklı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Kavaklı Mahallesi' ? 'selected' : ''; ?>>Kavaklı Mahallesi
                        </option>
                        <option value="Kışla Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Kışla Mahallesi' ? 'selected' : ''; ?>>Kışla Mahallesi</option>
                        <option value="Köselerli Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Köselerli Mahallesi' ? 'selected' : ''; ?>>Köselerli Mahallesi
                        </option>
                        <option value="Köycük Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Köycük Mahallesi' ? 'selected' : ''; ?>>Köycük Mahallesi</option>
                        <option value="Küçük Köy Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Küçük Köy Mahallesi' ? 'selected' : ''; ?>>Küçük Köy Mahallesi
                        </option>
                        <option value="Merkez Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Merkez Mahallesi' ? 'selected' : ''; ?>>Merkez Mahallesi</option>
                        <option value="Meydan Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Meydan Mahallesi' ? 'selected' : ''; ?>>Meydan Mahallesi</option>
                        <option value="Narlı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Narlı Mahallesi' ? 'selected' : ''; ?>>Narlı Mahallesi</option>
                        <option value="Ortaören Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Ortaören Mahallesi' ? 'selected' : ''; ?>>Ortaören Mahallesi
                        </option>
                        <option value="Özlü Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Özlü Mahallesi' ? 'selected' : ''; ?>>Özlü Mahallesi</option>
                        <option value="Pınarbaşı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Pınarbaşı Mahallesi' ? 'selected' : ''; ?>>Pınarbaşı Mahallesi
                        </option>
                        <option value="Sarıkavak Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Sarıkavak Mahallesi' ? 'selected' : ''; ?>>Sarıkavak Mahallesi
                        </option>
                        <option value="Selamlı Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Selamlı Mahallesi' ? 'selected' : ''; ?>>Selamlı Mahallesi
                        </option>
                        <option value="Taşhan Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Taşhan Mahallesi' ? 'selected' : ''; ?>>Taşhan Mahallesi</option>
                        <option value="Yalnızcabağ Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Yalnızcabağ Mahallesi' ? 'selected' : ''; ?>>Yalnızcabağ
                            Mahallesi</option>
                        <option value="Yeniköy Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Yeniköy Mahallesi' ? 'selected' : ''; ?>>Yeniköy Mahallesi
                        </option>
                        <option value="Yeşilyurt Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Yeşilyurt Mahallesi' ? 'selected' : ''; ?>>Yeşilyurt Mahallesi
                        </option>
                        <option value="Yukarıköselerli Mahallesi" <?php echo isset($_POST['personelBirimi']) && $_POST['personelBirimi'] == 'Yukarıköselerli Mahallesi' ? 'selected' : ''; ?>>Yukarıköselerli
                            Mahallesi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="talepTuru">Talep Edilen Birim:</label>
                    <select id="talepTuru" name="talepTuru">
                        <option value="">Tüm Birimler</option>
                        <option value="Belediye Başkanlığı" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Belediye Başkanlığı' ? 'selected' : ''; ?>>Belediye Başkanlığı
                        </option>
                        <option value="Basın Yayın ve Halkla İlişkiler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Basın Yayın ve Halkla İlişkiler Müdürlüğü' ? 'selected' : ''; ?>>
                            Basın Yayın ve Halkla İlişkiler Müdürlüğü</option>
                        <option value="Bilgi İşlem Daire Başkanlığı" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Bilgi İşlem Daire Başkanlığı' ? 'selected' : ''; ?>>Bilgi İşlem Daire
                            Başkanlığı</option>
                        <option value="Destek Hizmetleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Destek Hizmetleri Müdürlüğü' ? 'selected' : ''; ?>>Destek Hizmetleri
                            Müdürlüğü</option>
                        <option value="Fen İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Fen İşleri Müdürlüğü' ? 'selected' : ''; ?>>Fen İşleri Müdürlüğü
                        </option>
                        <option value="Hukuk İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Hukuk İşleri Müdürlüğü' ? 'selected' : ''; ?>>Hukuk İşleri Müdürlüğü
                        </option>
                        <option value="İmar ve Şehircilik Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İmar ve Şehircilik Müdürlüğü' ? 'selected' : ''; ?>>İmar ve Şehircilik
                            Müdürlüğü</option>
                        <option value="İnsan Kaynakları ve Eğitim Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İnsan Kaynakları ve Eğitim Müdürlüğü' ? 'selected' : ''; ?>>İnsan
                            Kaynakları ve Eğitim Müdürlüğü</option>
                        <option value="İşletme ve İştirakler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'İşletme ve İştirakler Müdürlüğü' ? 'selected' : ''; ?>>İşletme ve
                            İştirakler Müdürlüğü</option>
                        <option value="Kültür ve Sosyal İşler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Kültür ve Sosyal İşler Müdürlüğü' ? 'selected' : ''; ?>>Kültür ve
                            Sosyal İşler Müdürlüğü</option>
                        <option value="Mali Hizmetler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Mali Hizmetler Müdürlüğü' ? 'selected' : ''; ?>>Mali Hizmetler
                            Müdürlüğü</option>
                        <option value="Muhtarlık İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Muhtarlık İşleri Müdürlüğü' ? 'selected' : ''; ?>>Muhtarlık İşleri
                            Müdürlüğü</option>
                        <option value="Park ve Bahçeler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Park ve Bahçeler Müdürlüğü' ? 'selected' : ''; ?>>Park ve Bahçeler
                            Müdürlüğü</option>
                        <option value="Tarımsal Hizmetler Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Tarımsal Hizmetler Müdürlüğü' ? 'selected' : ''; ?>>Tarımsal Hizmetler
                            Müdürlüğü</option>
                        <option value="Temizlik İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Temizlik İşleri Müdürlüğü' ? 'selected' : ''; ?>>Temizlik İşleri
                            Müdürlüğü</option>
                        <option value="Yapı Kontrol Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Yapı Kontrol Müdürlüğü' ? 'selected' : ''; ?>>Yapı Kontrol Müdürlüğü
                        </option>
                        <option value="Yazı İşleri Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Yazı İşleri Müdürlüğü' ? 'selected' : ''; ?>>Yazı İşleri Müdürlüğü
                        </option>
                        <option value="Zabıta Müdürlüğü" <?php echo isset($_POST['talepTuru']) && $_POST['talepTuru'] == 'Zabıta Müdürlüğü' ? 'selected' : ''; ?>>Zabıta Müdürlüğü</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="talepId">Talep Numarası:</label>
                    <input type="text" id="talepId" name="talepId"
                        value="<?php echo isset($_POST['talepId']) ? htmlspecialchars($_POST['talepId']) : ''; ?>">
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
                            <p><strong>Tarih ve Saat:</strong> $tarihSaat</p>
                            <p><strong>Talep Eden Kişi:</strong> " . $row['personel_adi'] . "</p>
                            <p><strong>Mahalle:</strong> " . $row['personel_birimi'] . "</p>
                            <p><strong>Talep Edilen Birim:</strong> " . $row['talep_turu'] . "</p>
                            <p><strong>Talep Numarası:</strong> " . htmlspecialchars($row['id'] ?? '') . "</p>
                            <p><strong>Talep Detayı:</strong> " . $row['detay'] . "</p>
                            <p><strong>Durum:</strong> <span class='durum-badge " . ($row['durum'] == 'Beklemede' ? 'durum-beklemede' : ($row['durum'] == 'Yapıldı' ? 'durum-yapildi' : 'durum-yapilmadi')) . "'>" . $row['durum'] . "</span></p>
                            <form method='POST' class='durum-form'>
                                <input type='hidden' name='bildirim_id' value='" . $row['id'] . "'>
                                <select name='yeni_durum' class='durum-select'>
                                    <option value='Beklemede' " . ($row['durum'] == 'Beklemede' ? 'selected' : '') . ">Beklemede</option>
                                    <option value='Yapıldı' " . ($row['durum'] == 'Yapıldı' ? 'selected' : '') . ">Yapıldı</option>
                                    <option value='Yapılmadı' " . ($row['durum'] == 'Yapılmadı' ? 'selected' : '') . ">Yapılmadı</option>
                                </select>
                                <input type='hidden' name='current_personelAdi' value='" . (isset($_POST['personelAdi']) ? htmlspecialchars($_POST['personelAdi']) : '') . "'>
                                <input type='hidden' name='current_personelBirimi' value='" . (isset($_POST['personelBirimi']) ? htmlspecialchars($_POST['personelBirimi']) : '') . "'>
                                <input type='hidden' name='current_talepTuru' value='" . (isset($_POST['talepTuru']) ? htmlspecialchars($_POST['talepTuru']) : '') . "'>
                                <button type='submit' name='durum_guncelle' class='durum-btn'>Durumu Güncelle</button>
                            </form>
                        </div>";
                }
            } else {
                echo "<p>Talep bildirimi bulunamadı.</p>";
            }
            ?>
        </div>

        <!-- Sayfalama linklerini ekle -->
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
    <script>
        setTimeout(function () {
            location.reload();
        }, 20000);  // 20 saniye = 20000 milisaniye
    </script>
</body>

<footer>
    <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
    <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
</footer>
<!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->

</html>