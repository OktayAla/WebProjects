<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php
 goto MqJPX; IPRMx: foreach ($users as $user) { ?>
<tr class="hover:bg-gray-50 transition-colors"><td class="font-medium px-6 py-4 text-sm whitespace-nowrap text-gray-900"><?php  echo htmlspecialchars($user["\x69\x73\x69\155"]); ?>
</td><td class="text-sm px-6 py-4 whitespace-nowrap text-gray-500"><?php  if ($user["\x72\157\x6c"] === "\x61\x64\155\x69\x6e") { ?>
<span class="text-xs font-semibold inline-flex leading-5 px-2 rounded-full bg-primary-100 text-primary-800">Admin</span><?php  } else { ?>
<span class="text-xs font-semibold inline-flex leading-5 px-2 rounded-full bg-gray-100 text-gray-800">Kullanıcı</span><?php  } ?>
</td><td class="font-medium px-6 py-4 text-sm whitespace-nowrap text-right"><div class="flex justify-end space-x-2"><a class="text-primary-600 hover:text-primary-900"href="?edit=<?php  echo $user["\151\144"]; ?>
"title="Düzenle"><i class="bi bi-pencil-square"></i> </a><?php  if ((int) $user["\x69\x64"] !== (int) $current_user["\151\x64"]) { ?>
<a class="hover:text-danger-900 text-danger-600"href="?delete=<?php  echo $user["\151\x64"]; ?>
"title="Sil"onclick='return confirm("Bu kullanıcıyı silmek istediğinize emin misiniz?")'><i class="bi bi-trash"></i> </a><?php  } ?>
</div></td></tr><?php  } goto JMpxl; NqRrW: require_once __DIR__ . "\57\x69\156\x63\154\165\144\x65\x73\57\x68\x65\x61\144\x65\x72\x2e\x70\150\160"; goto b7XMB; wdqzJ: if (isset($_GET["\144\x65\154\x65\164\x65"])) { $delId = (int) $_GET["\144\145\154\145\x74\145"]; if ($delId === (int) $current_user["\x69\144"]) { header("\x4c\157\143\x61\164\x69\x6f\x6e\72\40\x6b\165\154\154\x61\x6e\151\x63\151\x6c\x61\162\x2e\160\x68\x70\77\145\162\x72\157\x72\75" . urlencode("\x4b\x65\156\x64\x69\x20\150\145\163\141\x62\304\261\156\xc4\xb1\x7a\304\xb1\40\163\151\x6c\145\x6d\145\172\163\x69\x6e\151\x7a\x2e")); die; } try { $stmt = $pdo->prepare("\x44\105\114\x45\x54\x45\x20\106\122\x4f\115\x20\153\165\x6c\154\141\156\151\143\x69\x6c\141\162\40\127\110\x45\122\105\x20\151\x64\40\75\40\77"); $stmt->execute(array($delId)); header("\114\157\x63\x61\164\151\157\x6e\72\40\153\165\154\154\x61\x6e\x69\143\151\154\141\x72\56\160\150\160\x3f\163\x75\143\143\145\x73\163\x3d" . urlencode("\113\x75\154\154\141\x6e\xc4\xb1\x63\304\xb1\x20\x62\x61\xc5\x9f\141\162\304\261\x79\x6c\141\x20\163\151\154\151\x6e\144\x69\56")); die; } catch (Exception $e) { $error = "\123\x69\x6c\x6d\x65\x20\x69\xc5\237\x6c\145\155\151\x20\142\141\305\x9f\x61\162\304\xb1\163\xc4\xb1\172\x3a\x20" . $e->getMessage(); } } goto NqRrW; gFX4m: ?>
<div class="container mx-auto px-4 py-6"><div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between"><div><h1 class="flex items-center font-bold text-2xl text-gray-800"><i class="bi mr-2 bi-people-fill text-primary-600"></i> Kullanıcı Yönetimi</h1><p class="text-sm mt-1 text-gray-600">Sistemde kayıtlı tüm kullanıcılar ve yetkileri</p></div><button class="flex items-center justify-center btn btn-primary hover:shadow-md shadow-sm transition-all"id="newUserBtn"><i class="bi mr-2 bi-person-plus-fill"></i> Yeni Kullanıcı</button></div><?php  goto obxBF; kasnD: if (isset($_GET["\x65\x64\151\x74"])) { $stmt = $pdo->prepare("\x53\x45\x4c\105\x43\124\40\x2a\x20\106\122\117\115\40\x6b\165\154\x6c\x61\x6e\x69\143\x69\154\141\162\40\x57\x48\105\122\105\x20\x69\x64\40\x3d\40\x3f"); $stmt->execute(array((int) $_GET["\x65\144\x69\x74"])); $editUser = $stmt->fetch(); } goto NjwqD; BSXNb: ?>
<div class="animate-fadeIn card-hover shadow-lg"><div class="p-5"><div class="flex flex-col gap-4 mb-6 md:flex-row"><div class="flex-grow relative"><div class="flex items-center absolute inset-y-0 left-0 pl-3 pointer-events-none"><i class="bi bi-search text-primary-500"></i></div><input id="searchInput"class="form-input pl-10"placeholder="Kullanıcı ara (isim/rol)"></div></div><div class="overflow-x-auto"><table class="divide-gray-200 divide-y min-w-full"><thead><tr><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-left">İsim</th><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-left">Rol</th><th class="font-medium px-6 bg-gray-50 py-3 text-gray-500 text-xs tracking-wider uppercase text-right">İşlemler</th></tr></thead><tbody class="bg-white divide-gray-200 divide-y"id="userTableBody"><?php  goto IPRMx; TgH9p: ?>
</label> <input id="password"class="form-input"name="password"type="password"<?php  goto nLwI2; APZ30: if ($current_user["\162\x6f\x6c"] !== "\x61\144\155\151\x6e") { header("\114\x6f\143\141\164\x69\x6f\x6e\x3a\40\x69\x6e\144\x65\x78\56\x70\x68\x70\77\145\x72\x72\157\x72\x3d" . urlencode("\102\165\40\163\141\171\x66\141\171\x61\40\x65\162\151\305\237\x69\155\x20\x79\145\164\153\151\156\151\x7a\40\142\x75\x6c\x75\156\x6d\x61\x6d\141\153\x74\x61\144\xc4\261\x72\x2e")); die; } goto viScM; kRSsA: if (isset($_GET["\x65\162\x72\157\x72"])) { ?>
<div class="mb-4 alert alert-danger"><?php  echo htmlspecialchars($_GET["\145\162\x72\x6f\x72"]); ?>
</div><?php  } goto SsUtO; Hh2NC: ?>
"><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="name">İsim</label> <input id="name"class="form-input"name="name"required value="<?php  goto CFpFl; NjwqD: $users = $pdo->query("\x53\x45\x4c\x45\x43\x54\x20\52\40\x46\x52\x4f\115\x20\x6b\165\x6c\154\x61\x6e\x69\x63\x69\154\x61\162\40\x4f\122\x44\x45\122\40\102\x59\x20\151\144\x20\104\105\123\x43")->fetchAll(); goto gFX4m; ws1HI: ?>
"></div><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="password">Şifre<?php  goto PlFSp; DLiwC: echo $editUser && $editUser["\x72\157\154"] === "\x75\x73\x65\x72" ? "\x73\145\x6c\x65\143\164\145\144" : ''; goto yDk1H; PlFSp: echo $editUser ? "\x28\x44\145\304\x9f\x69\xc5\237\164\151\x72\x6d\x65\153\40\x69\163\x74\145\155\x69\171\157\162\163\141\x6e\304\xb1\172\40\142\157\305\237\x20\x62\304\xb1\x72\x61\x6b\xc4\261\156\51" : ''; goto TgH9p; MqJPX: require_once __DIR__ . "\x2f\x69\156\x63\x6c\x75\x64\145\x73\57\141\x75\x74\x68\x2e\x70\150\160"; goto ZR6md; b7XMB: $editUser = null; goto kasnD; JMpxl: ?>
</tbody></table></div></div></div><div class="flex items-center justify-center bg-black bg-opacity-50 fixed hidden inset-0 z-50"id="userModal"><div class="bg-white max-w-md mx-4 rounded-lg shadow-xl w-full"><div class="p-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="font-semibold text-gray-900 text-lg"id="modalTitle">Yeni Kullanıcı Ekle</h3><button class="hover:text-gray-500 text-gray-400"id="closeModal"><i class="bi bi-x-lg"></i></button></div></div><form class="p-5"id="userForm"method="post"><input id="userId"name="id"type="hidden"value="<?php  goto DAChj; GON2A: require_once __DIR__ . "\x2f\151\156\x63\154\x75\x64\145\x73\57\146\157\x6f\x74\x65\x72\x2e\x70\x68\160"; goto J9p8i; ZLp6Y: ?>
></div><div class="mb-4"><label class="font-medium text-sm block mb-1 text-gray-700"for="role">Rol</label> <select class="form-select"id="role"name="role"><option value="user"<?php  goto DLiwC; ZXrxg: echo $editUser && $editUser["\162\157\x6c"] === "\x61\x64\155\x69\156" ? "\163\x65\154\x65\143\x74\145\x64" : ''; goto jScyK; rowm1: if ($_SERVER["\122\x45\121\x55\105\x53\x54\137\x4d\105\124\x48\117\x44"] === "\x50\117\x53\124") { $id = isset($_POST["\x69\144"]) ? (int) $_POST["\151\144"] : 0; $isim = trim($_POST["\156\141\155\x65"]); $eposta = generate_unique_email($pdo); $sifre = trim($_POST["\x70\x61\x73\x73\x77\x6f\162\x64"]); $rol = trim($_POST["\162\157\154\x65"]); try { if ($id > 0) { if (!empty($sifre)) { $hashed_password = password_hash($sifre, PASSWORD_DEFAULT); $stmt = $pdo->prepare("\x55\120\x44\x41\x54\x45\40\x6b\x75\x6c\x6c\141\156\151\x63\x69\x6c\141\x72\40\123\105\x54\40\x69\163\151\155\40\x3d\x20\x3f\54\40\163\x69\x66\162\x65\x20\x3d\40\x3f\x2c\x20\x72\x6f\x6c\40\x3d\x20\77\x20\x57\x48\105\122\105\x20\x69\144\40\75\x20\77"); $stmt->execute(array($isim, $hashed_password, $rol, $id)); } else { $stmt = $pdo->prepare("\x55\120\104\x41\124\105\x20\153\x75\154\x6c\141\156\x69\x63\151\x6c\141\x72\x20\x53\x45\124\x20\151\x73\x69\155\40\75\40\x3f\x2c\40\x72\157\x6c\40\75\x20\77\x20\x57\110\105\x52\x45\x20\x69\144\x20\x3d\x20\x3f"); $stmt->execute(array($isim, $rol, $id)); } $message = "\113\165\154\154\x61\x6e\xc4\xb1\x63\xc4\xb1\40\142\141\xc5\x9f\141\162\304\xb1\x79\154\141\40\147\303\274\156\x63\x65\154\154\x65\x6e\x64\x69\56"; } else { if (empty($sifre)) { throw new Exception("\x59\145\156\x69\x20\x6b\165\154\154\141\156\xc4\xb1\x63\304\261\x20\151\303\xa7\151\156\40\305\237\x69\x66\162\145\40\147\x65\162\x65\x6b\x6c\x69\x64\x69\162\x2e"); } $hashed_password = password_hash($sifre, PASSWORD_DEFAULT); $stmt = $pdo->prepare("\x49\116\123\105\x52\124\x20\x49\x4e\124\117\x20\153\x75\154\154\x61\156\151\x63\x69\x6c\x61\162\x20\x28\x69\x73\x69\x6d\x2c\x20\145\160\157\x73\x74\141\x2c\x20\163\151\x66\x72\x65\x2c\40\162\157\x6c\x2c\40\x6f\154\x75\163\164\165\162\x6d\x61\137\x7a\141\155\x61\x6e\151\51\x20\126\101\114\125\x45\x53\40\50\x3f\x2c\40\77\x2c\40\77\54\40\x3f\x2c\x20\x4e\x4f\x57\50\51\x29"); $stmt->execute(array($isim, $eposta, $hashed_password, $rol)); $message = "\x4b\x75\154\154\141\156\304\261\143\xc4\261\40\x62\x61\305\x9f\141\162\xc4\xb1\x79\154\141\40\145\x6b\154\145\156\x64\151\x2e"; } header("\x4c\157\x63\x61\164\151\157\x6e\x3a\x20\153\x75\154\154\141\x6e\151\x63\x69\x6c\x61\x72\56\160\150\160\x3f\163\x75\x63\143\145\x73\x73\75" . urlencode($message)); die; } catch (Exception $e) { $error = "\xc4\xb0\305\x9f\x6c\145\155\40\x62\141\305\237\x61\162\xc4\261\163\304\261\x7a\72\40" . $e->getMessage(); } } goto wdqzJ; MLorY: $current_user = current_user(); goto APZ30; nLwI2: echo $editUser ? '' : "\x72\x65\161\x75\x69\162\145\x64"; goto ZLp6Y; jScyK: ?>
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
        
        // URL'de edit parametresi varsa modalı aç<?php  goto q1St3; q1St3: if ($editUser) { ?>
