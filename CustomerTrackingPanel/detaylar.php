<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = get_pdo_connection();
$type = $_GET['type'] ?? '';

switch($type) {
    case 'sales':
        // Toplam satış detayları (borç + tahsilat)
        $stmt = $pdo->query("
            SELECT 
                i.id,
                i.miktar,
                i.odeme_tipi,
                i.aciklama,
                i.olusturma_zamani,
                m.isim AS musteri_isim,
                COALESCE(u.isim, 'Diğer') AS urun_isim
            FROM islemler i
            JOIN musteriler m ON m.id = i.musteri_id
            LEFT JOIN urunler u ON u.id = i.urun_id
            WHERE i.odeme_tipi IN ('borc', 'tahsilat')
            ORDER BY i.olusturma_zamani DESC
            LIMIT 50
        ");
        $transactions = $stmt->fetchAll();
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="min-w-full divide-y divide-gray-200">';
        echo '<thead class="bg-gray-50">';
        echo '<tr>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody class="bg-white divide-y divide-gray-200">';
        
        foreach($transactions as $tx) {
            echo '<tr class="hover:bg-gray-50">';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($tx['musteri_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($tx['urun_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . number_format($tx['miktar'], 2, ',', '.') . ' ₺</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap">';
            if ($tx['odeme_tipi'] === 'borc') {
                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Borç</span>';
            } else {
                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tahsilat</span>';
            }
            echo '</td>';
            echo '<td class="px-6 py-4 text-sm text-gray-500">' . htmlspecialchars($tx['aciklama'] ?: '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        break;
        
    case 'collections':
        // Tahsilat detayları
        $stmt = $pdo->query("
            SELECT 
                i.id,
                i.miktar,
                i.aciklama,
                i.olusturma_zamani,
                m.isim AS musteri_isim,
                COALESCE(u.isim, 'Diğer') AS urun_isim
            FROM islemler i
            JOIN musteriler m ON m.id = i.musteri_id
            LEFT JOIN urunler u ON u.id = i.urun_id
            WHERE i.odeme_tipi = 'tahsilat'
            ORDER BY i.olusturma_zamani DESC
            LIMIT 50
        ");
        $transactions = $stmt->fetchAll();
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="min-w-full divide-y divide-gray-200">';
        echo '<thead class="bg-gray-50">';
        echo '<tr>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody class="bg-white divide-y divide-gray-200">';
        
        foreach($transactions as $tx) {
            echo '<tr class="hover:bg-gray-50">';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($tx['musteri_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($tx['urun_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">' . number_format($tx['miktar'], 2, ',', '.') . ' ₺</td>';
            echo '<td class="px-6 py-4 text-sm text-gray-500">' . htmlspecialchars($tx['aciklama'] ?: '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        break;
        
    case 'receivables':
        // Alacak detayları
        $stmt = $pdo->query("
            SELECT 
                i.id,
                i.miktar,
                i.aciklama,
                i.olusturma_zamani,
                m.isim AS musteri_isim,
                m.tutar AS musteri_bakiye,
                COALESCE(u.isim, 'Diğer') AS urun_isim
            FROM islemler i
            JOIN musteriler m ON m.id = i.musteri_id
            LEFT JOIN urunler u ON u.id = i.urun_id
            WHERE i.odeme_tipi = 'borc'
            ORDER BY i.olusturma_zamani DESC
            LIMIT 50
        ");
        $transactions = $stmt->fetchAll();
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="min-w-full divide-y divide-gray-200">';
        echo '<thead class="bg-gray-50">';
        echo '<tr>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bakiye</th>';
        echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody class="bg-white divide-y divide-gray-200">';
        
        foreach($transactions as $tx) {
            echo '<tr class="hover:bg-gray-50">';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($tx['musteri_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($tx['urun_isim']) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])) . '</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">' . number_format($tx['miktar'], 2, ',', '.') . ' ₺</td>';
            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . number_format($tx['musteri_bakiye'], 2, ',', '.') . ' ₺</td>';
            echo '<td class="px-6 py-4 text-sm text-gray-500">' . htmlspecialchars($tx['aciklama'] ?: '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        break;
        
    default:
        echo '<div class="text-center py-8 text-gray-500">Geçersiz veri türü.</div>';
}
?>
