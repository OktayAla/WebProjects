<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

// Sadece admin rolündeki kullanıcılar erişebilir
$current_user = current_user();
if ($current_user['rol'] !== 'admin') {
    header('Location: index.php?error=' . urlencode('Bu sayfaya erişim yetkiniz bulunmamaktadır.'));
    exit;
}

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $isim = trim($_POST['name']);
    $sifre = trim($_POST['password']);
    $rol = trim($_POST['role']);
    
    try {
        if ($id > 0) {
            // Şifre değiştirilmek isteniyorsa
            if (!empty($sifre)) {
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE kullanicilar SET isim = ?, sifre = ?, rol = ? WHERE id = ?');
                $stmt->execute([$isim, $hashed_password, $rol, $id]);
            } else {
                // Şifre değiştirilmek istenmiyorsa
                $stmt = $pdo->prepare('UPDATE kullanicilar SET isim = ?, rol = ? WHERE id = ?');
                $stmt->execute([$isim, $rol, $id]);
            }
            $message = 'Kullanıcı başarıyla güncellendi.';
        } else {
            // Yeni kullanıcı ekleme
            if (empty($sifre)) {
                throw new Exception('Yeni kullanıcı için şifre gereklidir.');
            }
            $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO kullanicilar (isim, sifre, rol, olusturma_zamani) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$isim, $hashed_password, $rol]);
            $message = 'Kullanıcı başarıyla eklendi.';
        }
        
        header('Location: kullanicilar.php?success=' . urlencode($message));
        exit;
    } catch (Exception $e) {
        $error = 'İşlem başarısız: ' . $e->getMessage();
    }
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    // Kendini silmeye çalışıyorsa engelle
    if ($delId === (int)$current_user['id']) {
        header('Location: kullanicilar.php?error=' . urlencode('Kendi hesabınızı silemezsiniz.'));
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('DELETE FROM kullanicilar WHERE id = ?');
        $stmt->execute([$delId]);
        
        header('Location: kullanicilar.php?success=' . urlencode('Kullanıcı başarıyla silindi.'));
        exit;
    } catch (Exception $e) {
        $error = 'Silme işlemi başarısız: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php'; 

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM kullanicilar WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

// Tüm kullanıcıları getir
$users = $pdo->query('SELECT * FROM kullanicilar ORDER BY id DESC')->fetchAll();

?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="bi bi-people-fill mr-2 text-primary-600"></i> Kullanıcı Yönetimi
            </h1>
            <p class="text-sm text-gray-600 mt-1">Sistemde kayıtlı tüm kullanıcılar ve yetkileri</p>
        </div>
        <button id="newUserBtn" class="btn btn-primary flex items-center justify-center shadow-sm hover:shadow-md transition-all">
            <i class="bi bi-person-plus-fill mr-2"></i> Yeni Kullanıcı
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card-hover animate-fadeIn shadow-lg">
        <div class="p-5">
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-primary-500"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-input pl-10" placeholder="Kullanıcı ara (isim/rol)">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İsim</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oluşturma Zamanı</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['isim']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($user['rol'] === 'admin'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">Admin</span>
                                <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Kullanıcı</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['olusturma_zamani'] ?? 'Belirtilmemiş'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="?edit=<?php echo $user['id']; ?>" class="text-primary-600 hover:text-primary-900" title="Düzenle">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <?php if ((int)$user['id'] !== (int)$current_user['id']): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="text-danger-600 hover:text-danger-900" title="Sil" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Ekleme/Düzenleme Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-5 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Yeni Kullanıcı Ekle</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <form id="userForm" method="post" class="p-5">
                <input type="hidden" name="id" id="userId" value="<?php echo $editUser ? $editUser['id'] : ''; ?>">
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">İsim</label>
                    <input type="text" id="name" name="name" class="form-input" value="<?php echo $editUser ? htmlspecialchars($editUser['isim']) : ''; ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Şifre <?php echo $editUser ? '(Değiştirmek istemiyorsanız boş bırakın)' : ''; ?>
                    </label>
                    <input type="password" id="password" name="password" class="form-input" <?php echo $editUser ? '' : 'required'; ?>>
                </div>
                
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select id="role" name="role" class="form-select">
                        <option value="user" <?php echo ($editUser && $editUser['rol'] === 'user') ? 'selected' : ''; ?>>Kullanıcı</option>
                        <option value="admin" <?php echo ($editUser && $editUser['rol'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelBtn" class="btn btn-secondary">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const userTableBody = document.getElementById('userTableBody');
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const userId = document.getElementById('userId');
        const newUserBtn = document.getElementById('newUserBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Arama işlevi
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = userTableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const role = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || role.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Modal açma/kapama işlevleri
        function openModal(isEdit = false) {
            modalTitle.textContent = isEdit ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle';
            userModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeModalFunc() {
            userModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            if (!userId.value) {
                userForm.reset();
            }
        }
        
        newUserBtn.addEventListener('click', () => openModal(false));
        closeModal.addEventListener('click', closeModalFunc);
        cancelBtn.addEventListener('click', closeModalFunc);
        
        // URL'de edit parametresi varsa modalı aç
        <?php if ($editUser): ?>
        openModal(true);
        <?php endif; ?>
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>