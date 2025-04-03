<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../templates/header.php';

$user = getUserById($_SESSION['user_id']);
$total_employees = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='Aktif'")->fetchColumn();
$pending_leaves = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status='Beklemede'")->fetchColumn();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Aktif Çalışanlar</h5>
                <h2 class="card-text"><?php echo $total_employees; ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bekleyen İzinler</h5>
                <h2 class="card-text"><?php echo $pending_leaves; ?></h2>
            </div>
        </div>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>
