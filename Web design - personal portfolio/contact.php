<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al ve temizle
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = strip_tags(trim($_POST["message"]));

    // Karakter sınırlamalarını kontrol et
    if (strlen($name) > 25 || strlen($email) > 25 || strlen($message) > 300) {
        http_response_code(400);
        exit;
    }

    // Dosyaya kaydetme işlemi
    $log_file = 'messages.txt';
    $timestamp = date('d-m-y H:i:s');
    $log_entry = "Tarih: $timestamp\n";
    $log_entry .= "İsim: $name\n";
    $log_entry .= "E-posta: $email\n";
    $log_entry .= "Mesaj: $message\n";
    $log_entry .= "----------------------------------------\n";

    // Dosyaya yaz
    if (file_put_contents($log_file, $log_entry, FILE_APPEND)) {
        http_response_code(200);
        echo "success";
    } else {
        http_response_code(500);
        echo "error";
    }
} else {
    http_response_code(403);
    echo "error";
}
?>
