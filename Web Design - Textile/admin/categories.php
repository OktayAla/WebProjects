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

// İşlem kontrolü
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && !empty($_POST['new_category'])) {
        $new_category = $_POST['new_category'];
        // Kategori zaten var mı kontrol et
        if (!isset($products_data[$new_category])) {
            $products_data[$new_category] = []; // Boş array ile yeni kategori oluştur
            file_put_contents(__DIR__ . '/../products.json', json_encode($products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: categories.php?success=1');
            exit;
        }
    }
    elseif ($_POST['action'] === 'update' && isset($_POST['category_name']) && isset($_POST['new_category_name'])) {
        $category_name = $_POST['category_name'];
        $new_category_name = $_POST['new_category_name'];

        if (isset($products_data[$category_name])) {
            $products_data[$new_category_name] = $products_data[$category_name];
            unset($products_data[$category_name]);

            // JSON dosyasına kaydet
            file_put_contents(__DIR__ . '/../products.json', json_encode($products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            header('Location: categories.php?success=1');
            exit;
        }
    }
    elseif ($_POST['action'] === 'delete' && isset($_POST['category_name'])) {
        $category_to_delete = $_POST['category_name'];
        
        // Kategoride ürün var mı kontrol et
        if (empty($products_data[$category_to_delete]) || isset($_POST['confirm_delete'])) {
            unset($products_data[$category_to_delete]);
            file_put_contents(__DIR__ . '/../products.json', json_encode($products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: categories.php?success=1');
            exit;
        } else {
            $delete_warning = true;
            $category_to_delete_name = $category_to_delete;
        }
    }
}

// Kategorileri al
$categories = array_keys($products_data);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh; /* Sidebar'ın tam yükseklikte olmasını sağlar */
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        
        .sidebar .nav-link {
            padding: .5rem 1rem;
            color: #fff;
            transition: all .3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,.1);
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,.1);
        }
    </style>
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
                            <a class="nav-link text-white" href="products.php">
                                <i class="bi bi-box"></i> Ürünler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="categories.php">
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
                    <h1 class="h2">Kategori Yönetimi</h1>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">İşlem başarıyla tamamlandı.</div>
                <?php endif; ?>

                <div class="container">
                    <?php if (isset($delete_warning)): ?>
                        <div class="alert alert-warning">
                            <p><strong>Uyarı:</strong> "<?php echo htmlspecialchars($category_to_delete_name); ?>" kategorisinde ürünler bulunmaktadır. Silmek istediğinizden emin misiniz?</p>
                            <form method="post" action="" class="mt-3">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_name" value="<?php echo htmlspecialchars($category_to_delete_name); ?>">
                                <input type="hidden" name="confirm_delete" value="1">
                                <button type="submit" class="btn btn-danger">Evet, Kategoriyi ve İçindeki Tüm Ürünleri Sil</button>
                                <a href="categories.php" class="btn btn-secondary">İptal</a>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Yeni Kategori Ekleme Formu -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="h5 mb-0">Yeni Kategori Ekle</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="add">
                                <div class="mb-3">
                                    <label for="new_category" class="form-label">Kategori Adı</label>
                                    <input type="text" class="form-control" id="new_category" name="new_category" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Kategori Ekle</button>
                            </form>
                        </div>
                    </div>

                    <!-- Kategori Güncelleme Formu -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="h5 mb-0">Kategori Güncelle</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="update">
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Mevcut Kategori Adı</label>
                                    <select class="form-control" id="category_name" name="category_name" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category); ?>">
                                                <?php echo htmlspecialchars($category); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="new_category_name" class="form-label">Yeni Kategori Adı</label>
                                    <input type="text" class="form-control" id="new_category_name" name="new_category_name" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Güncelle</button>
                            </form>
                        </div>
                    </div>

                    <!-- Kategori Silme Formu -->
                    <div class="card mt-4">
                        <div class="card-header bg-danger text-white">
                            <h3 class="h5 mb-0">Kategori Sil</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="" onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?');">
                                <input type="hidden" name="action" value="delete">
                                <div class="mb-3">
                                    <label for="delete_category" class="form-label">Silinecek Kategori</label>
                                    <select class="form-control" id="delete_category" name="category_name" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category); ?>">
                                                <?php echo htmlspecialchars($category); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger">Kategoriyi Sil</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
