<?php
require_once 'config/database.php';

$month_year = date('Y-m', strtotime("+1 month")); // Gelecek ay
$stmt = $pdo->query("SELECT SUM(salary) AS total_salary FROM employees"); // Tüm çalışanların maaşlarını topla
$row = $stmt->fetch(PDO::FETCH_ASSOC); // Sonucu al
$total_salary = $row['total_salary']; // Toplam maaş

$estimated_overtime = $total_salary * 0.05; // Ortalama %5 fazla mesai
$estimated_bonuses = $total_salary * 0.02; // Ortalama %2 bonus
$estimated_taxes = $total_salary * 0.15; // %15 vergi kesintisi
$estimated_total = $total_salary + $estimated_overtime + $estimated_bonuses + $estimated_taxes; // Toplam tahmini gider

// Veritabanına kaydet
$stmt = $pdo->prepare("INSERT INTO budget (month_year, estimated_salary, estimated_overtime, estimated_bonuses, estimated_taxes, estimated_total) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$month_year, $total_salary, $estimated_overtime, $estimated_bonuses, $estimated_taxes, $estimated_total]);

// Sonucu ekrana yazdır
echo "Bütçe hesaplandı: $month_year için toplam tahmini gider = $estimated_total TL";
?>
