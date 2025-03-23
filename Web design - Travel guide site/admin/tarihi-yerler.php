<?php
require_once 'inc/auth.php';
require_once '../includes/config.php';

checkAuth();

// Create data directory if it doesn't exist
$dataPath = realpath(DATA_PATH) . DIRECTORY_SEPARATOR . 'tarihi-yerler';
if (!is_dir($dataPath)) {
    if (!@mkdir($dataPath, 0777, true)) {
        die('Dizin oluşturulamadı: ' . $dataPath);
    }
}

// Define JSON file path
$jsonFile = $dataPath . DIRECTORY_SEPARATOR . 'items.json';

// Initialize variables
$message = '';
$messageType = '';
$editItem = null;

try {
    // Load existing data
    $items = [];
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        if ($jsonContent === false) {
            throw new Exception('JSON dosyası okunamadı');
        }
        $items = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON parse hatası: ' . json_last_error_msg());
        }
    }

    // Get content from pages directory
    $pageFiles = glob('../pages/tarihi-yerler/*.php');
    $pagesContent = [];

    foreach ($pageFiles as $pageFile) {
        $filename = basename($pageFile);
        $name = str_replace('-detay.php', '', $filename);
        $title = ucwords(str_replace('-', ' ', $name));
        
        // Extract content from file to get more details
        $content = file_get_contents($pageFile);
        
        // Extract location if available
        $location = '';
        if (preg_match('/<span>([^<]+)<\/span>/i', $content, $locationMatch)) {
            $location = trim($locationMatch[1]);
        }
        
        // Extract description if available
        $description = '';
        if (preg_match('/<p>([^<]+)<\/p>/i', $content, $descMatch)) {
            $description = trim($descMatch[1]);
        }
        
        // Extract image if available
        $imageUrl = '';
        if (preg_match('/background-image: url\((.*?)\)/i', $content, $imgMatch)) {
            $imageUrl = trim($imgMatch[1], "'\"");
        }
        
        // Extract year if available
        $year = '';
        if (preg_match('/Yapım Yılı:?\s*(\d+)/i', $content, $yearMatch)) {
            $year = trim($yearMatch[1]);
        }
        
        $pagesContent[] = [
            'id' => count($pagesContent) + 1,
            'title' => $title,
            'location' => $location,
            'description' => $description,
            'image_url' => $imageUrl,
            'detail_page' => $filename,
            'region' => 'Türkiye',
            'year' => $year
        ];
    }

    // Merge with existing items or use pages content if no items exist
    if (empty($items)) {
        $items = $pagesContent;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!isset($_POST['csrf_token'])) {
            throw new Exception('CSRF token eksik');
        }
        validateCSRFToken($_POST['csrf_token']);

        // Handle form submissions
        if (isset($_POST['save_item'])) {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $isNew = ($id === 0);
            
            // Prepare item data
            $item = array_map('strip_tags', [
                'title' => trim($_POST['title']),
                'location' => trim($_POST['location']),
                'region' => trim($_POST['region']),
                'year' => trim($_POST['year']),
                'description' => trim($_POST['description']),
                'image_url' => filter_var(trim($_POST['image_url']), FILTER_SANITIZE_URL),
                'detail_page' => trim($_POST['detail_page'])
            ]);
            
            // Validate required fields
            if (empty($item['title']) || empty($item['location']) || empty($item['region'])) {
                $message = 'Başlık, konum ve bölge alanları zorunludur.';
                $messageType = 'error';
            } else {
                // Add new item or update existing one
                if ($isNew) {
                    // Generate new ID
                    $maxId = 0;
                    foreach ($items as $existingItem) {
                        if (isset($existingItem['id']) && $existingItem['id'] > $maxId) {
                            $maxId = $existingItem['id'];
                        }
                    }
                    $item['id'] = $maxId + 1;
                    $items[] = $item;
                    $message = 'Yeni tarihi yer başarıyla eklendi.';
                } else {
                    // Update existing item
                    foreach ($items as $key => $existingItem) {
                        if (isset($existingItem['id']) && $existingItem['id'] === $id) {
                            $item['id'] = $id;
                            $items[$key] = $item;
                            break;
                        }
                    }
                    $message = 'Tarihi yer başarıyla güncellendi.';
                }
                $messageType = 'success';
                
                // Save to JSON file
                if (file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                    throw new Exception('JSON dosyası yazılamadı');
                }
            }
        }
        
        // Delete item
        if (isset($_POST['delete_item'])) {
            $id = intval($_POST['delete_id']);
            
            foreach ($items as $key => $item) {
                if (isset($item['id']) && $item['id'] === $id) {
                    unset($items[$key]);
                    $message = 'Tarihi yer başarıyla silindi.';
                    $messageType = 'success';
                    
                    // Re-index array and save to JSON file
                    $items = array_values($items);
                    if (file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                        throw new Exception('JSON dosyası yazılamadı');
                    }
                    break;
                }
            }
        }
    }

} catch (Exception $e) {
    $message = 'Hata: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    foreach ($items as $item) {
        if (isset($item['id']) && $item['id'] === $id) {
            $editItem = $item;
            break;
        }
    }
}

