<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM expense_report ORDER BY month_year DESC");
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personel Gider Raporu</title>
</head>
<body>
    <h2>Personel Gider Raporu</h2>
    <table border="1">
        <tr>
            <th>Ay</th>
            <th>Toplam Maaş</th>
            <th>Fazla Mesai</th>
            <th>Vergi Kesintileri</th>
            <th>Bonuslar</th>
            <th>Toplam Gider</th>
        </tr>
        <?php foreach ($expenses as $expense) : ?>
        <tr>
            <td><?php echo $expense['month_year']; ?></td>
            <td><?php echo number_format($expense['total_salary'], 2); ?> TL</td>
            <td><?php echo number_format($expense['overtime_cost'], 2); ?> TL</td>
            <td><?php echo number_format($expense['tax_cost'], 2); ?> TL</td>
            <td><?php echo number_format($expense['bonus_cost'], 2); ?> TL</td>
            <td><?php echo number_format($expense['total_expense'], 2); ?> TL</td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
