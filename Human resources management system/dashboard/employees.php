<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
checkLogin();
$title = "Çalışanlar - İK Yönetim Sistemi";
require_once '../templates/header.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Eğer yönetici ise, sadece kendi şirketindeki çalışanları görebilir
if ($role === 'yonetici') {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE company_id = (SELECT company_id FROM users WHERE id = ?)");
    $stmt->execute([$user_id]);
} else {
    $stmt = $pdo->query("SELECT e.*, 
                            d.name as department_name, 
                            p.name as position_name,
                            c.name as company_name
                     FROM employees e
                     LEFT JOIN departments d ON e.department_id = d.id
                     LEFT JOIN positions p ON e.position_id = p.id
                     LEFT JOIN companies c ON e.company_id = c.id
                     WHERE e.status = 'aktif'
                     ORDER BY e.created_at DESC");
}

$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-3">Çalışan Listesi</h1>
    <div class="actions">
        <a href="add_employee.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Çalışan
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light" id="searchInput" 
                               placeholder="Çalışan ara...">
                    </div>
                </div>
                <div class="filters">
                    <select class="form-select bg-light border-0">
                        <option value="">Tüm Departmanlar</option>
                        <option value="IT">IT</option>
                        <option value="HR">İK</option>
                        <option value="Finance">Finans</option>
                    </select>
                </div>
            </div>

            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="sortable" data-sort="name">
                            <i class="fas fa-sort me-2"></i>Ad Soyad
                        </th>
                        <th>Departman</th>
                        <th>Pozisyon</th>
                        <th>İşe Giriş</th>
                        <th>Durum</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/assets/images/avatars/default.png" 
                                     class="rounded-circle me-2" width="32">
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($employee['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($employee['department_name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['position_name']); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($employee['start_date'])); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $employee['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                <?php echo $employee['status']; ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-sm" 
                                        onclick="location.href='view_employee.php?id=<?php echo $employee['id']; ?>'">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-light btn-sm" 
                                        onclick="location.href='edit_employee.php?id=<?php echo $employee['id']; ?>'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-light btn-sm" 
                                        onclick="deleteEmployee(<?php echo $employee['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>

<script>
function deleteEmployee(id) {
    if (confirm('Bu çalışanı silmek istediğinizden emin misiniz?')) {
        location.href = 'delete_employee.php?id=' + id;
    }
}
</script>
