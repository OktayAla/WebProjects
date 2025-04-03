<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

// Kullanıcı bilgilerini al
$stmt = $pdo->query("SELECT lr.*, 
                            CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                            d.name as department_name
                     FROM leave_requests lr
                     JOIN employees e ON lr.employee_id = e.id
                     LEFT JOIN departments d ON e.department_id = d.id
                     ORDER BY lr.created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Talepleri</title>
</head>
<body>
    <h2>İzin Talepleri</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Çalışan</th>
            <th>İzin Türü</th>
            <th>Başlangıç</th>
            <th>Bitiş</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>

        <!-- PHP ile izin taleplerini listele --- -->
        <?php foreach ($requests as $request) : ?>
        <tr>
            <td><?php echo $request['id']; ?></td>
            <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
            <td><?php echo $request['leave_type']; ?></td>
            <td><?php echo $request['start_date']; ?></td>
            <td><?php echo $request['end_date']; ?></td>
            <td><?php echo $request['status']; ?></td>
            <td><a href="approve_leave.php?id=<?php echo $request['id']; ?>">Onayla</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