openModal(true);<?php  } goto m6z1x; DAChj: echo $editUser ? $editUser["\x69\144"] : ''; goto Hh2NC; ZR6md: require_login(); goto MLorY; yDk1H: ?>
>Kullanıcı</option><option value="admin"<?php  goto ZXrxg; viScM: $pdo = get_pdo_connection(); goto f5Yop; SsUtO: if (isset($error)) { ?>
<div class="mb-4 alert alert-danger"><?php  echo htmlspecialchars($error); ?>
</div><?php  } goto BSXNb; m6z1x: ?>
});</script><?php  goto GON2A; f5Yop: function generate_unique_email($pdo) { $adjectives = array("\151\x6e\146\x6f", "\163\x75\160\x70\x6f\x72\x74", "\x63\157\x6e\x74\x61\143\164", "\163\145\162\x76\x69\x63\x65", "\150\x65\154\x70", "\x74\145\141\155", "\x61\x63\x63\157\x75\x6e\x74", "\x6e\157\164\x69\x66", "\x73\171\x73\x74\145\155", "\143\154\x69\145\x6e\x74", "\x6d\145\x6d\142\145\162", "\x75\163\x65\x72", "\160\x6f\162\164\x61\x6c", "\163\145\x63\x75\162\x65", "\x66\141\x73\164", "\163\x6d\141\x72\164", "\x70\x72\x69\x6d\x65", "\156\157\166\x61", "\141\154\160\150\x61", "\x62\x65\164\x61", "\144\x65\x6c\164\x61"); $nouns = array("\155\141\151\x6c", "\x64\x65\x73\153", "\x62\157\x78", "\143\x65\156\164\145\162", "\150\165\142", "\143\157\162\x65", "\165\x6e\x69\x74", "\154\x69\x6e\153", "\143\x6c\x6f\x75\x64", "\x62\x61\x73\145", "\147\141\x74\145", "\156\x6f\144\145", "\154\x69\x6e\x65", "\146\154\x6f\x77", "\x67\162\151\144", "\x6d\145\x73\x68", "\172\157\156\x65", "\x70\157\x69\x6e\x74", "\x66\151\145\x6c\x64", "\x73\164\141\x63\153"); $domains = array("\x6d\141\x69\x6c\x2e\143\x6f\155", "\145\x78\141\155\x70\154\x65\x2e\143\x6f\x6d", "\145\x78\x61\x6d\160\154\145\x2e\157\162\x67", "\x6d\141\x69\x6c\142\157\x78\56\155\145", "\x69\156\142\157\x78\x2e\x64\145\x76"); for ($i = 0; $i < 10; $i++) { $word1 = $adjectives[random_int(0, count($adjectives) - 1)]; $word2 = $nouns[random_int(0, count($nouns) - 1)]; $num = random_int(10, 9999); $suffix = random_int(0, 1) ? random_int(1, 99) : ''; $domain = $domains[random_int(0, count($domains) - 1)]; $email = strtolower($word1 . "\56" . $word2 . $num . $suffix . "\x40" . $domain); $chk = $pdo->prepare("\x53\105\x4c\105\103\124\40\61\40\x46\x52\x4f\x4d\x20\x6b\x75\x6c\x6c\141\x6e\x69\x63\151\x6c\x61\x72\40\x57\110\105\122\x45\40\x65\160\157\163\164\x61\x20\75\40\77\x20\x4c\111\x4d\x49\x54\40\61"); $chk->execute(array($email)); if (!$chk->fetchColumn()) { return $email; } } return "\x69\x6e\146\157\56" . bin2hex(random_bytes(4)) . "\x40\155\x61\151\154\x2e\143\157\155"; } goto rowm1; obxBF: if (isset($_GET["\x73\x75\x63\143\x65\x73\x73"])) { ?>
<div class="mb-4 alert alert-success"><?php  echo htmlspecialchars($_GET["\x73\165\143\x63\145\163\163"]); ?>
</div><?php  } goto kRSsA; CFpFl: echo $editUser ? htmlspecialchars($editUser["\151\x73\151\155"]) : ''; goto ws1HI; J9p8i: ?>