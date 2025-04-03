<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

session_start();
checkLogin();

$stmt = $pdo->query("SELECT id, name, email, position FROM employees WHERE status='Aktif'");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışan Listesi</title>
</head>
<body>
    <h2>Çalışan Listesi</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>E-Posta</th>
            <th>Pozisyon</th>
            <th>Detay</th>
        </tr>
        <?php foreach($employees as $emp): ?>
        <tr>
            <td><?php echo $emp['id']; ?></td>
            <td><?php echo htmlspecialchars($emp['name']); ?></td>
            <td><?php echo htmlspecialchars($emp['email']); ?></td>
            <td><?php echo htmlspecialchars($emp['position']); ?></td>
            <td><a href="employee_details.php?id=<?php echo $emp['id']; ?>">Görüntüle</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
