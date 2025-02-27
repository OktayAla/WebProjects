<?php
session_start();

// Veritabanı bağlantısı
$conn = mysqli_connect(hostname: 'localhost', username: 'oktayala', password: '123', database: 'database');

// Bağlantı hatasını kontrol et
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// Karakter setini UTF-8 olarak ayarla
mysqli_set_charset($conn, "utf8");

// Formdan gelen verileri al
$personelBirimi = $_POST['personelBirimi'];
$talepTuru = $_POST['talepTuru'];
$detay = $_POST['detay'];
$tarihSaat = $_POST['tarihSaat'];

// Karakter sınırı kontrolü (sunucu tarafında da kontrol!)
if (strlen($detay) > 800) {
    echo "error: Talep detayı 800 karakteri aşamaz!";
    exit; // İşlemi durdur
}

// Kullanıcı ID'sini belirle
$kullanici_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : $_SESSION['user_id'];

// Kullanıcının adını al
$sql_kullanici = "SELECT kullanici_adi FROM kullanicilar WHERE id = ?";
$stmt_kullanici = mysqli_prepare($conn, $sql_kullanici);

if ($stmt_kullanici === false) {
    echo "SQL hazırlama hatası (kullanıcı): " . mysqli_error($conn);
    die();
}

mysqli_stmt_bind_param($stmt_kullanici, "i", $kullanici_id);

if (mysqli_stmt_execute($stmt_kullanici)) {
    mysqli_stmt_store_result($stmt_kullanici);

    if (mysqli_stmt_num_rows($stmt_kullanici) > 0) {
        mysqli_stmt_bind_result($stmt_kullanici, $personelAdi);
        mysqli_stmt_fetch($stmt_kullanici);
    } else {
        $personelAdi = "Bilinmiyor"; // Kullanıcı bulunamazsa
        echo "Kullanıcı bulunamadı!<br>";
    }
} else {
    echo "Sorgu yürütme hatası (kullanıcı): " . mysqli_error($conn);
    die();
}

mysqli_stmt_close($stmt_kullanici);

// SQL injection'ı önlemek için prepared statement kullan
$sql = "INSERT INTO bildirimler (kullanici_id, personel_birimi, talep_turu, detay, tarih_saat, durum, personel_adi) VALUES (?, ?, ?, ?, ?, 'Beklemede', ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    echo "SQL hazırlama hatası (bildirim): " . mysqli_error($conn);
    die();
}

// Parametreleri bağla
mysqli_stmt_bind_param($stmt, "isssss", $kullanici_id, $personelBirimi, $talepTuru, $detay, $tarihSaat, $personelAdi);

// Sorguyu çalıştır
if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_stmt_error($stmt); // Prepared statement hatasını yazdır
}

// Statement'ı kapat
mysqli_stmt_close($stmt);

// Bağlantıyı kapat
mysqli_close($conn);

// Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  //

?>