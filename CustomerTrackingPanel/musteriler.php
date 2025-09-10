<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php
 goto NT3l4; i2YHB: ?>
</button></div></form></div></div></div><script>document.addEventListener('DOMContentLoaded', function() {
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
    
    // Düzenleme modunda modalı aç<?php  goto hgDph; zn2VY: if ($_SERVER["\122\x45\121\x55\105\123\x54\137\x4d\105\x54\x48\x4f\x44"] === "\120\117\123\x54") { $id = isset($_POST["\x69\x64"]) ? (int) $_POST["\151\144"] : 0; $isim = trim($_POST["\x6e\x61\x6d\x65"]); $numara = trim($_POST["\x70\x68\x6f\x6e\x65"]); $adres = trim($_POST["\141\144\x64\x72\x65\163\x73"]); try { if ($id > 0) { $stmt = $pdo->prepare("\125\120\104\x41\124\x45\x20\155\165\163\164\x65\x72\151\x6c\x65\x72\40\123\105\x54\x20\x69\163\x69\155\x20\75\40\x3f\54\40\156\165\155\141\x72\x61\40\x3d\x20\77\x2c\40\141\144\162\145\163\x20\x3d\x20\77\x20\x57\110\x45\x52\105\x20\151\x64\x20\75\x20\x3f"); $stmt->execute(array($isim, $numara, $adres, $id)); $message = "\115\303\274\305\x9f\x74\145\x72\x69\40\x62\x61\xc5\x9f\141\x72\304\261\171\154\x61\40\147\xc3\xbc\156\143\145\154\x6c\x65\156\144\x69\x2e"; } else { $stmt = $pdo->prepare("\x49\x4e\x53\105\122\124\x20\x49\x4e\124\117\x20\x6d\x75\163\x74\x65\x72\151\154\145\162\x20\x28\151\163\151\x6d\x2c\40\156\x75\155\141\162\x61\x2c\x20\141\144\162\145\163\51\40\x56\x41\114\x55\105\x53\40\50\x3f\x2c\x20\x3f\x2c\40\77\x29"); $stmt->execute(array($isim, $numara, $adres)); $message = "\115\303\274\305\237\x74\145\x72\x69\40\x62\x61\305\237\x61\162\304\xb1\x79\154\x61\x20\x65\x6b\x6c\145\x6e\x64\x69\x2e"; } header("\114\x6f\x63\141\164\151\x6f\156\x3a\x20\155\x75\x73\x74\x65\162\151\x6c\145\x72\56\x70\150\160\77\163\165\143\x63\x65\163\163\x3d" . urlencode($message)); die; } catch (Exception $e) { $error = "\304\260\305\x9f\154\145\x6d\x20\142\x61\xc5\x9f\141\x72\304\261\163\xc4\261\172\72\x20" . $e->getMessage(); } } goto CDBTS; v49Hq: echo $editCustomer ? (int) $editCustomer["\x69\x64"] : 0; goto cXUjp; uaepk: ?>
mr-2"></i><?php  goto uoLTB; ofJa1: require_once __DIR__ . "\x2f\x69\x6e\x63\154\165\144\145\163\x2f\x68\x65\141\144\145\162\56\x70\x68\x70"; goto mPjvv; O53rY: ?>
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
}</script><?php  goto rMPYu; ZuUw6: if (isset($error)) { ?>
<div class="mb-4 alert alert-danger"><?php  echo htmlspecialchars($error); ?>
</div><?php  } goto kLm9w; UyA26: echo $editCustomer ? htmlspecialchars($editCustomer["\x69\163\151\x6d"]) : ''; goto W4xr3; mZNRJ: if (isset($_GET["\x73\165\143\x63\145\x73\163"])) { ?>
<div class="mb-4 alert alert-success"><?php  echo htmlspecialchars($_GET["\163\x75\143\x63\145\x73\163"]); ?>
</div><?php  } goto ZuUw6; nr21R: ?>
"></div><div class="mb-4"><label class="flex items-center form-label"for="customerAddress">Adres</label> <textarea class="form-input"id="customerAddress"name="address"placeholder="Adres bilgisi"rows="3"><?php  goto z_R2J; HKcAa: ?>
</h5><button class="transition-colors hover:text-gray-700 text-gray-400"type="button"onclick="closeModal()"aria-label="Close"><i class="bi bi-x-circle"></i></button></div><div class="p-5 modal-body"><input name="id"value="<?php  goto v49Hq; U1Cth: if (empty($customers)) { ?>
<tr><td class="py-12 text-center text-gray-500"colspan="5"><div class="flex items-center justify-center flex-col gap-3"><div class="bg-gray-100 mb-2 p-4 rounded-full"><i class="bi text-primary-500 bi-person-circle text-5xl"></i></div><h4 class="font-medium text-lg">Henüz kayıtlı müşteri yok</h4><button class="btn btn-outline mt-2"type="button"onclick="showModal()"><i class="bi mr-1 bi-person-plus-fill"></i> İlk Müşteriyi Ekle</button></div></td></tr><?php  } else { foreach ($customers as $index => $row) { ?>
<tr class="transition-colors animate-fadeIn hover:bg-gray-50"style="animation-delay:<?php  echo $index * 0.05; ?>
s"><td><a class="font-medium hover:text-primary-900 text-primary-700 transition-colors"href="musteri_rapor.php?customer=<?php  echo $row["\x69\144"]; ?>
"><?php  echo htmlspecialchars($row["\151\x73\x69\x6d"]); ?>
</a></td><td><?php  echo htmlspecialchars($row["\156\165\155\x61\x72\x61"]); ?>
</td><td class="max-w-xs truncate"><?php  echo nl2br(htmlspecialchars($row["\x61\x64\x72\145\163"])); ?>
</td><td class="font-medium<?php  echo $row["\x6e\145\164\x5f\142\x61\153\x69\x79\x65"] < 0 ? "\164\x65\170\164\55\x64\141\x6e\147\145\x72\55\x36\60\x30" : ($row["\x6e\145\164\x5f\x62\141\x6b\151\171\145"] > 0 ? "\164\145\x78\x74\55\x73\x75\143\x63\x65\163\x73\55\66\60\x30" : "\x74\x65\170\x74\55\x67\162\x61\x79\x2d\66\60\x30"); ?>
"><i class="bi<?php  echo $row["\x6e\x65\x74\137\x62\x61\x6b\x69\171\145"] < 0 ? "\x62\151\x2d\141\162\x72\x6f\x77\x2d\x64\x6f\167\x6e\x2d\143\151\x72\143\x6c\145\55\146\x69\x6c\x6c\40\x74\x65\170\164\55\x64\141\156\x67\145\162\x2d\65\x30\x30" : ($row["\x6e\x65\x74\137\142\x61\x6b\151\171\145"] > 0 ? "\x62\x69\x2d\x61\162\162\157\x77\x2d\x75\160\55\x63\151\162\x63\x6c\145\55\x66\x69\154\x6c\40\164\x65\x78\164\x2d\x73\x75\143\x63\x65\163\163\55\65\60\x30" : "\142\x69\x2d\144\141\x73\150\55\x63\151\162\143\x6c\x65\x20\164\x65\x78\164\x2d\147\162\141\171\55\65\x30\x30"); ?>
mr-1"></i><?php  echo ($row["\x6e\x65\x74\137\142\141\x6b\x69\171\145"] > 0 ? "\x2b" : '') . number_format($row["\x6e\x65\164\x5f\142\141\153\151\171\x65"], 2, "\54", "\56"); ?>
₺</td><td class="text-right"><div class="flex justify-end space-x-3"><a class="btn-icon btn-primary-outline"href="islemler.php?customer=<?php  echo $row["\x69\144"]; ?>
"title="İşlem Ekle"><i class="bi bi-cash-stack"></i> </a><a class="btn-icon btn-secondary-outline"href="musteriler.php?edit=<?php  echo $row["\151\x64"]; ?>
"title="Düzenle"><i class="bi bi-pencil-square"></i> </a><a class="btn-icon btn-danger-outline"href="musteriler.php?delete=<?php  echo $row["\151\x64"]; ?>
"title="Sil"onclick='return confirm("Bu müşteriyi ve tüm işlemlerini silmek istediğinize emin misiniz?")'><i class="bi bi-trash3"></i></a></div></td></tr><?php  } } goto ZgNGZ; aC7f0: ?>
<div class="container mx-auto px-4 py-6"><div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between"><div><h1 class="flex items-center font-bold text-2xl text-gray-800"><i class="bi mr-2 bi-people text-primary-600"></i> Müşteri Yönetimi</h1><p class="mt-1 text-gray-600 text-sm">Sistemde kayıtlı tüm müşteriler ve bakiye durumları</p></div><button class="flex items-center justify-center btn btn-primary hover:shadow-md shadow-sm transition-all"id="newCustomerBtn"><i class="bi mr-2 bi-person-plus-fill"></i> Yeni Müşteri</button></div><?php  goto mZNRJ; DqrBd: require_login(); goto uIoFX; Wq4SN: echo $editCustomer ? "\115\xc3\xbc\xc5\237\164\145\162\x69\x79\x69\x20\104\xc3\274\172\x65\156\x6c\145" : "\131\145\156\x69\40\x4d\xc3\274\305\x9f\164\x65\x72\151\40\105\x6b\154\145"; goto HKcAa; rMPYu: require_once __DIR__ . "\57\151\156\143\154\x75\144\145\163\57\146\157\157\x74\x65\162\x2e\160\x68\160"; goto Kbt0v; kaAIn: ?>
</textarea></div></div><div class="flex justify-end border-gray-200 border-t gap-2 modal-footer p-4"><button class="btn btn-secondary"type="button"onclick="closeModal()"><i class="bi mr-2 bi-x-circle"></i> Vazgeç</button> <button class="btn btn-primary"type="submit"><i class="bi<?php  goto QGfL2; CDBTS: if (isset($_GET["\x64\145\x6c\145\x74\145"])) { $delId = (int) $_GET["\x64\145\x6c\145\164\145"]; $pdo->beginTransaction(); try { $pdo->prepare("\104\105\114\x45\124\105\40\106\122\117\115\40\151\163\x6c\145\155\154\145\x72\40\x57\x48\105\122\105\40\155\165\x73\x74\145\x72\151\x5f\x69\144\40\75\x20\x3f")->execute(array($delId)); $pdo->prepare("\x44\105\114\x45\124\x45\x20\106\122\x4f\x4d\x20\x6d\165\x73\x74\145\162\151\154\x65\162\x20\x57\x48\105\x52\x45\x20\x69\x64\40\75\x20\x3f")->execute(array($delId)); $pdo->commit(); header("\114\157\143\x61\164\x69\157\x6e\x3a\x20\155\165\x73\164\145\x72\x69\154\x65\x72\x2e\x70\150\160\77\x73\x75\x63\x63\145\x73\x73\75" . urlencode("\x4d\303\274\xc5\237\164\x65\x72\151\40\166\x65\40\151\x6c\151\305\237\153\x69\154\x69\x20\164\303\xbc\155\x20\151\xc5\x9f\154\x65\x6d\154\145\x72\x20\142\x61\305\x9f\141\x72\304\xb1\171\154\141\x20\163\151\154\x69\156\x64\x69\56")); die; } catch (Exception $e) { $pdo->rollBack(); $error = "\123\151\x6c\155\145\40\151\305\x9f\x6c\x65\x6d\151\40\x62\141\305\x9f\x61\x72\xc4\xb1\x73\304\261\x7a\x3a\x20" . $e->getMessage(); } } goto ofJa1; MLdjl: echo $editCustomer ? htmlspecialchars($editCustomer["\156\x75\x6d\x61\x72\141"]) : ''; goto nr21R; xTKD0: if (isset($_GET["\x65\x64\151\x74"])) { $stmt = $pdo->prepare("\123\x45\114\105\x43\124\x20\x2a\40\x46\x52\117\x4d\40\155\165\163\x74\145\162\x69\154\x65\162\x20\x57\x48\105\x52\105\40\151\144\40\75\40\x3f"); $stmt->execute(array((int) $_GET["\x65\x64\151\x74"])); $editCustomer = $stmt->fetch(); } goto hiF3p; mPjvv: $editCustomer = null; goto xTKD0; cXUjp: ?>
"type="hidden"><div class="mb-4"><label class="flex items-center form-label"for="customerName"><i class="bi mr-2 text-primary-500 bi-person"></i> Ad Soyad</label> <input class="form-input"id="customerName"placeholder="Müşteri adını giriniz"name="name"value="<?php  goto UyA26; hiF3p: $customers = $pdo->query("\x53\105\114\105\103\124\40\x6d\x2e\x2a\54\40\12\x20\40\40\x20\x28\123\105\x4c\105\103\x54\x20\103\x4f\x41\114\105\123\x43\105\x28\123\125\x4d\50\x6d\151\153\164\141\x72\51\54\40\60\51\x20\106\x52\x4f\x4d\40\x69\163\154\145\155\x6c\145\162\40\x57\x48\x45\122\x45\40\155\x75\x73\164\145\x72\151\137\151\144\40\75\x20\155\x2e\151\144\x20\x41\x4e\x44\x20\157\144\145\x6d\145\137\164\151\x70\x69\x20\x3d\40\42\142\x6f\x72\x63\x22\51\x20\x61\x73\x20\164\157\x70\154\141\x6d\x5f\142\x6f\162\x63\54\xa\x20\40\40\x20\x28\123\105\x4c\105\103\124\x20\103\117\x41\114\x45\123\103\105\x28\x53\125\115\x28\155\151\x6b\164\x61\x72\x29\x2c\40\x30\x29\40\x46\x52\x4f\115\x20\x69\163\154\145\x6d\154\x65\x72\x20\x57\x48\x45\122\105\40\155\165\163\x74\x65\x72\x69\x5f\151\144\x20\75\40\155\56\151\144\40\101\116\104\x20\x6f\x64\x65\x6d\x65\137\164\151\x70\151\40\x3d\40\x22\164\x61\x68\x73\x69\x6c\x61\164\42\51\40\x61\163\x20\x74\x6f\x70\x6c\141\155\137\164\x61\150\x73\151\154\x61\164\12\40\40\40\40\106\x52\x4f\x4d\x20\x6d\x75\163\164\145\162\x69\154\145\x72\40\155\40\x4f\x52\104\105\122\x20\102\x59\40\155\56\x69\144\x20\x44\x45\x53\x43")->fetchAll(); goto pPIZq; z_R2J: echo $editCustomer ? htmlspecialchars($editCustomer["\x61\x64\162\145\163"]) : ''; goto kaAIn; QGfL2: echo $editCustomer ? "\x62\151\x2d\x63\x68\145\x63\x6b\55\143\151\162\x63\x6c\145" : "\x62\x69\x2d\x70\154\165\163\x2d\x63\x69\162\143\154\145"; goto uaepk; W4xr3: ?>
"required></div><div class="mb-4"><label class="flex items-center form-label"for="customerPhone"><i class="bi mr-2 text-primary-500 bi-telephone"></i> Telefon</label> <input class="form-input"id="customerPhone"placeholder="Telefon numarası"name="phone"value="<?php  goto MLdjl; ZgNGZ: ?>
</tbody></table></div></div></div></div><style>.modal{display:none;position:fixed;z-index:1050;left:0;top:0;width:100vw;height:100vh;overflow:auto;background:rgba(17,24,39,.5);backdrop-filter:blur(4px);transition:all .3s ease;align-items:center;justify-content:center}.modal.show{display:flex;animation:fadeIn .3s ease}.modal-dialog{max-width:500px;width:100%;margin:auto;transform:translateY(0);transition:transform .3s ease}@keyframes fadeIn{from{opacity:0}to{opacity:1}}</style><div class="modal"aria-hidden="true"aria-labelledby="customerModalLabel"id="customerModal"tabindex="-1"><div class="modal-dialog modal-dialog-centered shadow-xl"><div class="border-0 modal-content"><form class="w-full"method="post"><div class="border-gray-200 border-b modal-header"><h5 class="flex items-center font-bold modal-title text-lg"id="customerModalLabel"><i class="bi<?php  goto VBl5o; pPIZq: foreach ($customers as &$customer) { $customer["\x6e\145\x74\x5f\x62\141\x6b\x69\x79\x65"] = $customer["\x74\x6f\160\154\141\155\x5f\x74\x61\150\x73\151\154\141\164"] - $customer["\164\x6f\160\154\x61\155\x5f\142\157\162\143"]; } goto aC7f0; NT3l4: require_once __DIR__ . "\x2f\151\x6e\143\154\165\x64\145\163\x2f\141\x75\164\x68\x2e\160\x68\x70"; goto DqrBd; uoLTB: echo $editCustomer ? "\113\141\x79\144\145\164" : "\x45\x6b\x6c\145"; goto i2YHB; VBl5o: echo $editCustomer ? "\x62\151\x2d\160\145\156\143\x69\x6c\55\x73\161\165\141\162\145\x20\164\x65\170\164\x2d\x70\x72\151\155\x61\162\x79\55\x36\x30\60" : "\142\151\x2d\160\x65\162\163\157\x6e\x2d\160\154\165\163\x2d\146\151\x6c\x6c\x20\x74\x65\x78\164\x2d\163\x75\x63\x63\x65\163\x73\x2d\x36\x30\x30"; goto IvZHo; uIoFX: $pdo = get_pdo_connection(); goto zn2VY; IvZHo: ?>
mr-2"></i><?php  goto Wq4SN; hgDph: if ($editCustomer) { ?>
showModal();<?php  } goto O53rY; kLm9w: ?>
<div class="animate-fadeIn card-hover shadow-lg"><div class="p-5"><div class="flex flex-col gap-4 mb-6 md:flex-row"><div class="flex-grow relative"><div class="flex items-center absolute inset-y-0 left-0 pl-3 pointer-events-none"><i class="bi text-primary-500 bi-search"></i></div><input class="form-input pl-10"id="searchInput"placeholder="Müşteri ara (ad/telefon)"></div><button class="btn btn-secondary"id="clearSearch"><i class="bi mr-2 bi-x-circle"></i> Temizle</button></div><div class="table-container"><table class="table table-hover"id="customersTable"><thead><tr><th><i class="bi text-primary-500 mr-1 bi-person-badge"></i> Ad Soyad</th><th><i class="bi text-primary-500 mr-1 bi-telephone"></i> Telefon</th><th><i class="bi text-primary-500 mr-1 bi-geo-alt"></i> Adres</th><th><i class="bi text-primary-500 mr-1 bi-cash-coin"></i> Bakiye (₺)</th><th class="text-right">İşlemler</th></tr></thead><tbody><?php  goto U1Cth; Kbt0v: ?>