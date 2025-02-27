<?php
// Veritabanı bağlantısı
$conn = new mysqli(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');

// Bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Karakter setini UTF-8 olarak ayarla
$conn->set_charset("utf8");

// Hata raporlamayı açalım (sadece geliştirme ortamında!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// İstatistiksel verileri çekmek için sorgular
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as count FROM bildirimler"),
    'status' => $conn->query("SELECT durum, COUNT(*) as count FROM bildirimler GROUP BY durum"),
    'departments' => $conn->query("SELECT personel_birimi, COUNT(*) as count FROM bildirimler GROUP BY personel_birimi"),
    'types' => $conn->query("SELECT talep_turu, COUNT(*) as count FROM bildirimler GROUP BY talep_turu"),
    'reporters' => $conn->query("SELECT personel_adi, COUNT(*) as count FROM bildirimler GROUP BY personel_adi ORDER BY count DESC LIMIT 10")
];

// Verileri JSON formatına çevirme
$totalCount = mysqli_fetch_assoc($stats['total'])['count'];

$statusData = [];
$departmentData = [];
$typeData = [];
$reporterData = [];

// Sorgu sonuçlarını kontrol edip verileri al
if ($stats['status']) {
    while ($row = mysqli_fetch_assoc($stats['status'])) {
        $statusData[$row['durum']] = $row['count'];
    }
}

if ($stats['departments']) {
    while ($row = mysqli_fetch_assoc($stats['departments'])) {
        $departmentData[$row['personel_birimi']] = $row['count'];
    }
}

if ($stats['types']) {
    while ($row = mysqli_fetch_assoc($stats['types'])) {
        $typeData[$row['talep_turu']] = $row['count'];
    }
}

