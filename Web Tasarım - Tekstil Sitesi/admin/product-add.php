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

// Kategorileri al
$categories = array_keys($products_data);

// Ürün ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_product = [
        'id' => time(), // Benzersiz ID oluştur
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'image' => $_POST['image'],
        'features' => json_decode($_POST['features'], true)
    ];

    $category = $_POST['category'];
    $products_data[$category][] = $new_product;

    // JSON dosyasına kaydet
    file_put_contents(__DIR__ . '/../products.json', json_encode($products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header('Location: products.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Ürün Ekle - Admin Panel</title>
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
                    <h1 class="h2">Yeni Ürün Ekle</h1>
                </div>

                <form method="post" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ürün Adı</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Resim URL</label>
                        <input type="text" class="form-control" id="image" name="image" required>
                    </div>
                    <div class="mb-3">
                        <label for="features" class="form-label">Özellikler (JSON formatında)</label>
                        <textarea class="form-control" id="features" name="features" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-control" id="category" name="category" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Ekle</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
