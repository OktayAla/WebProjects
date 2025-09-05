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
            <h2 class="text-xl font-semibold text-gray-900">Müşteri Listesi</h2>
            <p class="text-sm text-gray-600 mt-1">Sistemde kayıtlı tüm müşteriler</p>
        </div>
        <button id="newCustomerBtn" class="btn btn-primary flex items-center justify-center">
            <i class="bi bi-person-plus mr-2"></i> Yeni Müşteri
        </button>
    </div>

    <div class="card-hover animate-fadeIn">
        <div class="p-4">
            <div class="flex flex-col md:flex-row gap-3 mb-6">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-input pl-10" placeholder="Müşteri ara (ad/telefon)">
                </div>
                <button id="clearSearch" class="btn btn-outline">
                    <i class="bi bi-x-lg mr-2"></i> Temizle
                </button>
            </div>

            <div class="table-container">
                <table id="customersTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ad Soyad</th>
                            <th>Telefon</th>
                            <th>Adres</th>
                            <th>Bakiye (₺)</th>
                            <th class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($pdo->query('SELECT * FROM customers ORDER BY id DESC') as $row):
                        ?>
                        <tr class="animate-fadeIn">
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <a href="musteri_rapor.php?customer=<?php echo $row['id']; ?>" class="hover:text-primary-900 font-medium">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td class="max-w-xs truncate"><?php echo nl2br(htmlspecialchars($row['address'])); ?></td>
                            <td class="font-medium <?php echo $row['balance'] > 0 ? 'text-danger-600' : 'text-success-600'; ?>">
                                <?php echo number_format($row['balance'], 2, ',', '.'); ?>
                            </td>
                            <td class="text-right">
                                <div class="flex space-x-2 justify-end">
                                    <a href="islemler.php?customer=<?php echo $row['id']; ?>" class="text-primary-600 hover:text-primary-900" title="İşlem Ekle">
                                        <i class="bi bi-cash-coin"></i>
                                    </a>
                                    <a href="musteriler.php?edit=<?php echo $row['id']; ?>" class="text-gray-600 hover:text-gray-900" title="Düzenle">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="musteriler.php?delete=<?php echo $row['id']; ?>" class="text-danger-600 hover:text-danger-900" onclick="return confirm('Silmek istediğinize emin misiniz?');" title="Sil">
                                        <i class="bi bi-trash"></i>
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
    background: rgba(0,0,0,0.3);
    transition: opacity 0.2s;
    align-items: center;
    justify-content: center;
}
.modal.show {
    display: flex;
}
.modal-dialog {
    max-width: 500px;
    width: 100%;
    margin: auto;
}
</style>

<div class="modal" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" class="w-full">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">
                        <?php echo $editCustomer ? 'Müşteriyi Düzenle' : 'Yeni Müşteri'; ?>
                    </h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $editCustomer ? (int)$editCustomer['id'] : 0; ?>">
                    
                    <div class="mb-4">
                        <label for="customerName" class="form-label">Ad Soyad</label>
                        <input type="text" name="name" id="customerName" class="form-input" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerPhone" class="form-label">Telefon</label>
                        <input type="text" name="phone" id="customerPhone" class="form-input" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['phone']) : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="customerAddress" class="form-label">Adres</label>
                        <textarea name="address" id="customerAddress" rows="3" class="form-input"><?php echo $editCustomer ? htmlspecialchars($editCustomer['address']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Kapat</button>
                    <button type="submit" class="btn btn-primary">
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
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });

    const setupSearch = () => {
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearch');
        const table = document.getElementById('customersTable');
        const key = 'customers_search';
        
        if (!input || !table) return;
        
        input.value = localStorage.getItem(key) || '';
        
        function applyFilter() {
            const q = input.value.toLowerCase();
            const rows = table.tBodies[0].rows;
            let visibleCount = 0;
            
            for (const tr of rows) {
                const text = tr.innerText.toLowerCase();
                const isVisible = text.includes(q);
                tr.style.display = isVisible ? '' : 'none';
                tr.classList.toggle('animate-fadeIn', isVisible);
                if (isVisible) visibleCount++;
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
            if (link.getAttribute('href') === currentPath || 
                (currentPath.includes('customers.php') && link.getAttribute('href').includes('customers.php'))) {
                link.classList.add('text-primary-600', 'font-medium');
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
        const firstInput = modal.querySelector('input, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeModal() {
    const modal = document.getElementById('customerModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        const form = modal.querySelector('form');
        if (form && !form.querySelector('input[name="id"]').value) {
            form.reset();
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>