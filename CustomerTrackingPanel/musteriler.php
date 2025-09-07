<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $isim = trim($_POST['name']);
    $numara = trim($_POST['phone']);
    $adres = trim($_POST['address']);
    
    try {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE musteriler SET isim = ?, numara = ?, adres = ? WHERE id = ?');
            $stmt->execute([$isim, $numara, $adres, $id]);
            $message = 'Müşteri başarıyla güncellendi.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO musteriler (isim, numara, adres) VALUES (?, ?, ?)');
            $stmt->execute([$isim, $numara, $adres]);
            $message = 'Müşteri başarıyla eklendi.';
        }
        
        header('Location: musteriler.php?success=' . urlencode($message));
        exit;
    } catch (Exception $e) {
        $error = 'İşlem başarısız: ' . $e->getMessage();
    }
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    $pdo->beginTransaction();
    try {
        // Önce ilişkili işlemleri sil
        $pdo->prepare('DELETE FROM islemler WHERE musteri_id = ?')->execute([$delId]);
        
        // Sonra müşteriyi sil
        $pdo->prepare('DELETE FROM musteriler WHERE id = ?')->execute([$delId]);
        
        $pdo->commit();
        header('Location: musteriler.php?success=' . urlencode('Müşteri ve ilişkili tüm işlemler başarıyla silindi.'));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Silme işlemi başarısız: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php'; 

$editCustomer = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM musteriler WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editCustomer = $stmt->fetch();
}

