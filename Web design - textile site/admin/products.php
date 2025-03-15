<?php
session_start();

// Oturum kontrolü
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// JSON dosyasını oku
$products_json = file_get_contents(__DIR__ . '/../products.json');
$products_data = json_decode($products_json, true);

// Tüm ürünleri birleştir
$all_products = [];
foreach ($products_data as $category => $products) {
    foreach ($products as $product) {
        $product['category'] = $category;
        $all_products[] = $product;
    }
}

// Ürün silme işlemi
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    
    // Her kategorideki ürünleri kontrol et ve sil
    foreach ($products_data as $category => &$products) {
        foreach ($products as $key => $product) {
            if ($product['id'] === $id_to_delete) {
                unset($products[$key]);
                // Diziyi yeniden indexle
                $products = array_values($products);
                
                // JSON dosyasına kaydet
                file_put_contents(__DIR__ . '/../products.json', json_encode($products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                header('Location: products.php?success=1');
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Yönetimi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Elegance Textile</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light btn-sm">Çıkış Yap</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php">
                                <i class="bi bi-house"></i> Ana Sayfa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="products.php">
                                <i class="bi bi-box"></i> Ürünler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="categories.php">
                                <i class="bi bi-tags"></i> Kategoriler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Çıkış
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Ana İçerik -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Ürün Yönetimi</h1>
                    <a href="product-add.php" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Yeni Ürün Ekle
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">İşlem başarıyla tamamlandı.</div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Resim</th>
                                <th>Ürün Adı</th>
                                <th>Kategori</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($product['category'])); ?></td>
                                    <td>
                                        <a href="product-edit.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
