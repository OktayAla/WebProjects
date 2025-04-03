<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
checkLogin();
checkRole(['admin', 'ik']);

// Veritabanından bütçe verilerini al
$stmt = $pdo->query("SELECT * FROM budget ORDER BY month_year DESC");
$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bütçe Raporu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Bütçe Raporu</h2>
    <canvas id="budgetChart"></canvas>

    <script>
        var ctx = document.getElementById('budgetChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($budgets as $b) { echo "'".$b['month_year']."',"; } ?>],
                datasets: [{
                    label: 'Toplam Bütçe (TL)',
                    data: [<?php foreach ($budgets as $b) { echo $b['estimated_total'].","; } ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
