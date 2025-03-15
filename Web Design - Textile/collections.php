<?php require_once 'includes/header.php'; ?>

<?php
// products.json dosyasından gelen verileri oku
$products_json = file_get_contents(__DIR__ . '/products.json');
$products_data = json_decode($products_json, true);
?>

<!-- Sayfa başlığı ve arka plan resmi -->
<div class="page-header bg-texture py-5 mb-5" 
     style="background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
            url('img/collections/collections_header.webp');">
    <div class="container">
        <h1 class="display-4 text-white text-center mb-0">Koleksiyonlarımız</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <?php foreach ($products_data as $category => $products): ?>
            <div class="col-12 mb-5">

                <!-- Kategori başlığını göster -->
                <h2 class="section-title text-center"><?php echo ucfirst($category); ?></h2>
                
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="collection-item">
                                <div class="image-hover-effect">
                                    <!-- Ürün resmini göster -->
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <div class="collection-info p-3">
                                    <!-- Ürün adını göster -->
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    
                                    <!-- Ürün açıklamasını göster -->
                                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                                                        
                                    <!-- Ürün detaylarına gitmek için bağlantı -->
                                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100">Detaylar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>