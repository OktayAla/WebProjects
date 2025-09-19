<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
 goto l3nIA; l3nIA: require_once __DIR__ . "\57\x69\156\143\154\x75\144\x65\163\57\141\x75\x74\x68\56\x70\150\160"; goto fjBx5; RWK1G: require_login(); goto o_OCT; reXG9: ?>
"><?php  goto oXOz3; o_OCT: $pdo = get_pdo_connection(); goto Q82ck; xsRtA: $salesStmt->execute(array($customerId)); goto Uaaj1; dQ11L: ?>
</div></div><?php  goto ZXwsN; QMw2r: $customer = null; goto XAjv1; mYRg1: $stmt->execute(array($id)); goto gvkOt; HRb0D: echo htmlspecialchars($tx["\141\143\151\153\x6c\x61\x6d\141"] ?: "\x41\xc3\xa7\xc4\261\x6b\x6c\141\155\141\40\x62\x75\x6c\x75\156\x6d\x75\171\x6f\162\x2e"); goto dQ11L; utf5R: echo $tx["\157\144\x65\x6d\x65\x5f\x74\x69\160\x69"] === "\x62\157\x72\143" ? "\x42\x4f\122\xc3\207\x20\104\105\113\117\116\124\125" : "\x54\x41\x48\123\xc4\xb0\x4c\101\124\40\104\x45\113\x4f\116\124\x55"; goto a1Lcp; kuB4v: echo number_format($tx["\x6d\151\x6b\x74\141\162"], 2, "\x2c", "\56"); goto U8e3n; PG1gg: echo $tx["\x6f\x64\x65\x6d\145\137\164\x69\x70\x69"] === "\144\145\x62\151\x74" ? "\x62\141\x64\x67\x65\x20\142\x61\144\147\145\x2d\x64\145\142\x69\x74" : "\142\141\x64\147\x65\40\x62\x61\144\147\145\x2d\143\162\x65\x64\x69\164"; goto reXG9; Q82ck: $id = isset($_GET["\x69\x64"]) ? (int) $_GET["\151\x64"] : 0; goto CyqyV; r5PBY: echo $tx["\151\x64"]; goto Ltnzg; fjBx5: require_once __DIR__ . "\x2f\x69\x6e\143\x6c\x75\x64\145\x73\57\155\141\156\165\141\x6c\x5f\x70\162\x6f\x64\x75\143\164\x73\56\160\150\x70"; goto RWK1G; Ltnzg: ?>
| Tarih:<?php  goto tzBdU; H1p10: ?>
</span><div class="stat-info"><span class="stat-label">Net Bakiye</span> <span class="stat-value<?php  goto m35Jo; Lyl_6: $paidStmt->execute(array($customerId)); goto b44YU; XAjv1: if ($customerId) { $stmt = $pdo->prepare("\123\x45\114\105\x43\x54\x20\52\40\106\122\117\x4d\x20\x6d\165\x73\x74\145\162\151\x6c\x65\x72\40\127\110\105\122\105\40\x69\x64\40\x3d\40\77"); $stmt->execute(array($customerId)); $customer = $stmt->fetch(); } goto A8ZxR; KXSOZ: if (!$tx) { die("\113\x61\171\304\xb1\164\x20\142\x75\154\165\156\141\155\x61\144\304\261"); } goto ARWNa; lEwZW: $remaining = $totalPaid - $totalSales; goto gsggm; II9XF: if ($tx["\151\x73\137\155\x61\x69\156\137\164\x72\x61\156\163\141\143\164\x69\x6f\x6e"]) { $productStmt = $pdo->prepare("\xa\x20\40\40\40\x20\40\40\x20\123\x45\114\105\x43\124\40\12\x20\40\40\40\40\x20\40\40\40\40\x20\x20\151\56\52\x2c\40\xa\40\x20\x20\40\40\40\40\40\x20\x20\40\40\x75\x2e\151\x73\x69\155\40\x41\x53\x20\165\162\x75\156\x5f\151\163\x69\x6d\40\xa\x20\40\x20\40\40\x20\40\x20\x46\122\x4f\x4d\40\x69\x73\154\145\x6d\154\x65\162\40\151\x20\xa\x20\x20\40\x20\x20\x20\x20\40\x4c\x45\106\124\x20\112\x4f\x49\116\x20\165\162\165\156\154\145\x72\40\165\x20\x4f\x4e\40\x75\x2e\151\144\40\x3d\40\x69\x2e\x75\162\x75\x6e\x5f\151\x64\40\12\x20\40\40\40\x20\x20\x20\x20\x57\x48\105\122\x45\40\x69\56\x70\x61\162\x65\156\164\x5f\164\162\141\156\163\x61\143\x74\x69\157\156\137\x69\144\x20\75\x20\77\x20\101\116\x44\x20\151\x2e\x69\163\x5f\155\x61\x69\156\137\164\x72\x61\156\x73\x61\143\x74\x69\x6f\x6e\x20\75\x20\x30\12\40\40\x20\x20\x20\40\x20\40\x4f\122\104\105\122\40\102\131\40\151\56\151\x64\x20\101\x53\103\xa\x20\40\x20\40"); $productStmt->execute(array($id)); $products = $productStmt->fetchAll(); } else { $products = array(array("\x75\162\x75\156\137\151\x73\151\x6d" => $tx["\x75\x72\165\156\137\151\x73\x69\x6d"], "\x6d\151\153\164\x61\162" => $tx["\155\151\x6b\x74\x61\x72"], "\x61\143\151\x6b\154\x61\155\141" => $tx["\x61\x63\151\x6b\x6c\141\x6d\141"], "\141\x64\x65\x74" => 1)); } goto FxudF; tzBdU: echo date("\144\56\155\56\131\40\110\x3a\151", strtotime($tx["\157\154\x75\163\164\165\x72\x6d\141\x5f\172\x61\x6d\141\x6e\x69"])); goto mEMZq; aJmw_: ?>
</span></div><div class="flex flex-col md:items-end"><span class="font-medium text-sm text-gray-500">Tutar</span> <span class="font-bold text-2xl"><?php  goto kuB4v; Uaaj1: $totalSales = (double) $salesStmt->fetchColumn(); goto UHM_0; nOv9k: ?>
<div class="receipt-footer"><div class="text-xs text-gray-500">Bu belge<?php  goto XiU27; A8ZxR: $salesStmt = $pdo->prepare("\x53\105\114\105\x43\124\x20\103\117\x41\x4c\105\123\103\x45\50\123\x55\x4d\x28\155\x69\153\x74\141\x72\x29\x2c\x20\60\x29\40\x46\122\117\115\x20\x69\163\154\x65\x6d\x6c\145\162\x20\127\110\x45\122\105\x20\x6d\165\x73\x74\x65\x72\x69\137\151\x64\x20\x3d\40\x3f\x20\101\116\x44\40\157\144\x65\155\x65\137\x74\x69\x70\151\x20\x3d\40\x27\142\157\162\x63\47"); goto xsRtA; b44YU: $totalPaid = (double) $paidStmt->fetchColumn(); goto lEwZW; UHM_0: $paidStmt = $pdo->prepare("\x53\105\x4c\x45\x43\124\40\103\117\101\114\105\123\x43\105\x28\x53\x55\115\x28\155\x69\x6b\x74\x61\162\x29\x2c\40\x30\51\x20\x46\x52\x4f\x4d\40\151\163\154\x65\155\154\x65\x72\40\127\x48\105\122\x45\40\x6d\165\x73\x74\x65\x72\x69\x5f\x69\x64\40\x3d\40\77\40\x41\x4e\104\40\x6f\144\x65\155\x65\x5f\x74\x69\x70\151\x20\75\40\x27\164\141\x68\163\151\x6c\x61\x74\x27"); goto Lyl_6; mEMZq: ?>
</div></div></div><div class="panel"><div class="bg-gray-50 p-4 rounded-lg hidden mb-6 no-print"id="customize-panel"><h5 class="font-semibold mb-3">Dekont Özelleştirme</h5><div class="grid grid-cols-1 gap-4 md:grid-cols-2"><div><label class="font-medium text-sm block mb-1">Şirket Adı</label> <input class="form-input"id="company-name"value="Müşteri Portalı"></div><div><label class="font-medium text-sm block mb-1">Telefon</label> <input class="form-input"id="company-phone"value=""></div><div><label class="font-medium text-sm block mb-1">E-posta</label> <input class="form-input"id="company-email"value=""type="email"></div><div><label class="font-medium text-sm block mb-1">Adres</label> <textarea class="form-input"id="company-address"rows="2"></textarea></div></div><div class="mt-4"><button class="btn btn-primary btn-sm"onclick="applyCustomization()"><i class="bi mr-1 bi-check"></i> Uygula</button> <button class="btn btn-outline btn-sm ml-2"onclick="resetCustomization()"><i class="bi mr-1 bi-arrow-clockwise"></i> Sıfırla</button></div></div><div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4"><div class="card-hover stat-card"><div class="stat-icon"style="background:linear-gradient(135deg,#16a34a 0,#15803d 100%)"><i class="bi bi-cash-coin"></i></div></div><div class="card-hover stat-card"><div class="stat-icon"style="background:linear-gradient(135deg,#f56565 0,#e53e3e 100%)"><i class="bi bi-wallet2"></i></div></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div><div class="space-y-2"><div class="flex flex-col"><span class="font-medium text-sm text-gray-500">Ad Soyad</span> <span class="text-base"><?php  goto KyWCy; gvkOt: $tx = $stmt->fetch(); goto KXSOZ; XiU27: echo date("\x64\56\x6d\56\x59\x20\110\x3a\x69"); goto khnBN; gsggm: $historyStmt = $pdo->prepare("\123\x45\114\x45\x43\x54\x20\151\56\151\x64\x2c\x20\151\56\x6f\x64\x65\155\x65\x5f\164\151\x70\151\x2c\40\151\56\x6d\151\x6b\x74\x61\x72\54\40\x69\x2e\x61\x63\151\x6b\x6c\141\x6d\x61\x2c\x20\151\56\157\x6c\165\x73\x74\x75\x72\155\x61\x5f\x7a\x61\x6d\x61\156\151\x2c\40\165\56\x69\163\151\x6d\x20\x41\123\40\165\162\165\x6e\137\x69\163\151\155\40\106\x52\117\x4d\x20\151\163\x6c\145\x6d\x6c\145\x72\40\x69\x20\114\105\x46\124\x20\x4a\117\111\116\x20\165\162\165\156\x6c\145\x72\x20\x75\x20\117\x4e\40\x75\x2e\x69\144\40\x3d\40\151\x2e\x75\162\x75\156\x5f\x69\144\x20\x57\110\x45\x52\105\x20\151\56\x6d\x75\163\164\x65\162\151\x5f\x69\x64\x20\x3d\40\x3f\40\117\122\x44\x45\122\40\102\131\40\x69\x2e\x6f\x6c\165\163\x74\x75\x72\155\141\137\x7a\141\x6d\141\x6e\151\x20\104\105\123\103"); goto HK_Dr; U8e3n: ?>
₺</span></div></div></div></div><div class="border-gray-200 border-t mt-6 pt-6"><h5 class="text-base font-semibold text-gray-900 mb-2">Açıklama</h5><div class="bg-gray-50 p-4 rounded-lg whitespace-pre-line"><?php  goto HRb0D; IRvRa: ?>
<!doctypehtml><html lang="tr"><head><meta charset="UTF-8"><meta content="width=device-width,initial-scale=1"name="viewport"><meta content="IE=edge"http-equiv="X-UA-Compatible"><title>Yazdır</title><script src="https://cdn.tailwindcss.com"></script><link href="assets/css/print.css"rel="stylesheet"><script>tailwind.config = {
			theme: {
				extend: {
					colors: {
						primary: {
							50: '#eef2ff',
							100: '#e0e7ff',
							200: '#c7d2fe',
							300: '#a5b4fc',
							400: '#818cf8',
							500: '#6366f1',
							600: '#4f46e5',
							700: '#4338ca',
							800: '#3730a3',
							900: '#312e81',
							950: '#1e1b4b',
						},
					}
				}
			}
		}
		function yazdir() {
			setTimeout(function () {
				window.print();
			}, 500);
		}

		function toggleCustomize() {
			const panel = document.getElementById('customize-panel');
			panel.classList.toggle('hidden');
		}

		function applyCustomization() {
			const companyName = document.getElementById('company-name').value;
			const companyPhone = document.getElementById('company-phone').value;
			const companyEmail = document.getElementById('company-email').value;
			const companyAddress = document.getElementById('company-address').value;

			document.getElementById('company-name-display').textContent = companyName;

			const contactInfo = [];
			if (companyPhone) contactInfo.push(`Tel: ${companyPhone}`);
			if (companyEmail) contactInfo.push(`E-posta: ${companyEmail}`);
			if (companyAddress) contactInfo.push(`Adres: ${companyAddress}`);

			document.getElementById('company-contact-display').innerHTML = contactInfo.join('<br>');
			document.getElementById('customize-panel').classList.add('hidden');
		}

		function resetCustomization() {
			document.getElementById('company-name').value = 'Analiz Tarım';
			document.getElementById('company-phone').value = '';
			document.getElementById('company-email').value = '';
			document.getElementById('company-address').value = '';

			applyCustomization();
		}</script></head><body class="bg-gray-50"><div class="container max-w-4xl mx-auto px-4 py-8"><div class="flex items-center justify-between mb-6 no-print"><a class="flex items-center btn space-x-2 btn-primary"href="javascript:yazdir();"><svg class="bi bi-printer"fill="currentColor"height="16"viewBox="0 0 16 16"width="16"xmlns="http://www.w3.org/2000/svg"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg> <span>Yazdır</span> </a><a class="flex items-center btn space-x-2 btn-outline"href="index.php"><svg class="bi bi-arrow-left"fill="currentColor"height="16"viewBox="0 0 16 16"width="16"xmlns="http://www.w3.org/2000/svg"><path d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"fill-rule="evenodd"/></svg> <span>Panele Dön</span></a></div><div class="receipt"><div class="receipt-header"><img alt="Logo"class="receipt-logo"src="img/logo.jpg"><div class="receipt-header-content"><h1 class="receipt-title mb-1"id="company-name-display">ANALİZ TARIM</h1><div class="text-xs mb-2 text-gray-600"id="company-contact-display"></div><h2 class="text-sm mb-2 receipt-title"><?php  goto utf5R; HK_Dr: $historyStmt->execute(array($customerId)); goto Qk51I; ARWNa: $products = array(); goto II9XF; xqPL9: ?>
₺</span></div></div></div></div><div class="md:text-right"><div class="space-y-2"><div class="flex flex-col md:items-end"><span class="font-medium text-sm text-gray-500">Tür</span> <span class="<?php  goto PG1gg; Qk51I: $history = $historyStmt->fetchAll(); goto IRvRa; CyqyV: $stmt = $pdo->prepare("\xa\x20\x20\40\40\40\40\x20\40\x53\x45\114\x45\x43\124\40\xa\40\40\40\40\x20\40\x20\40\x20\40\40\x20\151\x2e\x2a\54\40\12\40\x20\40\40\x20\40\40\40\x20\40\x20\40\x6d\56\151\x73\151\155\40\x41\x53\40\x6d\165\163\x74\145\162\151\137\x69\163\x69\x6d\54\x20\12\x20\40\40\x20\x20\x20\x20\40\40\x20\40\x20\x6d\x2e\156\165\x6d\141\162\x61\x2c\x20\xa\40\x20\x20\x20\40\40\x20\40\40\40\x20\x20\x6d\x2e\x61\144\162\x65\163\x2c\40\xa\x20\40\x20\40\x20\40\x20\x20\x20\40\x20\x20\x75\56\151\x73\151\155\x20\101\123\40\165\162\x75\x6e\x5f\x69\163\151\155\x20\12\40\40\x20\x20\40\x20\40\40\x46\x52\117\115\40\151\163\x6c\145\155\x6c\145\x72\40\x69\40\xa\x20\40\x20\40\40\x20\40\x20\x4a\x4f\x49\116\40\155\165\163\164\x65\x72\151\x6c\x65\x72\x20\155\x20\x4f\116\x20\155\x2e\151\x64\x20\75\40\x69\x2e\x6d\x75\x73\x74\x65\x72\x69\137\151\144\x20\xa\x20\40\40\x20\40\x20\x20\40\114\105\x46\x54\40\x4a\x4f\111\116\x20\x75\x72\165\x6e\x6c\145\162\40\165\40\x4f\x4e\x20\x75\56\x69\144\40\75\40\151\56\165\x72\165\156\x5f\x69\144\40\xa\40\40\40\40\40\x20\40\x20\x57\x48\x45\x52\105\40\x69\56\x69\144\40\x3d\x20\x3f\40\x41\x4e\104\40\x28\151\56\151\163\137\x6d\x61\151\x6e\x5f\x74\x72\141\156\x73\x61\x63\x74\x69\x6f\x6e\x20\x3d\40\x31\40\117\x52\x20\x69\x2e\x69\x73\x5f\x6d\x61\151\x6e\137\x74\x72\x61\156\x73\x61\143\164\x69\157\x6e\x20\111\123\40\x4e\x55\x4c\x4c\51\12\x20\x20\40\x20"); goto mYRg1; oXOz3: echo $tx["\157\x64\x65\155\145\x5f\x74\x69\x70\151"] === "\x64\x65\142\x69\164" ? "\102\157\x72\303\xa7" : "\x54\x61\x68\163\151\x6c\141\x74"; goto aJmw_; T89v2: ?>
"><?php  goto RGt5W; a1Lcp: ?>
</h2><div class="text-xs">Fiş No: #<?php  goto r5PBY; m35Jo: echo $remaining < 0 ? "\x74\145\170\164\x2d\144\141\156\x67\145\x72\55\x36\x30\60" : ($remaining > 0 ? "\x74\x65\x78\164\x2d\x73\x75\x63\x63\x65\x73\x73\55\x36\60\60" : ''); goto T89v2; FxudF: $customerId = isset($_GET["\143\x75\x73\164\x6f\x6d\145\162"]) ? (int) $_GET["\x63\x75\163\x74\x6f\155\x65\162"] : (isset($tx["\x6d\165\163\x74\x65\162\x69\137\151\144"]) ? (int) $tx["\x6d\x75\163\164\145\x72\151\137\151\x64"] : 0); goto QMw2r; RGt5W: echo ($remaining > 0 ? "\53" : '') . number_format($remaining, 2, "\x2c", "\x2e"); goto xqPL9; ZXwsN: if (!empty($products)) { ?>
<div class="border-gray-200 border-t mt-6 pt-6"><h5 class="text-base font-semibold text-gray-900 mb-4">Ürün Detayları</h5><div class="space-y-3"><?php  foreach ($products as $product) { ?>
<div class="bg-gray-50 p-4 rounded-lg border"><div class="grid grid-cols-1 gap-4 md:grid-cols-4"><div><span class="font-medium text-sm text-gray-500">Ürün Adı</span><div class="font-medium text-base"><?php  if ($product["\165\162\x75\156\137\x69\x73\x69\x6d"]) { echo htmlspecialchars($product["\165\162\x75\x6e\x5f\151\163\151\x6d"]); } else { $manualProductName = getManualProduct($product["\151\x64"]); if ($manualProductName) { echo htmlspecialchars($manualProductName); } else { echo "\x4d\x61\156\x75\x65\154\x20\xc3\234\x72\303\xbc\x6e"; } } ?>
</div></div><div><span class="font-medium text-sm text-gray-500">Adet</span><div class="text-base"><?php  echo $product["\x61\144\145\x74"] ?: 1; ?>
</div></div><div><span class="font-medium text-sm text-gray-500">Birim Fiyat</span><div class="text-base"><?php  echo number_format($product["\155\151\153\x74\x61\162"] / ($product["\x61\144\x65\x74"] ?: 1), 2, "\x2c", "\56"); ?>
₺</div></div><div><span class="font-medium text-sm text-gray-500">Toplam</span><div class="text-base font-semibold"><?php  echo number_format($product["\155\x69\x6b\x74\x61\x72"], 2, "\54", "\56"); ?>
₺</div></div></div><?php  if ($product["\x61\x63\x69\x6b\154\141\155\x61"]) { ?>
<div class="mt-2"><span class="font-medium text-sm text-gray-500">Açıklama</span><div class="text-sm text-gray-700"><?php  echo htmlspecialchars($product["\141\143\151\x6b\154\x61\x6d\x61"]); ?>
</div></div><?php  } ?>
</div><?php  } ?>
</div></div><?php  } elseif ($tx["\165\x72\165\156\x5f\151\x73\x69\155"]) { ?>
<div class="border-gray-200 border-t mt-6 pt-6"><h5 class="text-base font-semibold text-gray-900 mb-2">Ürün Bilgisi</h5><div class="p-4 rounded-lg bg-blue-50"><div><span class="font-medium text-sm text-gray-500">Ürün Adı</span><div class="font-medium text-base"><?php  echo htmlspecialchars($tx["\165\x72\165\x6e\137\x69\163\x69\155"]); ?>
</div></div></div></div><?php  } goto nOv9k; KyWCy: echo htmlspecialchars($tx["\155\x75\x73\164\145\162\x69\137\x69\x73\151\x6d"]); goto H1p10; khnBN: ?>
tarihinde oluşturulmuştur.</div></div></div></div></div></body></html>