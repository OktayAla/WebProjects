<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();

try {
    $pdo->exec("ALTER TABLE urunler ADD COLUMN IF NOT EXISTS fiyat DECIMAL(10,2) DEFAULT 0.00");
} catch (Exception $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_price') {
        $id = (int)$_POST['id'];
        $fiyat = (float)str_replace([',', ' '], ['.', ''], $_POST['fiyat']);

        try {
            $stmt = $pdo->prepare('UPDATE urunler SET fiyat = ? WHERE id = ?');
            $stmt->execute([$fiyat, $id]);
            $success = 'Ürün fiyatı başarıyla güncellendi.';
        } catch (Exception $e) {
            $error = 'Fiyat güncellenemedi: ' . $e->getMessage();
        }
    }
}

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
            <h2 class="text-xl font-semibold text-gray-900">Ürün Fiyatları</h2>
            <p class="text-sm text-gray-600 mt-1">Ürünlerin fiyatlarını yönetin</p>
        </div>
    </div>
    
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
            <a href="fiyatlar.php" class="btn btn-outline">
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
                            <th>Mevcut Fiyat</th>
                            <th class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-8 text-gray-500">
                                <i class="bi bi-box text-4xl mb-2 block"></i>
                                <p>Henüz ürün eklenmemiş</p>
                                <a href="urunler.php" class="btn btn-outline btn-sm mt-2">
                                    Ürün Ekle
                                </a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($product['isim']); ?></td>
                                <td>
                                    <span class="text-lg font-semibold text-primary-600">
                                        <?php echo number_format($product['fiyat'] ?? 0, 2, ',', '.'); ?> ₺
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button type="button" onclick="showPriceModal(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['isim']); ?>', <?php echo $product['fiyat'] ?? 0; ?>)" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square mr-1"></i> Fiyat Güncelle
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

<div id="priceModal" class="modal hidden">
    <div class="modal-overlay" onclick="hidePriceModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Fiyat Güncelle</h3>
            <button type="button" onclick="hidePriceModal()" class="modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="priceForm" method="post" class="modal-body">
            <input type="hidden" name="action" value="update_price">
            <input type="hidden" name="id" id="priceProductId">
            
            <div class="form-group">
                <label class="form-label">Ürün Adı</label>
                <input type="text" id="priceProductName" class="form-input" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label">Yeni Fiyat (₺) *</label>
                <input type="number" name="fiyat" id="priceValue" class="form-input" step="0.01" min="0" required>
            </div>
                        
            <div class="modal-footer">
                <button type="button" onclick="hidePriceModal()" class="btn btn-outline">İptal</button>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>
    </div>
</div>

<script>
function showPriceModal(id, name, currentPrice) {
    document.getElementById('priceProductId').value = id;
    document.getElementById('priceProductName').value = name;
    document.getElementById('priceValue').value = currentPrice;
    document.getElementById('priceModal').classList.remove('hidden');
}

function hidePriceModal() {
    document.getElementById('priceModal').classList.add('hidden');
    document.getElementById('priceForm').reset();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
