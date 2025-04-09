<?php
// Bu dosya, admin panelinde içerik güncellemeleri için kullanılır
// session_start() ile oturum başlatılır
// ve admin giriş kontrolü yapılır.
session_start();

// Eğer kullanıcı giriş yapmamışsa, 403 hatası döndür
// ve erişimi engelle
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Yetkisiz erişim');
}

// Site içeriğini JSON dosyasından oku
$content_file = '../content/site-content.json';
$site_content = json_decode(file_get_contents($content_file), true);

// Eğer POST isteği yapılmışsa, içerik güncelleme işlemlerini gerçekleştir
// JSON dosyasını güncelle ve başarılıysa 200 döndür, hata varsa 500 döndür
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Eğer "about_text" POST isteği ile gönderilmişse, içeriği güncelle
    // Aksi takdirde, "type" ve "data" POST isteği ile gönderilmişse, içeriği güncelle
    if (isset($_POST['about_text'])) {
        $site_content['about']['text'] = $_POST['about_text'];
    } 
    else if (isset($input['type'])) {
        // Eğer "type" ve "data" ile gönderilmişse, içeriği güncelle
        switch ($input['type']) {
            case 'projects':
                $site_content['projects'] = $input['data'];
                break;
            case 'skills':
                $site_content['skills'] = $input['data'];
                break;
        }
    }

    // Eğer içerik güncelleme işlemi başarılıysa, JSON dosyasını güncelle ve 200 döndür
    // Hata varsa 500 döndür
    if (file_put_contents($content_file, json_encode($site_content, JSON_PRETTY_PRINT))) {
        http_response_code(200);
        echo 'success';
    } else {
        http_response_code(500);
        echo 'error';
    }
}
