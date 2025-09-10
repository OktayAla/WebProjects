<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
 goto KdCUa; A1U5Y: echo $editUser ? '' : "\x72\145\x71\165\151\162\145\144"; goto uNTZf; nmYF8: ?>
</tbody></table></div></div></div><div class="flex items-center justify-center bg-black bg-opacity-50 fixed hidden inset-0 z-50"id="userModal"><div class="bg-white max-w-md mx-4 rounded-lg shadow-xl w-full"><div class="p-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="font-semibold text-gray-900 text-lg"id="modalTitle">Yeni Kullanıcı Ekle</h3><button class="hover:text-gray-500 text-gray-400"id="closeModal"><i class="bi bi-x-lg"></i></button></div></div><form class="p-5"id="userForm"method="post"><input id="userId"name="id"type="hidden"value="<?php  goto CoNUg; Zw3ds: if ($editUser) { ?>
openModal(true);<?php  } goto B3S4g; bPEVl: $current_user = current_user(); goto t4hoP; t4hoP: if ($current_user["\x72\x6f\x6c"] !== "\141\x64\x6d\x69\156") { header("\114\x6f\x63\141\164\x69\x6f\156\72\40\151\156\x64\145\x78\56\160\x68\160\x3f\x65\x72\x72\157\162\75" . urlencode("\102\165\40\163\x61\171\x66\141\x79\141\x20\145\162\x69\xc5\x9f\x69\x6d\40\171\145\164\x6b\151\x6e\151\172\x20\x62\165\154\x75\156\x6d\x61\x6d\141\153\164\x61\144\xc4\261\x72\56")); die; } goto IWhE9; dEF32: echo $editUser && $editUser["\x72\157\154"] === "\165\163\145\x72" ? "\x73\145\x6c\145\x63\164\145\144" : ''; goto HrcIa; fIZLB: echo $editUser ? htmlspecialchars($editUser["\151\x73\x69\x6d"]) : ''; goto SYOYD; pJORn: require_once __DIR__ . "\57\x69\156\x63\x6c\x75\x64\145\163\x2f\150\145\x61\144\145\x72\x2e\160\x68\x70"; goto u5Aet; cg3P2: if (isset($_GET["\x65\x64\x69\164"])) { $stmt = $pdo->prepare("\123\x45\x4c\x45\103\x54\x20\52\40\106\122\117\115\40\x6b\165\x6c\154\141\x6e\151\143\x69\x6c\x61\162\40\x57\x48\x45\x52\x45\40\151\144\40\x3d\x20\x3f"); $stmt->execute(array((int) $_GET["\x65\x64\151\x74"])); $editUser = $stmt->fetch(); } goto Jua1j; HrcIa: ?>
>Kullanıcı</option><option value="admin"<?php  goto G8gVH; Vosxz: if ($_SERVER["\x52\x45\x51\x55\105\123\x54\137\115\x45\x54\x48\x4f\104"] === "\x50\x4f\123\x54") { $id = isset($_POST["\151\x64"]) ? (int) $_POST["\x69\x64"] : 0; $isim = trim($_POST["\156\141\x6d\x65"]); $eposta = "\151\156\x66\157\x40\x6d\141\151\x6c\56\143\x6f\x6d"; $sifre = trim($_POST["\160\x61\163\x73\167\157\162\144"]); $rol = trim($_POST["\x72\157\154\x65"]); try { if ($id > 0) { if (!empty($sifre)) { $hashed_password = password_hash($sifre, PASSWORD_DEFAULT); $stmt = $pdo->prepare("\x55\x50\104\101\x54\x45\40\153\165\154\x6c\141\x6e\151\x63\151\154\141\x72\40\123\105\124\x20\x69\163\x69\x6d\40\x3d\40\77\x2c\x20\x65\x70\x6f\163\x74\x61\40\75\x20\x3f\x2c\x20\x73\x69\146\x72\x65\40\x3d\40\77\x2c\40\162\157\x6c\x20\75\40\x3f\40\x57\110\x45\122\x45\40\151\x64\x20\x3d\40\x3f"); $stmt->execute(array($isim, $eposta, $hashed_password, $rol, $id)); } else { $stmt = $pdo->prepare("\x55\120\x44\x41\x54\x45\40\153\x75\154\x6c\141\156\x69\143\x69\154\x61\x72\40\123\x45\124\x20\x69\x73\x69\x6d\40\x3d\x20\x3f\54\x20\145\160\157\x73\x74\141\40\x3d\40\x3f\x2c\40\x72\x6f\x6c\40\x3d\40\x3f\x20\127\x48\x45\122\x45\x20\151\x64\40\x3d\40\x3f"); $stmt->execute(array($isim, $eposta, $rol, $id)); } $message = "\113\x75\x6c\x6c\x61\x6e\304\261\143\304\261\x20\142\141\305\237\141\x72\304\xb1\x79\154\141\x20\x67\xc3\xbc\156\143\145\154\x6c\145\x6e\144\151\x2e"; } else { if (empty($sifre)) { throw new Exception("\131\145\156\151\40\x6b\x75\154\154\x61\x6e\xc4\261\143\xc4\261\40\151\303\xa7\151\x6e\x20\305\x9f\x69\x66\x72\145\40\x67\x65\162\x65\x6b\x6c\151\x64\x69\162\56"); } $hashed_password = password_hash($sifre, PASSWORD_DEFAULT); $stmt = $pdo->prepare("\x49\x4e\123\x45\x52\x54\x20\111\116\124\117\40\153\x75\x6c\154\141\156\x69\143\x69\154\141\162\x20\50\x69\x73\x69\x6d\x2c\x20\145\x70\x6f\163\x74\141\54\40\x73\x69\x66\x72\145\54\x20\162\157\154\x2c\40\157\154\x75\x73\x74\x75\162\x6d\x61\x5f\172\141\x6d\141\156\x69\x29\x20\126\101\x4c\x55\x45\x53\40\x28\77\54\40\x3f\x2c\40\77\x2c\40\77\54\x20\116\117\x57\50\51\x29"); $stmt->execute(array($isim, $eposta, $hashed_password, $rol)); $message = "\x4b\x75\x6c\x6c\141\156\304\261\x63\xc4\xb1\40\142\141\xc5\x9f\x61\162\304\xb1\171\x6c\141\x20\145\x6b\154\x65\x6e\x64\x69\56"; } header("\x4c\x6f\x63\x61\164\151\157\x6e\72\x20\153\x75\x6c\154\x61\x6e\151\x63\x69\x6c\141\x72\56\x70\x68\x70\x3f\x73\x75\143\143\145\163\x73\x3d" . urlencode($message)); die; } catch (Exception $e) { $error = "\304\xb0\xc5\x9f\x6c\x65\x6d\x20\x62\141\xc5\237\141\162\304\xb1\163\xc4\xb1\172\x3a\x20" . $e->getMessage(); } } goto F98AM; u5Aet: $editUser = null; goto cg3P2; gGTJL: echo $editUser ? "\x28\x44\145\xc4\x9f\x69\305\x9f\x74\151\162\155\x65\153\40\x69\163\x74\145\155\x69\171\x6f\x72\163\141\x6e\xc4\261\x7a\40\142\x6f\305\237\x20\x62\xc4\xb1\162\141\153\xc4\xb1\x6e\x29" : ''; goto qSA4P; CoNUg: echo $editUser ? $editUser["\151\144"] : ''; goto rlSxB; IWhE9: $pdo = get_pdo_connection(); goto Vosxz; WfjUZ: if (isset($error)) { ?>
<div class="mb-4 alert alert-danger"><?php  echo htmlspecialchars($error); ?>
</div><?php  } goto DSmV0; vawNt: if (isset($_GET["\x65\162\162\x6f\x72"])) { ?>
<div class="mb-4 alert alert-danger"><?php  echo htmlspecialchars($_GET["\145\162\x72\157\162"]); ?>
</div><?php  } goto WfjUZ; O9Eso: require_once __DIR__ . "\57\151\156\143\154\x75\x64\145\x73\57\146\x6f\157\x74\145\162\56\160\x68\160"; goto Ls3XO; mkWmJ: ?>
>Admin</option></select></div><div class="flex justify-end mt-6 space-x-3"><button class="btn btn-secondary"id="cancelBtn"type="button">İptal</button> <button class="btn btn-primary"type="submit">Kaydet</button></div></form></div></div></div><script>document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const userTableBody = document.getElementById('userTableBody');
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const userId = document.getElementById('userId');
        const newUserBtn = document.getElementById('newUserBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Arama işlevi
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = userTableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const role = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || role.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Modal açma/kapama işlevleri
        function openModal(isEdit = false) {
            modalTitle.textContent = isEdit ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle';
            userModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeModalFunc() {
            userModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            if (!userId.value) {
                userForm.reset();
            }
        }
        
        newUserBtn.addEventListener('click', () => openModal(false));
        closeModal.addEventListener('click', closeModalFunc);
        cancelBtn.addEventListener('click', closeModalFunc);
        
        // URL'de edit parametresi varsa modalı aç<?php  goto Zw3ds; BtjbO: ?>
<div class="container mx-auto px-4 py-6"><div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between"><div><h1 class="flex items-center font-bold text-2xl text-gray-800"><i class="bi mr-2 bi-people-fill text-primary-600"></i> Kullanıcı Yönetimi</h1><p class="text-sm mt-1 text-gray-600">Sistemde kayıtlı tüm kullanıcılar ve yetkileri</p></div><button class="flex items-center justify-center btn btn-primary hover:shadow-md shadow-sm transition-all"id="newUserBtn"><i class="bi mr-2 bi-person-plus-fill"></i> Yeni Kullanıcı</button></div><?php  goto h9_zH; qSA4P: ?>
</label> <input id="password"class="form-input"name="password"type="password"<?php  goto A1U5Y; Jua1j: $users = $pdo->query("\x53\x45\114\x45\x43\x54\x20\x2a\x20\106\x52\117\x4d\x20\153\165\x6c\x6c\x61\x6e\x69\143\151\154\x61\162\x20\117\122\104\x45\x52\40\x42\131\x20\151\x64\40\104\x45\123\x43")->fetchAll(); goto BtjbO; SYOYD: ?>
"></div><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="password">Şifre<?php  goto gGTJL; uNTZf: ?>
></div><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="role">Rol</label> <select class="form-select"id="role"name="role"><option value="user"<?php  goto dEF32; DSmV0: ?>
<div class="animate-fadeIn card-hover shadow-lg"><div class="p-5"><div class="flex flex-col gap-4 mb-6 md:flex-row"><div class="flex-grow relative"><div class="flex items-center absolute inset-y-0 left-0 pl-3 pointer-events-none"><i class="bi bi-search text-primary-500"></i></div><input id="searchInput"class="form-input pl-10"placeholder="Kullanıcı ara (isim/rol)"></div></div><div class="overflow-x-auto"><table class="divide-gray-200 divide-y min-w-full"><thead><tr><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-left">İsim</th><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-left">Rol</th><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-right">İşlemler</th></tr></thead><tbody class="bg-white divide-gray-200 divide-y"id="userTableBody"><?php  goto a9jGC; F98AM: if (isset($_GET["\x64\x65\x6c\x65\164\145"])) { $delId = (int) $_GET["\144\145\x6c\x65\164\145"]; if ($delId === (int) $current_user["\151\x64"]) { header("\114\157\143\x61\164\151\157\x6e\x3a\x20\153\x75\x6c\x6c\141\156\151\143\x69\154\x61\162\x2e\160\150\160\x3f\x65\162\x72\x6f\162\75" . urlencode("\113\145\x6e\x64\151\x20\150\145\x73\141\x62\304\261\156\304\xb1\x7a\xc4\xb1\x20\x73\x69\154\145\155\x65\x7a\163\151\x6e\x69\x7a\x2e")); die; } try { $stmt = $pdo->prepare("\104\105\114\x45\x54\105\x20\x46\x52\x4f\x4d\40\x6b\x75\154\154\x61\x6e\151\x63\x69\154\141\162\40\x57\x48\105\122\105\40\151\x64\40\75\x20\x3f"); $stmt->execute(array($delId)); header("\114\157\143\141\x74\151\157\156\72\x20\153\x75\154\x6c\141\156\151\x63\151\154\x61\x72\x2e\x70\x68\x70\x3f\163\165\x63\143\145\163\x73\75" . urlencode("\113\165\x6c\x6c\x61\x6e\304\xb1\143\304\261\x20\x62\141\305\x9f\x61\x72\xc4\261\171\154\141\40\x73\151\154\151\x6e\144\151\x2e")); die; } catch (Exception $e) { $error = "\123\x69\x6c\155\145\40\x69\xc5\237\x6c\145\x6d\151\x20\142\141\305\237\141\162\304\261\163\304\261\x7a\x3a\40" . $e->getMessage(); } } goto pJORn; a9jGC: foreach ($users as $user) { ?>
<tr class="hover:bg-gray-50 transition-colors"><td class="font-medium px-6 py-4 text-sm whitespace-nowrap text-gray-900"><?php  echo htmlspecialchars($user["\151\x73\151\x6d"]); ?>
</td><td class="text-sm px-6 py-4 whitespace-nowrap text-gray-500"><?php  if ($user["\x72\157\x6c"] === "\x61\144\x6d\151\x6e") { ?>
<span class="text-xs font-semibold inline-flex leading-5 px-2 rounded-full bg-primary-100 text-primary-800">Admin</span><?php  } else { ?>
<span class="text-xs font-semibold inline-flex leading-5 px-2 rounded-full bg-gray-100 text-gray-800">Kullanıcı</span><?php  } ?>
</td><td class="font-medium px-6 py-4 text-sm whitespace-nowrap text-right"><div class="flex justify-end space-x-2"><a class="text-primary-600 hover:text-primary-900"href="?edit=<?php  echo $user["\x69\144"]; ?>
"title="Düzenle"><i class="bi bi-pencil-square"></i> </a><?php  if ((int) $user["\x69\x64"] !== (int) $current_user["\x69\144"]) { ?>
<a class="hover:text-danger-900 text-danger-600"href="?delete=<?php  echo $user["\151\144"]; ?>
"title="Sil"onclick='return confirm("Bu kullanıcıyı silmek istediğinize emin misiniz?")'><i class="bi bi-trash"></i> </a><?php  } ?>
</div></td></tr><?php  } goto nmYF8; rlSxB: ?>
"><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="name">İsim</label> <input id="name"class="form-input"name="name"required value="<?php  goto fIZLB; B3S4g: ?>
});</script><?php  goto O9Eso; h9_zH: if (isset($_GET["\x73\165\143\x63\145\x73\x73"])) { ?>
<div class="mb-4 alert alert-success"><?php  echo htmlspecialchars($_GET["\x73\165\x63\x63\145\163\163"]); ?>
</div><?php  } goto vawNt; KdCUa: require_once __DIR__ . "\x2f\x69\x6e\x63\154\x75\144\145\x73\x2f\x61\x75\164\150\x2e\160\x68\x70"; goto C_xVF; C_xVF: require_login(); goto bPEVl; G8gVH: echo $editUser && $editUser["\x72\157\x6c"] === "\141\144\155\151\156" ? "\163\145\154\145\x63\164\x65\x64" : ''; goto mkWmJ; Ls3XO: ?>