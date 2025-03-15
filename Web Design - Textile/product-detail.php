<?php require_once 'includes/header.php'; ?>

<?php
// Ürün ID'sini al
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// JSON dosyasından ürünleri oku
$products_json = file_get_contents(__DIR__ . '/products.json');
$products_data = json_decode($products_json, true);

// Ürünü bul
$product = null;
foreach ($products_data as $category => $products) {
    foreach ($products as $p) {
        if ($p['id'] == $product_id) {
            $product = $p;
            break 2;
        }
    }
}

// Eğer ürün bulunamazsa hata mesajı göster
if (!$product) {
    echo '<div class="container py-5"><div class="alert alert-warning">Ürün bulunamadı.</div></div>';
    require_once 'includes/footer.php';
    exit;
}
?>

<!-- Sayfa başlığı ve arka plan resmi -->
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h1 class="display-4"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($product['description']); ?></p>
            <h3 class="h5 mt-4">Özellikler</h3>
            <ul>
                <?php foreach ($product['features'] as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo is_array($value) ? htmlspecialchars(implode(", ", $value)) : htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
