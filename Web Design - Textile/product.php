<?php
session_start();

// Eğer yönetici girişi yapılmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// JSON dosyasından ürünleri oku
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
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
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
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Resim</th>
                                <th>Ürün Adı</th>
                                <th>Kategori</th>
                                <th>Fiyat</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Tüm ürünleri listele -->
                            <?php foreach ($all_products as $product): ?>
                                <tr>
                                    <!-- Ürün bilgilerini tabloya ekle -->
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <!-- Ürün resmini ve ismini tabloya ekle -->
                                        <img src="<?php echo $product['image']; ?>" 
                                             alt="<?php echo $product['name']; ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>

                                    <!-- Ürün adını, kategorisini, fiyatını ve işlemler butonlarını tabloya ekle -->
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo ucfirst($product['category']); ?></td>
                                    <td><?php echo $product['price']; ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary me-1">Düzenle</a>
                                        <a href="#" class="btn btn-sm btn-danger">Sil</a>
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