if ($stats['reporters']) {
    while ($row = mysqli_fetch_assoc($stats['reporters'])) {
        $reporterData[$row['personel_adi']] = $row['count'];
    }
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep İstatistik Ekranı</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="theme-color" content="#222930">
    <style>
        :root {
            --theme_color: #404D5B;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #ededed, #ececec);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            /* Sayfanın her zaman en az ekran yüksekliğinde olmasını sağlar */
            color: #333;
            /* Genel metin rengini koyulaştırdık, okunabilirlik için */
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
            color: #333;
            /* Input ve select metin rengi */
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
            color: #fff;
            /* Footer rengini beyaz yaptık */
            width: 100%;
            padding: 20px 0;
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
            color: #333;
            /* Input ve select metin rengi */
        }

        .search-container input[type="date"] {
            padding: 10px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            color: #333;
            /* Input ve select metin rengi */
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
            /* Menü öğeleri sığmadığında alt satıra geçmesini sağlar */
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
            color: #826A56;
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


        .durum-select {
            padding: 5px;
            margin: 5px;
            border-radius: 4px;
        }

        .durum-form {
            display: inline-block;
        }

        .durum-btn {
            background-color: #826A56;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;

        }

        .durum-btn:hover {
            background-color: #463629;
            transition: background-color 0.3s ease;

        }

        .durum-beklemede {
            background-color: #ffc107;
        }

        .durum-yapildi {
            background-color: #28a745;
        }

        .durum-yapilmadi {
            background-color: #dc3545;
        }

        .durum-badge {
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
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

        /* Genel buton stili (çıkış butonu hariç) */
        button.btn,
        input[type="submit"].btn,
        input[type="reset"].btn,
        .btn {
            background-color: #826A56;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button.btn:hover,
        input[type="submit"].btn:hover,
        input[type="reset"].btn:hover,
        .btn:hover {
            background-color: #463629;
        }

        /* Çıkış butonunun stilini sıfırla */
        .menu-button-danger {
            background-color: #dc3545;
        }

        .menu-button-danger:hover {
            background-color: #c82333;
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
                <i class="fas fa-table"></i>Tüm Talepler
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
        <h1 class="baslik">TALEP İSTATİSTİK EKRANI</h1>

        <div class="stats-summary">
            <div class="stat-card">
                <h3>Bildirilen Toplam Talepler</h3>
                <p><?php echo $totalCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Yapılan Talepler</h3>
                <p><?php echo $statusData['Yapıldı'] ?? 0; ?></p>
            </div>
            <div class="stat-card">
                <h3>Yapılmayan Talepler</h3>
                <p><?php echo $statusData['Yapılmadı'] ?? 0; ?></p>
            </div>
            <div class="stat-card">
                <h3>Bekleyen Talepler</h3>
                <p><?php echo $statusData['Beklemede'] ?? 0; ?></p>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-box">
                <h3>Mevcut Durum Dağılımı</h3>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Talep Bildiren Mahalleler</h3>
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Talep Edilen Birimler</h3>
                <canvas id="typeChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Talep Bildiren Kullanıcılar</h3>
                <canvas id="reporterChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        const statusData = <?php echo json_encode($statusData); ?>;
        const departmentData = <?php echo json_encode($departmentData); ?>;
        const typeData = <?php echo json_encode($typeData); ?>;
        const reporterData = <?php echo json_encode($reporterData); ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js" defer></script>
    <script>
    // Pass PHP data to JavaScript
    window.appData = {
        statusData: <?php echo json_encode($statusData); ?>,
        departmentData: <?php echo json_encode($departmentData); ?>,
        typeData: <?php echo json_encode($typeData); ?>,
        reporterData: <?php echo json_encode($reporterData); ?>
    };
    </script>
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get the data from the window object
    const { statusData, departmentData, typeData, reporterData } = window.appData;

    // Helper function to destroy existing chart if it exists
    function destroyChart(chartId) {
        const chartInstance = Chart.getChart(chartId);
        if (chartInstance) {
            chartInstance.destroy();
        }
    }

    // Initialize Status Chart
    function initStatusChart() {
        destroyChart('statusChart');
        const ctx = document.getElementById('statusChart').getContext('2d');
        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(status => {
                        switch (status) {
                            case 'Yapıldı': return '#28a745';
                            case 'Yapılmadı': return '#dc3545';
                            case 'Beklemede': return '#ffc107';
                            default: return '#858796';
                        }
                    })
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = Object.keys(statusData)[index]; // Correctly get the label
                        window.location.href = `taleptakip.php?durum=${encodeURIComponent(label)}`;
                    }
                }
            }
        });
    }

    // Initialize Department Chart
    function initDepartmentChart() {
        destroyChart('departmentChart');
        const ctx = document.getElementById('departmentChart').getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(departmentData),
                datasets: [{
                    data: Object.values(departmentData),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#2c9faf', '#17a673', '#2e59d9'
                    ]
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                       const index = elements[0].index;
                        const label = Object.keys(departmentData)[index];
                        window.location.href = `taleptakip.php?personelBirimi=${encodeURIComponent(label)}`;
                    }
                }
            }
        });
    }

    // Initialize Type Chart
    function initTypeChart() {
        destroyChart('typeChart');
        const ctx = document.getElementById('typeChart').getContext('2d');
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#2c9faf', '#17a673', '#2e59d9'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                         const index = elements[0].index;
                        const label = Object.keys(typeData)[index];
                        window.location.href = `taleptakip.php?talepTuru=${encodeURIComponent(label)}`;
                    }
                }
            }
        });
    }

    // Initialize Reporter Chart
    function initReporterChart() {
        destroyChart('reporterChart');
        const ctx = document.getElementById('reporterChart').getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(reporterData),
                datasets: [{
                    data: Object.values(reporterData),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#2c9faf', '#17a673', '#2e59d9'
                    ]
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = Object.keys(reporterData)[index];
                        window.location.href = `taleptakip.php?personelAdi=${encodeURIComponent(label)}`;
                    }
                }
            }
        });
    }

    // Initialize all charts
    initStatusChart();
    initDepartmentChart();
    initTypeChart();
    initReporterChart();
});
    </script>

    <?php
    // taleptakip.php dosyasında filtreleme işlemleri
    if (isset($_GET['mahalle'])) {
        $mahalle = $_GET['mahalle'];
        // Mahalleye göre veritabanı sorgusu ekleyebilirsin
    }

    if (isset($_GET['durum'])) {
        $durum = $_GET['durum'];
        // Duruma göre veritabanı sorgusu ekleyebilirsin
    }
    ?>

    <?php mysqli_close($conn); ?>

    <footer>
        <p class="footer-text">Copyright © 2024 | Her hakkı saklıdır.</p>
        <p class="footer-text">OA Grafik Tasarım tarafından ♥ ile tasarlanmıştır.</p>
    </footer>
</body>
        <!-- Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  -->
</html>