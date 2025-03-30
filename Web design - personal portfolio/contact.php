<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = strip_tags(trim($_POST["message"]));

    // E-posta başlığı
    $subject = "Yeni İletişim Formu Mesajı";
    
    
    // E-posta içeriği
    $email_content = "İsim: $name\n";
    $email_content .= "E-posta: $email\n\n";
    $email_content .= "Mesaj:\n$message\n";

    // E-posta başlıkları
    $email_headers = "From: $name <$email>";

    // E-postayı gönder
    // Not: Gerçek uygulamada kendi e-posta adresinizi kullanın
    $recipient = "info@oktayala.com";
    
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "Teşekkürler! Mesajınız gönderildi.";
    } else {
        http_response_code(500);
        echo "Üzgünüz, mesajınız gönderilemedi.";
    }
} else {
    http_response_code(403);
    echo "Form gönderiminde bir hata oluştu.";
}
?>
