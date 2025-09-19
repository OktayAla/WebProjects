<?php
// Manuel ürün adlarını yönetmek için yardımcı fonksiyonlar

/**
 * Manuel ürün adını JSON dosyasına kaydet
 */
function saveManualProduct($transactionId, $productName) {
    $jsonFile = __DIR__ . '/../data/manual_products.json';
    
    // Dosya yoksa oluştur
    if (!file_exists($jsonFile)) {
        $dir = dirname($jsonFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($jsonFile, '{}');
    }
    
    // Mevcut verileri oku
    $data = json_decode(file_get_contents($jsonFile), true);
    if (!$data) {
        $data = [];
    }
    
    // Yeni ürün adını ekle
    $data[$transactionId] = trim($productName);
    
    // JSON dosyasına kaydet
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Manuel ürün adını JSON dosyasından al
 */
function getManualProduct($transactionId) {
    $jsonFile = __DIR__ . '/../data/manual_products.json';
    
    if (!file_exists($jsonFile)) {
        return null;
    }
    
    $data = json_decode(file_get_contents($jsonFile), true);
    
    return isset($data[$transactionId]) ? $data[$transactionId] : null;
}

/**
 * Tüm manuel ürünleri al
 */
function getAllManualProducts() {
    $jsonFile = __DIR__ . '/../data/manual_products.json';
    
    if (!file_exists($jsonFile)) {
        return [];
    }
    
    $data = json_decode(file_get_contents($jsonFile), true);
    
    return $data ? $data : [];
}

/**
 * Manuel ürün adını sil
 */
function deleteManualProduct($transactionId) {
    $jsonFile = __DIR__ . '/../data/manual_products.json';
    
    if (!file_exists($jsonFile)) {
        return;
    }
    
    $data = json_decode(file_get_contents($jsonFile), true);
    if ($data && isset($data[$transactionId])) {
        unset($data[$transactionId]);
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