// Tüm müşterileri getir (sıralama ID'ye göre)
$customers = $pdo->query('SELECT m.*, 
    (SELECT COALESCE(SUM(CASE WHEN odeme_tipi = "borc" THEN miktar ELSE 0 END), 0) FROM islemler WHERE musteri_id = m.id) as toplam_borc,
    (SELECT COALESCE(SUM(CASE WHEN odeme_tipi = "tahsilat" THEN miktar ELSE 0 END), 0) FROM islemler WHERE musteri_id = m.id) as toplam_tahsilat
    FROM musteriler m ORDER BY m.id DESC')->fetchAll();

// Her müşteri için net bakiye hesapla
foreach ($customers as &$customer) {
    $customer['net_bakiye'] = $customer['toplam_borc'] - $customer['toplam_tahsilat'];
}

?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="bi bi-people mr-2 text-primary-600"></i> Müşteri Yönetimi
            </h1>
            <p class="text-sm text-gray-600 mt-1">Sistemde kayıtlı tüm müşteriler ve bakiye durumları</p>
        </div>
        <button id="newCustomerBtn" class="btn btn-primary flex items-center justify-center shadow-sm hover:shadow-md transition-all">
            <i class="bi bi-person-plus-fill mr-2"></i> Yeni Müşteri
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card-hover animate-fadeIn shadow-lg">
        <div class="p-5">
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-primary-500"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-input pl-10" placeholder="Müşteri ara (ad/telefon)">
                </div>
                <button id="clearSearch" class="btn btn-secondary">
                    <i class="bi bi-x-circle mr-2"></i> Temizle
                </button>
            </div>

            <div class="table-container">
                <table id="customersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <!-- # ID SÜTUNU KALDIRILDI -->
                            <th><i class="bi bi-person-badge mr-1 text-primary-500"></i> Ad Soyad</th>
                            <th><i class="bi bi-telephone mr-1 text-primary-500"></i> Telefon</th>
                            <th><i class="bi bi-geo-alt mr-1 text-primary-500"></i> Adres</th>
                            <th><i class="bi bi-cash-coin mr-1 text-primary-500"></i> Bakiye (₺)</th>
                            <th class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="bg-gray-100 rounded-full p-4 mb-2">
                                        <i class="bi bi-person-circle text-5xl text-primary-500"></i>
                                    </div>
                                    <h4 class="text-lg font-medium">Henüz kayıtlı müşteri yok</h4>
                                    <button type="button" onclick="showModal()" class="btn btn-outline mt-2">
                                        <i class="bi bi-person-plus-fill mr-1"></i> İlk Müşteriyi Ekle
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $index => $row): ?>
                            <tr class="animate-fadeIn hover:bg-gray-50 transition-colors" style="animation-delay: <?php echo $index * 0.05; ?>s">
                                <!-- ID SÜTUNU KALDIRILDI -->
                                <td>
                                    <a href="musteri_rapor.php?customer=<?php echo $row['id']; ?>" class="font-medium text-primary-700 hover:text-primary-900 transition-colors">
                                        <?php echo htmlspecialchars($row['isim']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row['numara']); ?></td>
                                <td class="max-w-xs truncate"><?php echo nl2br(htmlspecialchars($row['adres'])); ?></td>
                                <td class="font-medium <?php echo $row['net_bakiye'] > 0 ? 'text-danger-600' : ($row['net_bakiye'] < 0 ? 'text-success-600' : 'text-gray-600'); ?>">
                                    <i class="bi <?php echo $row['net_bakiye'] > 0 ? 'bi-arrow-up-circle-fill text-danger-500' : ($row['net_bakiye'] < 0 ? 'bi-arrow-down-circle-fill text-success-500' : 'bi-dash-circle text-gray-500'); ?> mr-1"></i>
                                    <?php echo number_format(abs($row['net_bakiye']), 2, ',', '.'); ?> ₺
                                    <?php if ($row['net_bakiye'] < 0): ?><span class="text-xs text-gray-500">(alacaklı)</span><?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="flex space-x-3 justify-end">
                                        <a href="islemler.php?customer=<?php echo $row['id']; ?>" class="btn-icon btn-primary-outline" title="İşlem Ekle">
                                            <i class="bi bi-cash-stack"></i>
                                        </a>
                                        <a href="musteriler.php?edit=<?php echo $row['id']; ?>" class="btn-icon btn-secondary-outline" title="Düzenle">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="musteriler.php?delete=<?php echo $row['id']; ?>" class="btn-icon btn-danger-outline" onclick="return confirm('Bu müşteriyi ve tüm işlemlerini silmek istediğinize emin misiniz?');" title="Sil">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS styles for modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0; top: 0; width: 100vw; height: 100vh;
    overflow: auto;
    background: rgba(17, 24, 39, 0.5);
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}
.modal.show {
    display: flex;
    animation: fadeIn 0.3s ease;
}
.modal-dialog {
    max-width: 500px;
    width: 100%;
    margin: auto;
    transform: translateY(0);
    transition: transform 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<div class="modal" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow-xl">
        <div class="modal-content border-0">
            <form method="post" class="w-full">
                <div class="modal-header border-b border-gray-200">
                    <h5 class="modal-title text-lg font-bold flex items-center" id="customerModalLabel">
                        <i class="bi <?php echo $editCustomer ? 'bi-pencil-square text-primary-600' : 'bi-person-plus-fill text-success-600'; ?> mr-2"></i>
                        <?php echo $editCustomer ? 'Müşteriyi Düzenle' : 'Yeni Müşteri Ekle'; ?>
                    </h5>
                    <button type="button" class="text-gray-400 hover:text-gray-700 transition-colors" onclick="closeModal()" aria-label="Close">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <input type="hidden" name="id" value="<?php echo $editCustomer ? (int)$editCustomer['id'] : 0; ?>">
                    
                    <div class="mb-4">
                        <label for="customerName" class="form-label flex items-center">
                            <i class="bi bi-person mr-2 text-primary-500"></i> Ad Soyad
                        </label>
                        <input type="text" name="name" id="customerName" class="form-input" placeholder="Müşteri adını giriniz" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['isim']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerPhone" class="form-label flex items-center">
                            <i class="bi bi-telephone mr-2 text-primary-500"></i> Telefon
                        </label>
                        <input type="text" name="phone" id="customerPhone" class="form-input" placeholder="Telefon numarası" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['numara']) : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerAddress" class="form-label flex items-center">
                            Adres
                        </label>
                        <textarea name="address" id="customerAddress" rows="3" class="form-input" placeholder="Adres bilgisi"><?php echo $editCustomer ? htmlspecialchars($editCustomer['adres']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-gray-200 p-4 flex justify-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="bi bi-x-circle mr-2"></i> Vazgeç
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi <?php echo $editCustomer ? 'bi-check-circle' : 'bi-plus-circle'; ?> mr-2"></i>
                        <?php echo $editCustomer ? 'Kaydet' : 'Ekle'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('customerModal');
    const newCustomerBtn = document.getElementById('newCustomerBtn');
    
    if (newCustomerBtn) {
        newCustomerBtn.addEventListener('click', function() {
            // Yeni müşteri ekleme modunda formu sıfırla
            document.querySelector('#customerModal form').reset();
            document.querySelector('#customerModal input[name="id"]').value = 0;
            document.getElementById('customerModalLabel').innerHTML = '<i class="bi bi-person-plus-fill text-success-600 mr-2"></i> Yeni Müşteri Ekle';
            showModal();
        });
    }
    
    if (modal) {
        modal.addEventListener('mousedown', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal?.classList.contains('show')) {
            closeModal();
        }
    });
    
    const customerForm = document.querySelector('#customerModal form');
    if (customerForm) {
        customerForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i> İşleniyor...';
            }
        });
    }

    const setupSearch = () => {
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearch');
        const table = document.getElementById('customersTable');
        const key = 'customers_search';
        
        if (!input || !table) return;
        
        // Tarayıcı depolamasından arama kelimesini al
        input.value = localStorage.getItem(key) || '';
        
        function applyFilter() {
            const q = input.value.toLowerCase().trim();
            const rows = table.tBodies[0].rows;
            let visibleCount = 0;
            
            // Tüm satırları döngüye al
            for (const tr of rows) {
                // Sadece Ad Soyad ve Telefon sütunlarını kontrol et (ID'yi kaldırdık)
                const nameText = tr.cells[0]?.innerText.toLowerCase() || ''; // Ad Soyad
                const phoneText = tr.cells[1]?.innerText.toLowerCase() || ''; // Telefon
                
                const isVisible = nameText.includes(q) || phoneText.includes(q);
                tr.style.display = isVisible ? '' : 'none';
                
                if (isVisible) {
                    // Animasyon gecikmesini güncelle
                    tr.classList.add('animate-fadeIn');
                    tr.style.animationDelay = (visibleCount * 0.05) + 's';
                    visibleCount++;
                } else {
                    tr.classList.remove('animate-fadeIn');
                }
            }
            
            // Arama kelimesini kaydet
            localStorage.setItem(key, q);
        }
        
        input.addEventListener('input', applyFilter);
        if (clearBtn) {
            clearBtn.addEventListener('click', function() { 
                input.value = ''; 
                applyFilter(); 
                input.focus();
            });
        }
        
        // Sayfa yüklendiğinde filtreyi uygula
        applyFilter();
    };
    
    // Düzenleme modunda modalı aç
    <?php if ($editCustomer): ?>
        showModal();
    <?php endif; ?>

    setupSearch();
});

function showModal() {
    const modal = document.getElementById('customerModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Modalı ortaya çıkarmak için küçük bir gecikme ekle
        setTimeout(() => {
            const dialog = modal.querySelector('.modal-dialog');
            if (dialog) {
                dialog.style.transform = 'translateY(0)';
                dialog.style.opacity = '1';
            }
        }, 50);
        
        const firstInput = modal.querySelector('input[name="name"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    }
}

function closeModal() {
    const modal = document.getElementById('customerModal');
    if (modal) {
        const dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            dialog.style.transform = 'translateY(-10px)';
            dialog.style.opacity = '0';
        }
        
        setTimeout(() => {
            modal.classList.remove('show');
            document.body.style.overflow = '';
            
            // Eğer URL'de 'edit' parametresi varsa, modal kapatıldığında bunu temizle
            const url = new URL(window.location.href);
            if (url.searchParams.has('edit')) {
                url.searchParams.delete('edit');
                history.replaceState(null, '', url.toString());
            }
        }, 200);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>