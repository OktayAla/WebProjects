<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?');
        $stmt->execute([$name, $phone, $address, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)');
        $stmt->execute([$name, $phone, $address]);
    }
    header('Location: musteriler.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM transactions WHERE customer_id = ?')->execute([$delId]);
    $pdo->prepare('DELETE FROM customers WHERE id = ?')->execute([$delId]);
    header('Location: musteriler.php');
    exit;
}

require_once __DIR__ . '/includes/header.php'; 

$editCustomer = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editCustomer = $stmt->fetch();
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
                            <th class="text-center">#</th>
                            <th><i class="bi bi-person-badge mr-1 text-primary-500"></i> Ad Soyad</th>
                            <th><i class="bi bi-telephone mr-1 text-primary-500"></i> Telefon</th>
                            <th><i class="bi bi-geo-alt mr-1 text-primary-500"></i> Adres</th>
                            <th><i class="bi bi-cash-coin mr-1 text-primary-500"></i> Bakiye (₺)</th>
                            <th class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($pdo->query('SELECT * FROM customers ORDER BY id DESC') as $row):
                        ?>
                        <tr class="animate-fadeIn hover:bg-gray-50 transition-colors">
                            <td class="text-center font-medium text-gray-500"><?php echo $row['id']; ?></td>
                            <td>
                                <a href="musteri_rapor.php?customer=<?php echo $row['id']; ?>" class="font-medium text-primary-700 hover:text-primary-900 transition-colors">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </td>
                            <td><i class="bi bi-telephone-fill text-gray-400 mr-1"></i> <?php echo htmlspecialchars($row['phone']); ?></td>
                            <td class="max-w-xs truncate"><i class="bi bi-geo text-gray-400 mr-1"></i> <?php echo nl2br(htmlspecialchars($row['address'])); ?></td>
                            <td class="font-medium <?php echo $row['balance'] > 0 ? 'text-danger-600' : 'text-success-600'; ?>">
                                <i class="bi <?php echo $row['balance'] > 0 ? 'bi-arrow-up-circle-fill text-danger-500' : 'bi-arrow-down-circle-fill text-success-500'; ?> mr-1"></i>
                                <?php echo number_format($row['balance'], 2, ',', '.'); ?>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
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
                        <input type="text" name="name" id="customerName" class="form-input" placeholder="Müşteri adını giriniz" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerPhone" class="form-label flex items-center">
                            <i class="bi bi-telephone mr-2 text-primary-500"></i> Telefon
                        </label>
                        <input type="text" name="phone" id="customerPhone" class="form-input" placeholder="Telefon numarası" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['phone']) : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerAddress" class="form-label flex items-center">
                            <i class="bi bi-geo-alt mr-2 text-primary-500"></i> Adres
                        </label>
                        <textarea name="address" id="customerAddress" rows="3" class="form-input" placeholder="Adres bilgisi"><?php echo $editCustomer ? htmlspecialchars($editCustomer['address']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-gray-200 p-4">
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
            showModal();
            // Form alanına otomatik odaklanma
            setTimeout(() => {
                document.getElementById('customerName')?.focus();
            }, 300);
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
        
        input.value = localStorage.getItem(key) || '';
        
        function applyFilter() {
            const q = input.value.toLowerCase().trim();
            const rows = table.tBodies[0].rows;
            let visibleCount = 0;
            
            table.classList.add('filtering');
            
            for (const tr of rows) {
                const text = tr.innerText.toLowerCase();
                const isVisible = text.includes(q);
                tr.style.display = isVisible ? '' : 'none';
                
                if (isVisible) {
                    tr.classList.add('animate-fadeIn');
                    tr.style.animationDelay = (visibleCount * 0.05) + 's';
                    visibleCount++;
                } else {
                    tr.classList.remove('animate-fadeIn');
                }
            }
            
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
        
        applyFilter();
    };
    
    const highlightActivePage = () => {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('nav a');
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || 
                (currentPath.includes('musteriler.php') && href.includes('musteriler.php'))) {
                link.classList.add('active');
            }
        });
    };
    
    setupSearch();
    highlightActivePage();

    <?php if ($editCustomer): ?>
        showModal();
    <?php endif; ?>
});

function showModal() {
    const modal = document.getElementById('customerModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        const dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            dialog.style.transform = 'translateY(0)';
            dialog.style.opacity = '1';
        }
        
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
            const form = modal.querySelector('form');
            const idInput = form?.querySelector('input[name="id"]');
            if (form && idInput && !parseInt(idInput.value)) {
                form.reset();
            }
        }, 200);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>