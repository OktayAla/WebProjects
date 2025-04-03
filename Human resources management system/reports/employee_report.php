<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

// Filtreleme seçenekleri ve rapor oluşturma işlemleri...

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışan Raporu</title>
</head>
<body>
    <h2>Çalışan Raporu</h2>
    <!-- Filtreleme Formu -->
    <form action="employee_report.php" method="GET">
        <!-- Filtreleme seçenekleri buraya gelecek -->
        <button type="submit">Rapor Oluştur</button>
    </form>

    <!-- Rapor Tablosu -->
    <table border="1">
        <tr>
            <th>Çalışan Adı</th>
            <th>İzin Sayısı</th>
            <th>Fazla Mesai Saati</th>
            <!-- Diğer sütunlar -->
        </tr>
        <!-- Veritabanından çekilen verilerle doldurulacak -->
    </table>
</body>
</html>
