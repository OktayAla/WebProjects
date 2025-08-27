<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name']);

        try {
            $stmt = $pdo->prepare('INSERT INTO products (name) VALUES (?)');
            $stmt->execute([$name]);
            $success = 'Ürün başarıyla eklendi.';
        } catch (Exception $e) {
            $error = 'Ürün eklenemedi: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);

        try {
            $stmt = $pdo->prepare('UPDATE products SET name = ? WHERE id = ?');
            $stmt->execute([$name, $id]);
            $success = 'Ürün başarıyla güncellendi.';
        } catch (Exception $e) {
            $error = 'Ürün güncellenemedi: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE product_id = ?');
            $stmt->execute([$id]);
            $usageCount = $stmt->fetchColumn();
            
            if ($usageCount > 0) {
                $error = 'Bu ürün işlemlerde kullanıldığı için silinemez.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
                $stmt->execute([$id]);
                $success = 'Ürün başarıyla silindi.';
            }
        } catch (Exception $e) {
            $error = 'Ürün silinemedi: ' . $e->getMessage();
        }
    }
}

$products = $pdo->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();
?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700 font-medium">Ürünler</span>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Ürünler</h2>
            <p class="text-sm text-gray-600 mt-1">Sistemdeki tüm ürünleri yönetin</p>
        </div>
        <button type="button" onclick="showAddProductModal()" class="btn btn-primary flex items-center">
            <i class="bi bi-plus-lg mr-2"></i> Yeni Ürün
        </button>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card-hover animate-fadeIn">
        <div class="card-header">
            <h3 class="card-title">Ürün Listesi</h3>
        </div>
        <div class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ürün Adı</th>
                            <th class="text-right">İşlemler</th>
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
                                <td><?php echo $product['id']; ?></td>
                                <td class="font-medium"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="text-right">
                                    <button type="button" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="btn btn-outline btn-sm mr-2">
                                        <i class="bi bi-pencil mr-1"></i> Düzenle
                                    </button>
                                    <button type="button" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')" class="btn btn-danger btn-sm">
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
                <input type="text" name="name" id="productName" class="form-input" required>
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
    document.getElementById('productName').value = product.name;
    document.getElementById('productModal').classList.remove('hidden');
}

function hideProductModal() {
    document.getElementById('productModal').classList.add('hidden');
}

function deleteProduct(id, name) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>