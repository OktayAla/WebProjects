<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Eğer istek methodu POST ise
    // Form verilerini al
    $name = strip_tags(trim($_POST["name"])); // Adı soyadı
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL); // E-posta
    $message = strip_tags(trim($_POST["message"])); // Mesaj

    // Karakter sınırlamalarını kontrol et
    // Eğer ad, e-posta 25 karakterden uzunsa, 400 yanıt kodu döndür
    // Eğer ad, e-posta veya mesaj 300 karakterden uzunsa, 400 yanıt kodu döndür
    // ve çıkış yap
    if (strlen($name) > 25 || strlen($email) > 25 || strlen($message) > 300) {
        http_response_code(400);
        exit;
    }

    // Dosyaya kaydetme işlemi
    $log_file = 'messages.txt';
    $timestamp = date('d/m/Y H:i');
    $log_entry = "Tarih: $timestamp\n";
    $log_entry .= "Ad Soyad: $name\n";
    $log_entry .= "E-posta: $email\n";
    $log_entry .= "Mesaj: $message\n\n";

    // Eğer dosya yazma işlemi başarılıysa, 200 yanıt kodu döndür
    // ve "success" mesajı gönder
    // Eğer dosya yazma işlemi başarısızsa, 500 yanıt kodu döndür
    if (file_put_contents($log_file, $log_entry, FILE_APPEND)) {
        http_response_code(200);
        echo "success";

        // Eğer dosya yazma işlemi basarısızsa, 500 yanıt kodu döndür
    } else {
        http_response_code(500);
        echo "error";
    }

    // Eğer dosya yazma işlemi basarısızsa, 403 yanıt kodu döndür
} else {
    http_response_code(403);
    echo "error";
}
?>
