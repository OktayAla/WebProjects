<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $isim = trim($_POST['isim']);
        $urun_fiyat = $_POST['urun_fiyat'];

        try {
            $stmt = $pdo->prepare('INSERT INTO urunler (isim) VALUES (?)');
            $stmt->execute([$isim]);
            $urun_id = $pdo->lastInsertId();

            // Fiyatlar tablosuna da ekle
            $stmt = $pdo->prepare('INSERT INTO fiyatlar (urun_id, fiyat) VALUES (?, ?)');
            $stmt->execute([$urun_id, $urun_fiyat]);

            $success = 'Ürün başarıyla eklendi.';
        } catch (Exception $e) {
            $error = 'Ürün eklenemedi: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $isim = trim($_POST['isim']);

        try {
            $stmt = $pdo->prepare('UPDATE urunler SET isim = ? WHERE id = ?');
            $stmt->execute([$isim, $id]);
            $success = 'Ürün başarıyla güncellendi.';
        } catch (Exception $e) {
            $error = 'Ürün güncellenemedi: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM islemler WHERE urun_id = ?');
            $stmt->execute([$id]);
            $usageCount = $stmt->fetchColumn();
            
            if ($usageCount > 0) {
                $error = 'Bu ürün işlemlerde kullanıldığı için silinemez.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM urunler WHERE id = ?');
                $stmt->execute([$id]);
                $success = 'Ürün başarıyla silindi.';
            }
        } catch (Exception $e) {
            $error = 'Ürün silinemedi: ' . $e->getMessage();
        }
    }
}

// Arama parametresi
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Ürünleri getir
if ($search) {
    $stmt = $pdo->prepare('SELECT * FROM urunler WHERE isim LIKE ? ORDER BY isim ASC');
    $stmt->execute(["%$search%"]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query('SELECT * FROM urunler ORDER BY isim ASC')->fetchAll();
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Ürünler</h2>
            <p class="text-sm text-gray-600 mt-1">Sistemdeki tüm ürünleri yönetin</p>
        </div>
        <button type="button" onclick="showAddProductModal()" class="btn btn-primary flex items-center">
            <i class="bi bi-plus-lg mr-2"></i> Yeni Ürün
        </button>
    </div>
    
    <!-- Arama Formu -->
    <div class="mb-6">
        <form action="" method="GET" class="flex gap-2">
            <div class="form-group flex-grow">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="bi bi-search text-gray-400"></i>
                    </span>
                    <input type="text" id="search" name="search" class="form-input pl-10 w-full" placeholder="Ürün adına göre ara..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search mr-1"></i> Ara
            </button>
            <?php if ($search): ?>
            <a href="urunler.php" class="btn btn-outline">
                <i class="bi bi-x-circle mr-1"></i> Temizle
            </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card-hover animate-fadeIn">
        <div class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ürün Adı</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-8 text-gray-500">
                                <i class="bi bi-box text-4xl mb-2 block"></i>
                                <p>Henüz ürün eklenmemiş</p>
                                <button type="button" onclick="showAddProductModal()" class="btn btn-outline btn-sm mt-2">
                                    İlk Ürünü Ekle
                                </button>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($products as $index => $product): ?>
                            <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.1 + ($index * 0.05); ?>s">
                                <td class="font-medium"><?php echo htmlspecialchars($product['isim']); ?></td>
                                <td class="text-right">
                                    <button type="button" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="btn btn-outline btn-sm mr-2">
                                        <i class="bi bi-pencil mr-1"></i> Düzenle
                                    </button>
                                    <button type="button" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['isim']); ?>')" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash mr-1"></i> Sil
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="productModal" class="modal hidden">
    <div class="modal-overlay" onclick="hideProductModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="modal-title">Yeni Ürün Ekle</h3>
            <button type="button" onclick="hideProductModal()" class="modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="productForm" method="post" class="modal-body">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="productId">
            
            <div class="form-group">
                <label class="form-label">Ürün Adı *</label>
                <input type="text" name="isim" id="productName" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Ürün Fiyatı *</label>
                <input type="text" name="urun_fiyat" id="productPrice" class="form-input" required>
            </div>
                        
            <div class="modal-footer">
                <button type="button" onclick="hideProductModal()" class="btn btn-outline">İptal</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal hidden">
    <div class="modal-overlay" onclick="hideDeleteModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title text-red-600">Ürün Sil</h3>
            <button type="button" onclick="hideDeleteModal()" class="modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Bu ürünü silmek istediğinizden emin misiniz?</p>
            <p class="font-medium" id="deleteProductName"></p>
        </div>
        <form method="post" class="modal-footer">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteProductId">
            <button type="button" onclick="hideDeleteModal()" class="btn btn-outline">İptal</button>
            <button type="submit" class="btn btn-danger">Sil</button>
        </form>
    </div>
</div>

<script>
function showAddProductModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Ürün Ekle';
    document.getElementById('formAction').value = 'add';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productModal').classList.remove('hidden');
}

function editProduct(product) {
    document.getElementById('modalTitle').textContent = 'Ürün Düzenle';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.isim;
    document.getElementById('productModal').classList.remove('hidden');
}

function hideProductModal() {
    document.getElementById('productModal').classList.add('hidden');
}

function deleteProduct(id, isim) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = isim;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>