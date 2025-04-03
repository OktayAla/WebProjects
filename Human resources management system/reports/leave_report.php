<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

$stmt = $pdo->prepare("SELECT e.name, 
       SUM(CASE WHEN leave_type = 'Yıllık İzin' THEN 1 ELSE 0 END) as annual_leave,
       SUM(CASE WHEN leave_type = 'Raporlu' THEN 1 ELSE 0 END) as sick_leave,
       COUNT(*) as total_leave
       FROM leave_requests lr
       JOIN employees e ON lr.employee_id = e.id
       WHERE YEAR(start_date) = ?
       GROUP BY e.id, e.name");
$stmt->execute([$year]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Raporu</title>
</head>
<body>
    <h2><?php echo $year; ?> Yılı İzin Raporu</h2>
    
    <form method="GET">
        <select name="year">
            <?php for($i = date('Y'); $i >= date('Y')-5; $i--): ?>
                <option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
        <button type="submit">Filtrele</button>
    </form>

    <table border="1">
        <tr>
            <th>Çalışan</th>
            <th>Yıllık İzin</th>
            <th>Rapor</th>
            <th>Toplam</th>
        </tr>
        <?php foreach($reports as $report): ?>
        <tr>
            <td><?php echo htmlspecialchars($report['name']); ?></td>
            <td><?php echo $report['annual_leave']; ?></td>
            <td><?php echo $report['sick_leave']; ?></td>
            <td><?php echo $report['total_leave']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