// Get regions for filter
$regions = [];
foreach ($items as $item) {
    if (isset($item['region']) && !in_array($item['region'], $regions)) {
        $regions[] = $item['region'];
    }
}
sort($regions);

// Handle filter
$filteredItems = $items;
if (isset($_GET['filter_region']) && !empty($_GET['filter_region'])) {
    $filterRegion = $_GET['filter_region'];
    $filteredItems = array_filter($items, function($item) use ($filterRegion) {
        return isset($item['region']) && $item['region'] === $filterRegion;
    });
}

// Handle search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = strtolower($_GET['search']);
    $filteredItems = array_filter($filteredItems, function($item) use ($search) {
        return strpos(strtolower($item['title']), $search) !== false ||
               strpos(strtolower($item['location']), $search) !== false ||
               strpos(strtolower($item['description'] ?? ''), $search) !== false;
    });
}

// Generate CSRF token for forms
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarihi Yerler Yönetimi - <?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">

</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-paper-plane logo"></i>
                <h1>Türkiye Gezi Rehberi</h1>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-category">Ana Menü</div>
                <a href="\turkiyegezirehberi\index.php" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Anasayfa</span>
                </a>
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <div class="menu-category">İçerik Yönetimi</div>
                <a href="tarihi-yerler.php" class="menu-item active">
                    <i class="fas fa-landmark"></i>
                    <span>Tarihi Yerler</span>
                </a>
                <a href="dogal-guzellikler.php" class="menu-item">
                    <i class="fas fa-mountain"></i>
                    <span>Doğal Güzellikler</span>
                </a>
                <a href="lezzet-duraklari.php" class="menu-item">
                    <i class="fas fa-utensils"></i>
                    <span>Lezzet Durakları</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <h2 class="page-title">Tarihi Yerler Yönetimi</h2>
                
                <a href="?action=add" class="btn-add">
                    <i class="fas fa-plus"></i> Yeni Ekle
                </a>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                <!-- Add/Edit Form -->
                <div class="form-container">
                    <h3 class="form-title">
                        <?= isset($_GET['id']) ? 'Tarihi Yer Düzenle' : 'Yeni Tarihi Yer Ekle' ?>
                    </h3>
                    
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <?php if ($editItem): ?>
                            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="title">Başlık *</label>
                                <input type="text" id="title" name="title" required 
                                       value="<?= htmlspecialchars($editItem['title'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="location">Konum *</label>
                                <input type="text" id="location" name="location" required 
                                       value="<?= htmlspecialchars($editItem['location'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="region">Bölge *</label>
                                <select id="region" name="region" required>
                                    <option value="">Bölge Seçin</option>
                                    <option value="Marmara" <?= (isset($editItem['region']) && $editItem['region'] === 'Marmara') ? 'selected' : '' ?>>Marmara</option>
                                    <option value="Ege" <?= (isset($editItem['region']) && $editItem['region'] === 'Ege') ? 'selected' : '' ?>>Ege</option>
                                    <option value="Akdeniz" <?= (isset($editItem['region']) && $editItem['region'] === 'Akdeniz') ? 'selected' : '' ?>>Akdeniz</option>
                                    <option value="Karadeniz" <?= (isset($editItem['region']) && $editItem['region'] === 'Karadeniz') ? 'selected' : '' ?>>Karadeniz</option>
                                    <option value="İç Anadolu" <?= (isset($editItem['region']) && $editItem['region'] === 'İç Anadolu') ? 'selected' : '' ?>>İç Anadolu</option>
                                    <option value="Doğu Anadolu" <?= (isset($editItem['region']) && $editItem['region'] === 'Doğu Anadolu') ? 'selected' : '' ?>>Doğu Anadolu</option>
                                    <option value="Güneydoğu Anadolu" <?= (isset($editItem['region']) && $editItem['region'] === 'Güneydoğu Anadolu') ? 'selected' : '' ?>>Güneydoğu Anadolu</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="year">Yapım Yılı</label>
                                <input type="text" id="year" name="year" 
                                       value="<?= htmlspecialchars($editItem['year'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea id="description" name="description"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url">Görsel URL</label>
                            <input type="text" id="image_url" name="image_url" 
                                   value="<?= htmlspecialchars($editItem['image_url'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="detail_page">Detay Sayfası</label>
                            <input type="text" id="detail_page" name="detail_page" 
                                   value="<?= htmlspecialchars($editItem['detail_page'] ?? '') ?>">
                        </div>
                        
                        <div class="form-buttons">
                            <a href="tarihi-yerler.php" class="btn-cancel">İptal</a>
                            <button type="submit" name="save_item" class="btn-save">Kaydet</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Filter and Search -->
                <div class="filter-bar">
                    <div class="filter-section">
                        <form method="get" action="">
                            <select name="filter_region" onchange="this.form.submit()">
                                <option value="">Tüm Bölgeler</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= htmlspecialchars($region) ?>" <?= (isset($_GET['filter_region']) && $_GET['filter_region'] === $region) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($region) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    
                    <div class="search-box">
                        <form method="get" action="">
                            <?php if (isset($_GET['filter_region'])): ?>
                                <input type="hidden" name="filter_region" value="<?= htmlspecialchars($_GET['filter_region']) ?>">
                            <?php endif; ?>
                            <input type="text" name="search" placeholder="Ara..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn-search">Ara</button>
                            <a href="tarihi-yerler.php" class="btn-reset">Sıfırla</a>
                        </form>
                    </div>
                </div>
                
                <!-- Items Table -->
                <div class="items-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Görsel</th>
                                <th>Başlık</th>
                                <th>Konum</th>
                                <th>Bölge</th>
                                <th>Yapım Yılı</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredItems)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Kayıt bulunamadı.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filteredItems as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="item-image">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px; background-color: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                    <i class="fas fa-image" style="color: #aaa;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($item['title']) ?></td>
                                        <td><?= htmlspecialchars($item['location']) ?></td>
                                        <td><?= htmlspecialchars($item['region']) ?></td>
                                        <td><?= htmlspecialchars($item['year'] ?? '-') ?></td>
                                        <td>
                                            <div class="item-actions">
                                                <a href="?action=edit&id=<?= $item['id'] ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                                <form method="post" action="" onsubmit="return confirm('Bu öğeyi silmek istediğinizden emin misiniz?');" style="display: inline;">
                                                    <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" name="delete_item" class="btn-delete">
                                                        <i class="fas fa-trash"></i> Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>