<?php
// Hata raporlamayı açalım
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader'ı include et
require_once __DIR__ . '/vendor/phpoffice/phpspreadsheet/autoload.php';

// PhpSpreadsheet sınıflarını kullan
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Veritabanı bağlantısı
$conn = new mysqli('localhost', 'kodkampu_test', '1624@Eriskia,,!*', 'kodkampu_talepbildirimsistemi');
mysqli_set_charset($conn, "utf8");

// İstatistiksel verileri çek
$stats = [
    'status' => mysqli_query($conn, "SELECT durum, COUNT(*) as count FROM bildirimler GROUP BY durum"),
    'departments' => mysqli_query($conn, "SELECT personel_birimi, COUNT(*) as count FROM bildirimler GROUP BY personel_birimi"),
    'types' => mysqli_query($conn, "SELECT talep_turu, COUNT(*) as count FROM bildirimler GROUP BY talep_turu"),
    'reporters' => mysqli_query($conn, "SELECT personel_adi, COUNT(*) as count FROM bildirimler GROUP BY personel_adi ORDER BY count DESC")
];

// Yeni bir Excel dosyası oluştur
$spreadsheet = new Spreadsheet();

// Başlık stili oluştur
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];

// Veri stili oluştur
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
    ],
];

// Durum Dağılımı Sayfası
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Durum Dağılımı');
$sheet->setCellValue('A1', 'Durum');
$sheet->setCellValue('B1', 'Sayı');
$sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

$row = 2;
while ($data = mysqli_fetch_assoc($stats['status'])) {
    $sheet->setCellValue('A' . $row, $data['durum']);
    $sheet->setCellValue('B' . $row, $data['count']);
    $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
    $row++;
}

// Mahalleler Sayfası
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Mahalleler');
$sheet->setCellValue('A1', 'Mahalle');
$sheet->setCellValue('B1', 'Talep Sayısı');
$sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

$row = 2;
while ($data = mysqli_fetch_assoc($stats['departments'])) {
    $sheet->setCellValue('A' . $row, $data['personel_birimi']);
    $sheet->setCellValue('B' . $row, $data['count']);
    $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
    $row++;
}

// Talep Türleri Sayfası
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(2);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Talep Türleri');
$sheet->setCellValue('A1', 'Talep Türü');
$sheet->setCellValue('B1', 'Sayı');
$sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

$row = 2;
while ($data = mysqli_fetch_assoc($stats['types'])) {
    $sheet->setCellValue('A' . $row, $data['talep_turu']);
    $sheet->setCellValue('B' . $row, $data['count']);
    $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
    $row++;
}

// Muhtar Raporu Sayfası
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(3);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Muhtar Raporu');
$sheet->setCellValue('A1', 'Muhtar Adı');
$sheet->setCellValue('B1', 'Talep Sayısı');
$sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

$row = 2;
while ($data = mysqli_fetch_assoc($stats['reporters'])) {
    $sheet->setCellValue('A' . $row, $data['personel_adi']);
    $sheet->setCellValue('B' . $row, $data['count']);
    $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
    $row++;
}

// Tüm sayfalardaki sütun genişliklerini otomatik ayarla
foreach ($spreadsheet->getAllSheets() as $sheet) {
    foreach (range('A', 'B') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
}

// İlk sayfayı aktif yap
$spreadsheet->setActiveSheetIndex(0);

// Excel dosyasını oluştur
$writer = new Xlsx($spreadsheet);

// Dosya adını oluştur
$filename = 'Talep_Raporu_' . date('Y-m-d_H-i-s') . '.xlsx';

// Header'ları ayarla
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Dosyayı indir
$writer->save('php://output');

// Veritabanı bağlantısını kapat
mysqli_close($conn);
exit; 

// Design By. OA Grafik Tasarım | OKTAY ALA | Her Hakkı Saklıdır. © Copyright  //
