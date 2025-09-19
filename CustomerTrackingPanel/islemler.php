<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
 goto l5TdF; d5aco: if ($_SERVER["\x52\x45\121\x55\105\123\x54\x5f\x4d\x45\x54\x48\117\104"] === "\120\117\x53\124") { $pdo->beginTransaction(); try { if (isset($_POST["\141\143\164\x69\x6f\x6e"]) && $_POST["\141\143\x74\151\157\x6e"] === "\x75\x70\x64\141\x74\x65\137\164\162\141\x6e\163\141\x63\x74\151\157\x6e" && isset($_POST["\x74\x72\x61\x6e\163\x61\143\164\x69\x6f\156\x5f\151\144"])) { $transaction_id = (int) $_POST["\x74\x72\x61\156\163\x61\x63\x74\151\157\x6e\137\x69\x64"]; $urun_id = !empty($_POST["\x70\162\157\144\x75\x63\x74\x5f\151\144"]) ? (int) $_POST["\160\162\x6f\144\x75\143\164\x5f\151\x64"] : null; $odeme_tipi = $_POST["\164\x79\160\145"]; $miktar = (double) str_replace(array("\54", "\x20"), array("\56", ''), $_POST["\141\x6d\157\165\x6e\x74"]); $aciklama = trim($_POST["\156\x6f\x74\x65"]); $adet = isset($_POST["\x71\165\141\156\x74\151\x74\171"]) ? (int) $_POST["\161\165\141\156\164\151\x74\x79"] : 1; $oldTxStmt = $pdo->prepare("\x53\x45\x4c\x45\x43\x54\x20\151\56\x2a\x2c\x20\x6b\x2e\x69\163\x69\155\x20\101\x53\x20\x6b\165\x6c\154\x61\156\151\143\x69\x5f\x69\x73\x69\155\40\106\122\x4f\x4d\40\151\163\x6c\x65\155\x6c\x65\x72\40\x69\x20\x4c\x45\x46\x54\40\x4a\x4f\x49\x4e\x20\x6b\165\x6c\154\x61\x6e\x69\143\151\x6c\141\x72\x20\x6b\x20\x4f\x4e\x20\153\x2e\x69\x64\x20\x3d\40\x69\56\153\x75\154\x6c\x61\156\x69\x63\151\x5f\151\x64\x20\x57\110\x45\x52\105\x20\151\56\151\144\x20\75\40\77"); $oldTxStmt->execute(array($transaction_id)); $oldTransaction = $oldTxStmt->fetch(); if ($oldTransaction) { if ($oldTransaction["\157\x64\145\155\145\x5f\164\151\160\151"] === "\142\x6f\162\143") { $pdo->prepare("\x55\120\104\x41\124\105\x20\x6d\165\x73\164\x65\x72\151\154\x65\x72\40\123\x45\x54\x20\x74\165\164\x61\162\40\75\x20\164\x75\164\x61\x72\x20\55\40\77\40\127\x48\x45\x52\105\40\151\144\x20\x3d\40\77")->execute(array($oldTransaction["\155\x69\x6b\164\x61\x72"], $oldTransaction["\155\x75\x73\x74\x65\162\x69\137\x69\x64"])); } else { $pdo->prepare("\125\x50\x44\x41\x54\x45\40\x6d\165\163\x74\145\162\x69\x6c\145\x72\x20\x53\105\124\x20\164\x75\164\x61\x72\x20\x3d\40\x74\x75\164\x61\162\x20\x2b\40\77\40\127\x48\x45\x52\105\40\x69\x64\x20\75\x20\77")->execute(array($oldTransaction["\x6d\x69\x6b\164\x61\x72"], $oldTransaction["\x6d\x75\x73\164\145\162\151\x5f\x69\144"])); } $stmt = $pdo->prepare("\125\x50\104\101\124\105\x20\x69\163\154\145\x6d\154\145\x72\x20\123\x45\124\x20\165\x72\x75\156\137\x69\144\40\x3d\x20\77\54\40\157\x64\145\155\x65\137\164\x69\x70\151\x20\75\x20\77\54\40\x6d\x69\x6b\164\141\x72\x20\x3d\40\x3f\54\40\141\x63\151\x6b\x6c\141\x6d\141\x20\75\40\x3f\x20\x57\110\105\122\105\x20\151\x64\x20\75\40\x3f"); $stmt->execute(array($urun_id, $odeme_tipi, $miktar, $aciklama, $transaction_id)); if ($odeme_tipi === "\142\x6f\162\x63") { $pdo->prepare("\x55\120\104\101\124\x45\x20\155\x75\x73\164\145\x72\x69\x6c\x65\162\40\x53\x45\124\x20\164\165\x74\x61\x72\40\75\40\x74\165\x74\x61\x72\x20\x2b\x20\77\40\x57\x48\x45\122\x45\40\x69\x64\40\75\40\77")->execute(array($miktar, $oldTransaction["\155\x75\x73\164\x65\x72\151\137\151\144"])); } else { $pdo->prepare("\x55\120\x44\x41\x54\x45\40\155\165\x73\164\x65\162\x69\154\145\162\x20\x53\105\x54\40\164\x75\164\141\162\40\75\40\164\x75\x74\141\162\x20\x2d\40\x3f\40\127\x48\x45\x52\105\x20\151\x64\x20\75\40\x3f")->execute(array($miktar, $oldTransaction["\x6d\x75\163\x74\x65\162\151\x5f\x69\144"])); } } $pdo->commit(); $redirect_url = "\151\x73\154\145\x6d\154\145\162\x2e\160\x68\160"; $query_params = array(); if ($customerId) { $query_params["\143\165\163\x74\x6f\155\145\x72"] = $customerId; } if (isset($_GET["\160\141\x67\145"])) { $query_params["\x70\141\x67\145"] = $_GET["\x70\141\x67\x65"]; } $query_params["\163\165\143\143\145\163\163"] = "\x32"; $redirect_url .= "\x3f" . http_build_query($query_params); header("\114\157\143\141\164\151\x6f\156\72\40" . $redirect_url); die; } else { $musteri_id = (int) $_POST["\x63\165\163\164\157\155\x65\x72\137\x69\x64"]; if ($musteri_id === 0 && !empty($_POST["\x6e\145\167\137\x63\x75\163\x74\x6f\x6d\145\x72\x5f\156\141\155\x65"])) { $newCustomerName = trim($_POST["\x6e\145\167\137\x63\165\163\164\157\x6d\x65\x72\x5f\156\141\x6d\x65"]); $newCustomerPhone = trim($_POST["\x6e\145\167\137\x63\165\163\x74\157\155\145\162\x5f\160\150\x6f\156\x65"] ?? ''); $stmt = $pdo->prepare("\111\116\x53\x45\x52\124\40\x49\x4e\124\x4f\x20\x6d\x75\163\164\x65\x72\151\x6c\x65\x72\x20\x28\151\x73\151\155\x2c\x20\156\x75\x6d\141\x72\141\54\40\x74\x75\x74\x61\162\51\40\126\101\x4c\x55\105\x53\x20\50\x3f\x2c\x20\77\x2c\x20\x30\x29"); $stmt->execute(array($newCustomerName, $newCustomerPhone)); $musteri_id = $pdo->lastInsertId(); } $products = $_POST["\160\162\x6f\144\x75\x63\164\163"] ?? array(); $totalDebit = 0; $totalCredit = 0; $mainTransactionId = null; $transactionDescription = ''; if (!empty($products)) { $firstProduct = $products[0]; $odeme_tipi = $firstProduct["\x74\171\x70\145"]; $productCount = count($products); $transactionDescription = $productCount > 1 ? "\303\207\157\153\x6c\x75\x20\303\xbc\162\xc3\274\156\x20\151\305\237\x6c\x65\x6d\151\40\x28{$productCount}\x20\153\x61\154\x65\155\x29" : "\x54\145\x6b\x20\303\xbc\162\xc3\xbc\156\x20\151\xc5\237\x6c\x65\x6d\151"; try { $stmt = $pdo->prepare("\111\116\x53\105\x52\124\40\111\116\x54\117\x20\x69\163\x6c\x65\x6d\x6c\145\162\x20\50\x6d\x75\163\164\145\162\x69\x5f\x69\144\54\40\x75\162\x75\x6e\x5f\x69\144\54\40\x6f\144\145\x6d\x65\x5f\164\151\x70\151\x2c\x20\x6d\151\x6b\x74\141\162\54\x20\141\x63\x69\153\x6c\x61\x6d\x61\54\40\x6b\x75\154\x6c\141\x6e\x69\x63\151\x5f\x69\x64\x2c\40\x69\163\137\x6d\141\x69\x6e\137\x74\x72\x61\156\163\141\x63\x74\151\157\x6e\51\x20\126\x41\x4c\x55\105\123\x20\x28\x3f\x2c\x20\77\x2c\40\77\54\40\x3f\54\x20\x3f\54\x20\77\54\x20\61\51"); $stmt->execute(array($musteri_id, null, $odeme_tipi, 0, $transactionDescription, $currentUserId)); } catch (Exception $e) { if (strpos($e->getMessage(), "\151\x73\137\x6d\141\x69\x6e\x5f\164\x72\x61\156\x73\141\x63\x74\x69\x6f\x6e") !== false) { $stmt = $pdo->prepare("\x49\x4e\x53\105\x52\x54\x20\x49\116\124\x4f\x20\x69\163\x6c\145\x6d\x6c\145\162\40\x28\155\x75\163\x74\145\x72\151\137\151\x64\54\x20\x75\x72\165\156\x5f\151\x64\x2c\40\157\144\145\155\145\x5f\164\151\160\x69\54\x20\x6d\151\x6b\x74\x61\162\54\40\x61\x63\151\153\154\x61\155\x61\x2c\40\x6b\165\154\154\x61\x6e\x69\143\x69\x5f\151\144\51\40\126\x41\x4c\125\x45\123\40\x28\77\54\40\77\54\x20\77\54\x20\77\54\40\77\x2c\x20\77\x29"); $stmt->execute(array($musteri_id, null, $odeme_tipi, 0, $transactionDescription, $currentUserId)); } else { throw $e; } } $mainTransactionId = $pdo->lastInsertId(); } foreach ($products as $index => $productData) { if (!empty($productData["\141\155\157\165\156\x74"])) { $urun_id = !empty($productData["\160\x72\157\x64\x75\143\x74\x5f\151\144"]) ? (int) $productData["\x70\x72\157\144\x75\x63\164\137\x69\x64"] : null; $odeme_tipi = $productData["\164\171\160\145"]; $miktar = (double) str_replace(array("\x2c", "\x20"), array("\56", ''), $productData["\x61\x6d\x6f\x75\x6e\x74"]); $adet = isset($productData["\x71\x75\141\156\164\x69\164\x79"]) ? (int) $productData["\x71\165\x61\x6e\164\151\164\x79"] : 1; $urun_notu = !empty($productData["\156\x6f\164\x65"]) ? trim($productData["\x6e\157\164\145"]) : ''; if ($urun_id === 0 && !empty($productData["\156\145\167\137\160\162\x6f\x64\x75\x63\164\x5f\156\x61\x6d\x65"])) { $newProductName = trim($productData["\156\145\x77\x5f\x70\162\x6f\144\x75\x63\x74\137\156\x61\x6d\x65"]); $urun_id = null; } try { $stmt = $pdo->prepare("\111\x4e\x53\105\x52\x54\40\x49\116\x54\x4f\40\151\x73\x6c\x65\155\x6c\145\162\x20\x28\x6d\165\163\x74\x65\x72\151\x5f\x69\144\x2c\40\165\162\165\156\137\151\x64\x2c\x20\x6f\144\x65\x6d\145\137\x74\151\x70\x69\x2c\40\x6d\x69\153\164\141\162\54\40\x61\143\x69\x6b\x6c\141\155\141\54\40\153\x75\154\x6c\141\x6e\151\143\151\137\x69\144\x2c\x20\160\141\162\145\156\164\x5f\x74\x72\141\156\x73\141\x63\x74\x69\157\x6e\137\x69\x64\x2c\40\151\163\137\x6d\x61\x69\x6e\x5f\x74\x72\141\156\163\x61\x63\164\x69\x6f\x6e\x2c\40\x61\x64\145\x74\x29\40\x56\101\x4c\x55\x45\x53\40\x28\77\x2c\40\77\54\40\77\x2c\40\77\54\x20\x3f\x2c\x20\x3f\54\x20\x3f\54\x20\x30\54\40\77\51"); $stmt->execute(array($musteri_id, $urun_id, $odeme_tipi, $miktar, $urun_notu, $currentUserId, $mainTransactionId, $adet)); $transactionId = $pdo->lastInsertId(); if ($urun_id === null && !empty($productData["\x6e\145\167\137\160\x72\x6f\x64\x75\x63\x74\137\156\x61\155\x65"])) { saveManualProduct($transactionId, $productData["\156\145\167\x5f\160\x72\157\x64\165\x63\164\x5f\x6e\141\155\x65"]); } } catch (Exception $e) { if (strpos($e->getMessage(), "\x61\144\x65\164") !== false || strpos($e->getMessage(), "\160\141\x72\x65\156\164\x5f\x74\x72\141\x6e\x73\141\143\x74\x69\x6f\156\x5f\x69\144") !== false) { $stmt = $pdo->prepare("\111\116\x53\105\122\x54\x20\x49\x4e\x54\117\40\151\163\x6c\x65\155\154\145\x72\x20\50\x6d\165\163\x74\145\162\151\x5f\151\x64\54\40\165\162\165\x6e\137\151\144\54\40\157\x64\145\x6d\145\x5f\164\x69\160\x69\x2c\40\155\x69\x6b\x74\x61\162\54\40\141\143\x69\153\x6c\141\155\141\54\x20\x6b\x75\154\x6c\141\156\x69\143\x69\x5f\151\144\51\x20\126\x41\114\125\x45\123\40\x28\77\54\40\x3f\x2c\x20\x3f\54\40\x3f\54\x20\x3f\54\x20\77\51"); $stmt->execute(array($musteri_id, $urun_id, $odeme_tipi, $miktar, $urun_notu, $currentUserId)); $transactionId = $pdo->lastInsertId(); if ($urun_id === null && !empty($productData["\156\145\x77\x5f\x70\162\x6f\x64\165\143\x74\x5f\156\141\155\145"])) { saveManualProduct($transactionId, $productData["\156\x65\167\x5f\x70\x72\157\144\165\x63\164\x5f\x6e\x61\155\145"]); } } else { throw $e; } } if ($odeme_tipi === "\142\157\x72\143") { $totalDebit += $miktar; } else { $totalCredit += $miktar; } } } if ($mainTransactionId) { $netAmount = $totalDebit - $totalCredit; $stmt = $pdo->prepare("\125\120\104\101\124\105\40\x69\x73\x6c\145\x6d\x6c\x65\162\40\123\x45\124\40\x6d\x69\x6b\x74\141\x72\x20\75\x20\x3f\40\127\x48\105\122\x45\40\151\x64\40\x3d\40\77"); $stmt->execute(array($netAmount, $mainTransactionId)); } $netAmount = $totalDebit - $totalCredit; if ($netAmount != 0) { $pdo->prepare("\125\x50\x44\101\124\105\40\x6d\165\x73\164\145\x72\151\154\145\x72\x20\123\x45\124\40\164\165\164\141\162\x20\75\40\164\x75\164\141\162\40\53\x20\x3f\40\x57\110\x45\122\105\40\x69\144\x20\75\40\77")->execute(array($netAmount, $musteri_id)); } $pdo->commit(); $redirect_url = "\x69\x73\154\145\x6d\x6c\x65\162\x2e\160\150\160"; $query_params = array(); if ($customerId) { $query_params["\143\x75\163\164\x6f\x6d\145\x72"] = $customerId; } $query_params["\x73\165\x63\143\x65\x73\163"] = "\61"; $redirect_url .= "\77" . http_build_query($query_params); header("\x4c\x6f\143\141\x74\151\157\x6e\x3a\x20" . $redirect_url); die; } } catch (Exception $e) { $pdo->rollBack(); $error = "\xc4\260\305\x9f\154\145\155\x20\142\141\xc5\x9f\141\162\xc4\261\163\304\261\x7a\72\40" . $e->getMessage(); } } goto Hn9A3; hqCU6: ?>
</div></div><?php  goto ROev7; Wt7mX: $stmt->execute(); goto VqmJD; FT9RH: if (!empty($conditions)) { $sql .= "\x20\127\110\105\122\105\40\x28\x69\56\x69\x73\137\x6d\x61\x69\156\x5f\x74\162\141\156\163\x61\x63\164\151\157\156\40\x3d\x20\61\x20\117\x52\x20\151\56\x69\163\x5f\155\141\x69\156\x5f\x74\162\x61\156\x73\141\143\x74\151\x6f\156\40\x49\123\x20\116\x55\114\114\51\40\101\x4e\104\x20" . implode("\40\x41\x4e\x44\x20", $conditions); } else { $sql .= "\40\127\110\x45\122\x45\x20\50\151\x2e\x69\163\x5f\155\141\x69\x6e\x5f\x74\162\x61\x6e\x73\141\x63\164\x69\157\156\x20\x3d\x20\x31\x20\x4f\122\40\151\56\151\x73\137\155\141\151\x6e\137\x74\162\141\156\x73\x61\143\x74\151\x6f\156\40\111\123\40\116\x55\114\114\x29"; } goto kQLJa; wAE07: $currentUserName = "\123\151\163\164\x65\155"; goto rM3uI; dk6PQ: ?>
>Borç</option><option value="tahsilat"<?php  goto Mc4RW; UD_tS: ?>
</select></div><div class="form-group"><label class="form-label"for="type">İşlem Türü</label> <select class="form-select"id="type"><option value="">Tümü</option><option value="borc"<?php  goto X_YBt; NKfJB: $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT); goto Wt7mX; mK2H8: if ($selectedCustomer) { ?>
<input name="customer_id"type="hidden"value="<?php  echo $customerId; ?>
"> <input class="form-input bg-gray-100"value="<?php  echo htmlspecialchars($selectedCustomer["\151\x73\x69\x6d"]); ?>
"readonly><?php  } else { ?>
<div class="relative"><input class="form-input"id="customer-search"placeholder="Müşteri ara veya yeni müşteri adı yazın..."autocomplete="off"autofocus> <input name="customer_id"type="hidden"id="customer_id"required> <input name="new_customer_name"type="hidden"id="new_customer_name"> <input name="new_customer_phone"type="hidden"id="new_customer_phone"><div class="bg-white border border-gray-300 absolute hidden max-h-60 overflow-y-auto rounded-md shadow-lg w-full z-10"id="customer-suggestions"></div><div class="text-sm text-gray-600 hidden mt-2"id="customer-balance"></div></div><?php  } goto QihyR; S40nZ: $customerId = isset($_GET["\143\x75\163\164\157\155\145\x72"]) ? (int) $_GET["\143\165\163\x74\157\x6d\145\162"] : 0; goto r3UiU; Ovo1y: $selectedCustomer = null; goto l4Lvl; V4wep: $sql = "\123\x45\x4c\x45\x43\124\40\x69\56\x2a\54\x20\x6d\56\x69\163\151\155\x20\x41\123\40\x6d\165\163\164\x65\162\x69\137\151\x73\151\x6d\x2c\x20\x75\56\151\x73\151\x6d\40\x41\123\40\x75\162\165\156\x5f\151\x73\x69\x6d\54\x20\x6b\56\151\163\151\155\40\101\x53\x20\x6b\165\154\x6c\141\156\x69\x63\151\x5f\x69\x73\151\155\54\xa\x20\x20\x20\x20\40\x20\40\40\40\x20\40\x20\x20\40\x20\x28\123\x45\x4c\105\x43\x54\x20\x43\117\x55\116\x54\50\x2a\x29\x20\106\x52\x4f\115\x20\151\163\x6c\x65\x6d\x6c\145\162\40\163\165\x62\40\x57\x48\105\122\105\40\x73\165\x62\56\160\141\x72\x65\x6e\x74\137\164\162\141\156\163\x61\143\164\x69\157\x6e\137\x69\144\x20\x3d\x20\151\x2e\x69\x64\51\40\x61\163\x20\160\x72\x6f\x64\165\x63\164\137\143\x6f\165\156\164\xa\x20\x20\40\40\x20\x20\40\x46\122\117\x4d\x20\151\163\x6c\145\x6d\154\x65\x72\40\151\12\x20\40\40\x20\40\40\40\x4a\x4f\x49\116\x20\x6d\165\163\164\145\162\151\154\x65\162\40\x6d\40\x4f\x4e\40\155\x2e\151\144\40\75\40\x69\56\x6d\x75\x73\164\x65\162\151\x5f\151\144\12\x20\x20\x20\x20\40\40\40\114\x45\106\x54\40\x4a\x4f\x49\116\x20\x75\162\165\156\x6c\145\x72\x20\x75\x20\117\116\x20\165\x2e\151\144\x20\x3d\40\151\x2e\x75\x72\165\156\137\x69\x64\12\40\x20\40\x20\x20\x20\x20\114\x45\106\x54\x20\x4a\117\111\x4e\40\153\165\154\x6c\x61\156\151\143\151\x6c\141\x72\x20\x6b\40\117\x4e\40\153\x2e\151\x64\40\75\40\151\56\153\165\x6c\x6c\x61\x6e\x69\143\151\137\151\144"; goto FT9RH; l5TdF: require_once __DIR__ . "\x2f\x69\x6e\x63\154\165\x64\x65\x73\x2f\x61\x75\x74\x68\x2e\x70\x68\x70"; goto aSQsq; Tm_Ac: if ($dateTo) { $conditions[] = "\151\56\x6f\154\x75\163\x74\165\162\x6d\141\x5f\172\141\155\141\x6e\x69\x20\74\75\40\x3f"; $params[] = $dateTo . "\x20\x32\x33\72\65\71\x3a\65\71"; } goto ZB0Sg; ZC3LI: $dateFrom = isset($_GET["\144\141\x74\x65\137\x66\162\157\x6d"]) ? $_GET["\144\x61\164\145\x5f\146\162\x6f\155"] : ''; goto u3fb3; kJbWi: ?>
>Tahsilat</option></select></div><div class="grid gap-2 grid-cols-2"><div class="form-group"><label class="form-label"for="date_from">Başlangıç Tarihi</label> <input class="form-input"value="<?php  goto w_M1H; Wje_A: $countStmt = $pdo->prepare($countSql); goto Q4tcs; rEojT: $totalPages = max(1, ceil($totalRows / $perPage)); goto V4wep; xTPxd: $paramIndex = 1; goto q9EiU; V6UjN: $totalRows = (int) $countStmt->fetchColumn(); goto rEojT; uUi6W: ?>
</div><div class="animate-fadeIn card-hover shadow-lg mb-6"><div class="card-header"><h3 class="flex items-center card-title"><i class="bi mr-2 text-primary-600 bi-plus-circle"></i> Yeni İşlem Ekle</h3></div><div class="p-5"><form method="POST"class="gap-4 grid grid-cols-1"id="transactionForm"><div class="gap-4 grid grid-cols-1 md:grid-cols-12"><div class="col-span-1 md:col-span-4"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-person"></i> Müşteri</label><?php  goto mK2H8; ou2EF: if ($dateFrom) { $conditions[] = "\151\56\x6f\154\165\x73\x74\165\x72\155\x61\137\x7a\141\x6d\x61\x6e\x69\40\76\x3d\x20\x3f"; $params[] = $dateFrom . "\40\x30\60\x3a\60\60\x3a\x30\60"; } goto Tm_Ac; kQLJa: $sql .= "\40\x4f\x52\104\105\122\40\x42\x59\40\x69\56\x6f\154\x75\163\164\165\162\x6d\x61\137\172\141\x6d\141\156\151\40\x44\x45\x53\103\40\114\111\115\111\124\x20\x3f\40\117\x46\x46\123\x45\x54\x20\x3f"; goto O7MEO; ee2SM: if ($typeFilter) { $conditions[] = "\x69\x2e\157\144\145\x6d\145\137\x74\151\160\x69\40\75\40\77"; $params[] = $typeFilter; } goto ou2EF; rBaHp: if ($customerId) { ?>
<input id="customer-filter"type="hidden"value="<?php  echo $customerId; ?>
"><?php  } goto suU9s; u3fb3: $dateTo = isset($_GET["\x64\141\164\145\x5f\164\x6f"]) ? $_GET["\144\141\164\145\x5f\x74\157"] : ''; goto QY9kf; kg0S2: $countSql = "\x53\x45\114\x45\103\124\40\103\117\125\x4e\x54\50\52\51\40\x46\122\x4f\115\40\x69\163\x6c\145\155\154\x65\162\x20\x69\x20\112\117\111\x4e\40\x6d\x75\x73\x74\145\x72\x69\154\145\162\x20\155\x20\x4f\x4e\40\x6d\x2e\151\x64\x20\75\x20\x69\x2e\155\165\163\x74\145\162\151\x5f\x69\x64\x20"; goto Nrm3d; Hn9A3: require_once __DIR__ . "\x2f\151\x6e\x63\154\x75\x64\145\163\57\x68\x65\141\x64\145\162\56\x70\x68\160"; goto PREnF; aSQsq: require_once __DIR__ . "\57\151\156\143\x6c\x75\x64\145\163\57\155\x61\156\x75\x61\x6c\x5f\160\162\157\x64\165\x63\164\x73\56\x70\150\x70"; goto RT1jV; nwq3Q: if ($customerId && $selectedCustomer) { echo htmlspecialchars($selectedCustomer["\151\163\151\155"]); ?>
- İşlem Geçmişi<?php  } else { ?>
Son İşlemler<?php  } goto c0x9B; EEXRZ: ?>
</div></div><div class="p-0"><div class="table-container"><div id="transactions-table-container"><table class="table table-hover"><thead><tr><?php  goto NeS1x; N8SQg: if (isset($error)) { ?>
<div class="p-4 border-l-4 mb-6 rounded shadow-sm bg-red-100 border-red-500 text-red-700"role="alert"><div class="flex items-center"><div class="py-1"><i class="bi mr-3 bi-exclamation-triangle-fill text-red-500"></i></div><div><p class="font-medium">Hata!</p><p class="text-sm"><?php  echo htmlspecialchars($error); ?>
</p></div></div></div><?php  } goto uJp1r; I_99R: ?>
<div class="px-4 container mx-auto py-6"><?php  goto yliMd; Qq8y1: $productFilter = isset($_GET["\x70\x72\x6f\144\165\x63\164"]) ? (int) $_GET["\160\162\x6f\x64\x75\x63\164"] : 0; goto iHw78; IfaQR: echo htmlspecialchars($search); goto oRhV3; L3_cY: $editTransaction = null; goto PH5K4; v7RL_: if ($selectedCustomer) { ?>
<a class="flex items-center btn btn-secondary"href="musteri_rapor.php?customer=<?php  echo $customerId; ?>
"><i class="bi mr-2 bi-file-earmark-bar-graph"></i> Müşteri Raporu </a><?php  } goto uUi6W; Y28FM: if (!$hasTransactions) { ?>
<tr><td class="py-12 text-center text-gray-500"colspan="<?php  echo $customerId ? "\x37" : "\70"; ?>
"><div class="flex items-center justify-center flex-col gap-3"><div class="bg-gray-100 rounded-full mb-2 p-4"><i class="bi text-primary-500 bi-receipt text-5xl"></i></div><h4 class="font-medium text-lg">Henüz işlem bulunmuyor</h4><p class="text-sm max-w-md text-gray-400"><?php  if ($customerId) { ?>
Bu müşteri için henüz bir işlem kaydı oluşturulmamış. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.<?php  } else { ?>
Sistemde henüz bir işlem kaydı bulunmuyor. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.<?php  } ?>
</p></div></td></tr><?php  } goto jUxQP; ROev7: if ($editTransaction) { ?>
<div class="flex items-center justify-center backdrop-blur-sm bg-black bg-opacity-50 fixed inset-0 z-50"id="editTransactionModal"><div class="animate-fadeIn bg-white max-w-2xl mx-4 rounded-lg shadow-xl w-full"><div class="flex items-center justify-between border-b border-gray-200 p-4"><h3 class="flex items-center font-semibold text-gray-900 text-lg"><i class="bi mr-2 text-primary-600 bi-pencil-square"></i> İşlem Düzenle</h3><button class="text-gray-400 close-modal hover:text-gray-500"type="button"><i class="bi bi-x-lg"></i></button></div><form method="POST"action=""><div class="p-4"><input name="transaction_id"type="hidden"value="<?php  echo (int) $editTransaction["\x69\x64"]; ?>
"> <input name="action"type="hidden"value="update_transaction"><div class="gap-4 grid grid-cols-1 md:grid-cols-2 mb-4"><div><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-person"></i> Müşteri</label> <input class="form-input bg-gray-100"value="<?php  echo htmlspecialchars($editTransaction["\x6d\165\x73\164\145\162\151\x5f\x69\x73\x69\x6d"]); ?>
"readonly></div><div><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-box-seam"></i> Ürün</label> <select class="form-select"name="product_id"><option value="">Ürün Seçiniz (Opsiyonel)</option><?php  foreach ($products as $product) { ?>
<option value="<?php  echo $product["\x69\144"]; ?>
"<?php  echo $editTransaction && $editTransaction["\x75\162\x75\156\137\151\x64"] == $product["\x69\144"] ? "\163\145\154\x65\143\x74\x65\x64" : ''; ?>
><?php  echo htmlspecialchars($product["\151\163\151\x6d"]); ?>
</option><?php  } ?>
</select></div><div><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-arrow-left-right"></i> İşlem Türü</label> <select class="form-select"name="type"><option value="borc"<?php  echo $editTransaction && $editTransaction["\x6f\144\x65\155\x65\137\x74\x69\x70\151"] === "\x62\x6f\162\x63" ? "\x73\x65\154\x65\x63\x74\x65\144" : ''; ?>
>Borç</option><option value="tahsilat"<?php  echo $editTransaction && $editTransaction["\157\144\145\x6d\145\x5f\164\151\160\151"] === "\x74\141\x68\163\151\x6c\x61\x74" ? "\163\145\154\x65\x63\164\x65\144" : ''; ?>
>Tahsilat</option></select></div><div><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-person-fill"></i> İşlemi Ekleyen</label> <input class="form-input bg-gray-100"value="<?php  echo isset($editTransaction["\153\x75\x6c\154\x61\156\x69\143\151\137\x69\163\x69\x6d"]) && !empty($editTransaction["\153\165\154\154\141\156\x69\x63\x69\137\151\163\151\155"]) ? htmlspecialchars($editTransaction["\x6b\x75\154\154\x61\156\x69\143\151\x5f\x69\x73\x69\155"]) : "\123\151\x73\x74\145\x6d"; ?>
"readonly></div><div><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-currency-exchange"></i> Tutar (₺)</label> <input class="form-input"value="<?php  echo $editTransaction ? number_format($editTransaction["\x6d\x69\x6b\x74\141\162"], 2, "\x2c", "\x2e") : ''; ?>
"name="amount"placeholder="0,00"required></div><div class="md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-chat-left-text"></i> Açıklama</label> <input class="form-input"value="<?php  echo $editTransaction ? htmlspecialchars($editTransaction["\x61\143\x69\x6b\x6c\x61\x6d\141"]) : ''; ?>
"name="note"placeholder="İşlem açıklaması"></div></div></div><div class="flex gap-2 justify-end border-gray-200 border-t px-4 py-3"><button class="btn btn-outline close-modal"type="button"><i class="bi mr-1 bi-x-circle"></i> İptal</button> <button class="btn btn-primary"type="submit"><i class="bi mr-1 bi-check-circle"></i> Kaydet</button></div></form></div></div><?php  } goto lo3QV; PH5K4: if (isset($_GET["\145\144\x69\164"])) { $stmt = $pdo->prepare("\123\105\x4c\x45\x43\x54\40\x69\56\x2a\x2c\x20\x6d\56\x69\x73\151\x6d\40\x61\163\40\x6d\165\163\x74\x65\162\151\x5f\151\x73\x69\x6d\x2c\40\x6b\56\151\163\151\x6d\x20\x61\163\40\153\165\154\154\x61\x6e\151\x63\151\x5f\151\x73\x69\155\x20\106\122\x4f\x4d\x20\151\163\154\x65\155\x6c\145\162\x20\151\40\112\x4f\x49\116\x20\x6d\165\x73\x74\x65\162\151\154\145\162\x20\155\x20\117\x4e\40\151\x2e\155\x75\x73\x74\x65\162\151\x5f\x69\x64\40\x3d\x20\x6d\x2e\x69\x64\40\x4c\x45\106\124\40\x4a\117\x49\x4e\40\153\x75\154\154\141\156\151\x63\151\154\x61\x72\40\x6b\x20\x4f\x4e\40\151\56\x6b\x75\x6c\x6c\141\x6e\x69\143\x69\x5f\x69\144\40\x3d\x20\153\56\x69\144\40\x57\110\x45\122\105\40\x69\56\151\144\x20\75\40\x3f"); $stmt->execute(array((int) $_GET["\145\x64\x69\x74"])); $editTransaction = $stmt->fetch(); } goto O8PdW; X_YBt: echo $typeFilter === "\142\157\x72\x63" ? "\x73\x65\x6c\x65\x63\x74\145\144" : ''; goto dk6PQ; O7MEO: $stmt = $pdo->prepare($sql); goto xTPxd; Y78n3: $index = 0; goto N2jAp; S2W5z: require_once __DIR__ . "\x2f\151\x6e\x63\154\x75\144\145\163\57\146\157\x6f\x74\x65\x72\56\x70\x68\160"; goto si70v; m6sN7: $params = array(); goto A16NZ; lo3QV: ?>
<script>document.addEventListener('DOMContentLoaded', function () {
        // Çoklu ürün yönetimi
        let productCounter = 1;
        const productsContainer = document.getElementById('products-container');
        const addProductButton = document.getElementById('add-product');

        if (addProductButton && productsContainer) {
            addProductButton.addEventListener('click', function () {
                const newRow = document.createElement('div');
                newRow.className = 'product-row grid grid-cols-1 md:grid-cols-12 gap-4 mb-3';
                newRow.innerHTML = `
                <div class="md:col-span-4 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                    </label>
                    <div class="relative">
                        <input type="text" class="form-input product-search" placeholder="Ürün ara veya yeni ürün adı yazın..." autocomplete="off">
                        <input type="hidden" name="products[${productCounter}][product_id]" class="product-id" required>
                        <input type="hidden" name="products[${productCounter}][new_product_name]" class="new-product-name">
                        <div class="product-suggestions absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                    </label>
                    <select name="products[${productCounter}][type]" class="form-select type-select">
                        <option value="borc">Borç</option>
                        <option value="tahsilat">Tahsilat</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-123 mr-2 text-primary-500"></i> Adet
                    </label>
                    <input type="number" name="products[${productCounter}][quantity]" class="form-input quantity-input" value="1" min="1" required>
                </div>

                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Birim Fiyat (₺)
                    </label>
                    <input type="text" name="products[${productCounter}][unit_price]" class="form-input unit-price-input" placeholder="0,00" required>
                </div>

                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-calculator mr-2 text-primary-500"></i> Toplam (₺)
                    </label>
                    <input type="text" name="products[${productCounter}][amount]" class="form-input amount-input" placeholder="0,00" readonly>
                </div>

                <div class="md:col-span-1 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Not
                    </label>
                    <input type="text" name="products[${productCounter}][note]" class="form-input" placeholder="Not">
                </div>
                
                <div class="md:col-span-1 col-span-1 flex items-end">
                    <button type="button" class="btn btn-outline text-red-500 remove-product" title="Kaldır">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

                productsContainer.appendChild(newRow);
                // Yeni eklenen ürün arama alanını etkinleştir
                const newlyAddedSearch = newRow.querySelector('.product-search');
                if (newlyAddedSearch) {
                    setupProductSearch(newlyAddedSearch);
                }
                productCounter++;

                // Yeni eklenen satırdaki input alanlarını ayarla
                setupAmountInputs();
                // Tüm remove butonlarını güncelle
                updateRemoveButtons();
            });
        }

        function updateRemoveButtons() {
            const removeButtons = document.querySelectorAll('.remove-product');
            const productRows = document.querySelectorAll('.product-row');

            removeButtons.forEach((button, index) => {
                // İlk satırdaki remove butonunu gizle, diğerlerini göster
                if (index === 0 && productRows.length === 1) {
                    button.classList.add('hidden');
                } else {
                    button.classList.remove('hidden');
                }

                button.onclick = function () {
                    if (productRows.length > 1) {
                        button.closest('.product-row').remove();
                        updateRemoveButtons();
                    }
                };
            });
        }

        // Tutar alanları için para formatı ve hesaplama
        function setupAmountInputs() {
            const unitPriceInputs = document.querySelectorAll('.unit-price-input');
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const amountInputs = document.querySelectorAll('.amount-input');

            // Birim fiyat formatı
            unitPriceInputs.forEach(input => {
                // Önce mevcut event listener'ları temizle
                input.removeEventListener('input', input._inputHandler);
                
                // Yeni event handler oluştur
                input._inputHandler = function (e) {
                    let value = e.target.value.replace(/[^\d,]/g, '');
                    value = value.replace(',', '.');
                    if (value.includes('.')) {
                        const parts = value.split('.');
                        if (parts[1] && parts[1].length > 2) {
                            parts[1] = parts[1].substring(0, 2);
                        }
                        value = parts.join('.');
                    }
                    e.target.value = value;
                    calculateTotal(e.target);
                };
                
                input.addEventListener('input', input._inputHandler);
            });

            // Adet değişikliği
            quantityInputs.forEach(input => {
                // Önce mevcut event listener'ları temizle
                input.removeEventListener('input', input._quantityHandler);
                
                // Yeni event handler oluştur
                input._quantityHandler = function (e) {
                    calculateTotal(e.target);
                };
                
                input.addEventListener('input', input._quantityHandler);
            });

            // Toplam hesaplama fonksiyonu
            function calculateTotal(input) {
                const row = input.closest('.product-row');
                if (!row) return;

                const unitPriceInput = row.querySelector('.unit-price-input');
                const quantityInput = row.querySelector('.quantity-input');
                const amountInput = row.querySelector('.amount-input');

                if (!unitPriceInput || !quantityInput || !amountInput) return;

                const unitPrice = parseFloat(unitPriceInput.value.replace(',', '.')) || 0;
                const quantity = parseInt(quantityInput.value) || 1;
                const total = unitPrice * quantity;

                amountInput.value = total.toFixed(2).replace('.', ',');
            }
        }

        // Form gönderildiğinde buton durumunu değiştir
        const form = document.getElementById('transactionForm');
        if (form) {
            form.addEventListener('submit', function () {
                const submitButton = document.getElementById('submitButton');
                if (submitButton) {
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> İşleniyor...';
                    submitButton.disabled = true;
                }
            });
        }

        // Modal işlemleri
        const modal = document.getElementById('editTransactionModal');
        if (modal) {
            const closeButtons = document.querySelectorAll('.close-modal');

            const closeModal = () => {
                modal.classList.add('hidden');
                // URL'yi temizle
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.replaceState({}, document.title, url);
            };

            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });

            // Modal dışına tıklandığında kapat
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Escape tuşu ile kapat
            document.addEventListener('keydown', function (e) {
                if (e.key === "Escape" && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        }

        // İlk yüklemede remove butonlarını güncelle ve amount inputlarını ayarla
        updateRemoveButtons();
        setupAmountInputs();
        
        // İlk satır için de event listener'ları ayarla
        document.querySelectorAll('.product-search').forEach(setupProductSearch);
        
        // İlk satır için hesaplama fonksiyonunu test et
        setTimeout(() => {
            const firstQuantityInput = document.querySelector('.quantity-input');
            const firstUnitPriceInput = document.querySelector('.unit-price-input');
            if (firstQuantityInput && firstUnitPriceInput) {
                // İlk satır için hesaplama yap
                const row = firstQuantityInput.closest('.product-row');
                if (row) {
                    const unitPriceInput = row.querySelector('.unit-price-input');
                    const quantityInput = row.querySelector('.quantity-input');
                    const amountInput = row.querySelector('.amount-input');
                    
                    if (unitPriceInput && quantityInput && amountInput) {
                        const unitPrice = parseFloat(unitPriceInput.value.replace(',', '.')) || 0;
                        const quantity = parseInt(quantityInput.value) || 1;
                        const total = unitPrice * quantity;
                        amountInput.value = total.toFixed(2).replace('.', ',');
                    }
                }
            }
        }, 100);

        // Fiyat güncelleme fonksiyonu
        function updatePrice(quantityInput, amountInput) {
            if (!amountInput || !quantityInput) return;
            
            const basePrice = parseFloat(amountInput.getAttribute('data-base-price') || amountInput.value.replace(',', '.'));
            if (!isNaN(basePrice)) {
                const quantity = parseInt(quantityInput.value) || 1;
                const newPrice = (basePrice * quantity).toFixed(2);
                amountInput.value = newPrice.replace('.', ',');
            }
        }
        
        // Müşteri arama özelliği
        const customerSearch = document.getElementById('customer-search');
        const customerSuggestions = document.getElementById('customer-suggestions');
        const customerIdInput = document.getElementById('customer_id');
        
        if (customerSearch) {
            // Sayfa yüklendiğinde arama alanını temizle
            customerSearch.value = '';
            customerIdInput.value = '';
            document.getElementById('new_customer_name').value = '';
            document.getElementById('new_customer_phone').value = '';
            let customerTimeout;
            
            customerSearch.addEventListener('input', function() {
                clearTimeout(customerTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    customerSuggestions.classList.add('hidden');
                    customerIdInput.value = '';
                    return;
                }
                
                customerTimeout = setTimeout(() => {
                    fetch(`musteriara.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                customerSuggestions.innerHTML = data.map(customer => 
                                    `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" data-id="${customer.id}" data-name="${customer.isim}">
                                        <div class="font-medium">${customer.isim}</div>
                                        <div class="text-sm text-gray-500">${customer.numara || 'Telefon yok'}</div>
                                    </div>`
                                ).join('');
                                customerSuggestions.classList.remove('hidden');
                            } else {
                                customerSuggestions.innerHTML = `<div class="p-3 text-gray-500">"${query}" için müşteri bulunamadı. Yeni müşteri olarak eklenebilir.</div>`;
                                customerSuggestions.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Müşteri arama hatası:', error);
                        });
                }, 300);
            });
            
            // Müşteri seçimi
            customerSuggestions.addEventListener('click', function(e) {
                const item = e.target.closest('[data-id]');
                if (item) {
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    
                    customerSearch.value = name;
                    customerIdInput.value = id;
                    document.getElementById('new_customer_name').value = '';
                    document.getElementById('new_customer_phone').value = '';
                    customerSuggestions.classList.add('hidden');
                    // Bakiye getir
                    try { fetchCustomerBalance(id); } catch(e){}
                }
            });
            
            // Enter tuşu ile yeni müşteri ekleme veya arama yapma
            customerSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length > 0) {
                        // Eğer öneriler gösteriliyorsa ve bir öneri seçiliyse, onu seç
                        const selectedSuggestion = customerSuggestions.querySelector('[data-id]');
                        if (selectedSuggestion) {
                            const id = selectedSuggestion.dataset.id;
                            const name = selectedSuggestion.dataset.name;
                            customerSearch.value = name;
                            customerIdInput.value = id;
                            document.getElementById('new_customer_name').value = '';
                            document.getElementById('new_customer_phone').value = '';
                            customerSuggestions.classList.add('hidden');
                            try { fetchCustomerBalance(id); } catch(e){}
                        } else {
                            // Eğer öneri yoksa, yeni müşteri olarak ekle
                            customerIdInput.value = '0';
                            document.getElementById('new_customer_name').value = query;
                            customerSuggestions.classList.add('hidden');
                            // Yeni müşterilerde başlangıç bakiye 0 kabul edilir
                            try { showBalanceInfo({ bakiye: 0 }); } catch(e){}
                        }
                    }
                }
            });
            
            // Dışarı tıklandığında önerileri gizle
            document.addEventListener('click', function(e) {
                if (!customerSearch.contains(e.target) && !customerSuggestions.contains(e.target)) {
                    customerSuggestions.classList.add('hidden');
                }
            });
        }
        
        // Ürün arama özelliği
        function setupProductSearch(productSearchInput) {
            const productSuggestions = productSearchInput.parentElement.querySelector('.product-suggestions');
            const productIdInput = productSearchInput.parentElement.querySelector('.product-id');
            
            let productTimeout;
            
            productSearchInput.addEventListener('input', function() {
                clearTimeout(productTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    productSuggestions.classList.add('hidden');
                    productIdInput.value = '';
                    return;
                }
                
                productTimeout = setTimeout(() => {
                    fetch(`urunara.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                productSuggestions.innerHTML = data.map(product => 
                                    `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" data-id="${product.id}" data-name="${product.isim}">
                                        <div class="font-medium">${product.isim}</div>
                                        <div class="text-sm text-gray-500">${product.fiyat ? product.fiyat + ' ₺' : 'Fiyat belirtilmemiş'}</div>
                                    </div>`
                                ).join('');
                                productSuggestions.classList.remove('hidden');
                            } else {
                                productSuggestions.innerHTML = `<div class="p-3 text-gray-500">"${query}" için ürün bulunamadı. Yeni ürün olarak eklenebilir.</div>`;
                                productSuggestions.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Ürün arama hatası:', error);
                        });
                }, 300);
            });
            
            // Ürün seçimi
            productSuggestions.addEventListener('click', function(e) {
                const item = e.target.closest('[data-id]');
                if (item) {
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    
                    productSearchInput.value = name;
                    productIdInput.value = id;
                    productSearchInput.parentElement.querySelector('.new-product-name').value = '';
                    productSuggestions.classList.add('hidden');
                    // Fiyatı birim fiyat alanına otomatik doldur
                    const row = productSearchInput.closest('.product-row');
                    const unitPriceInput = row ? row.querySelector('.unit-price-input') : null;
                    const quantityInput = row ? row.querySelector('.quantity-input') : null;
                    const amountInput = row ? row.querySelector('.amount-input') : null;
                    
                    // Ürün fiyatını birim fiyat alanına doldur
                    fetch(`urunara.php?q=${encodeURIComponent(name)}`)
                        .then(r => r.json())
                        .then(list => {
                            const found = Array.isArray(list) ? list.find(p => String(p.id) === String(id)) : null;
                            if (found && unitPriceInput) {
                                const val = Number(found.fiyat || 0);
                                if (!isNaN(val) && val > 0) {
                                    unitPriceInput.value = val.toFixed(2).replace('.', ',');
                                    // Toplam hesapla
                                    if (quantityInput && amountInput) {
                                        const quantity = parseInt(quantityInput.value) || 1;
                                        const total = val * quantity;
                                        amountInput.value = total.toFixed(2).replace('.', ',');
                                    }
                                }
                            }
                        })
                        .catch(() => {});
                }
            });
            
            // Enter tuşu ile yeni ürün ekleme
            productSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length > 0) {
                        productIdInput.value = '0';
                        productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                        productSuggestions.classList.add('hidden');
                    }
                }
            });
            
            // Dışarı tıklandığında önerileri gizle ve manuel ürün kontrolü yap
            document.addEventListener('click', function(e) {
                if (!productSearchInput.contains(e.target) && !productSuggestions.contains(e.target)) {
                    productSuggestions.classList.add('hidden');
                    
                    // Eğer arama alanında değer var ama ürün seçilmemişse, manuel ürün olarak işle
                    const query = productSearchInput.value.trim();
                    if (query.length > 0 && (!productIdInput.value || productIdInput.value === '')) {
                        productIdInput.value = '0';
                        productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                    }
                }
            });
            
            // Form submit edilmeden önce manuel ürün kontrolü
            productSearchInput.addEventListener('blur', function() {
                const query = this.value.trim();
                if (query.length > 0 && (!productIdInput.value || productIdInput.value === '')) {
                    productIdInput.value = '0';
                    productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                }
            });
        }
        
        // Mevcut ürün arama alanlarını ayarla
        document.querySelectorAll('.product-search').forEach(setupProductSearch);
        
        // Yeni ürün satırı eklendiğinde arama özelliğini ayarla
        const originalAddProduct = window.addProduct;
        window.addProduct = function() {
            originalAddProduct();
            // Yeni eklenen ürün arama alanını ayarla
            const newProductSearch = document.querySelectorAll('.product-search');
            setupProductSearch(newProductSearch[newProductSearch.length - 1]);
        };

        // Bakiye yardımcıları
        function fetchCustomerBalance(id) {
            const info = document.getElementById('customer-balance');
            if (!info) return;
            info.classList.remove('hidden');
            info.innerHTML = '<span class="text-gray-500">Bakiye yükleniyor...</span>';
            fetch(`musteri_ozeti.php?customer=${encodeURIComponent(id)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error();
                    showBalanceInfo(data.data);
                })
                .catch(() => {
                    info.innerHTML = '<span class="text-red-600">Bakiye getirilemedi</span>';
                });
        }

        function showBalanceInfo(data) {
            const info = document.getElementById('customer-balance');
            if (!info) return;
            const bakiye = Number(data.bakiye || 0);
            const cls = bakiye > 0 ? 'text-red-600' : (bakiye < 0 ? 'text-green-600' : 'text-gray-700');
            info.innerHTML = `Mevcut bakiye: <span class="font-medium ${cls}">${bakiye.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ₺</span>`;
        }
    });

    // AJAX tabanlı arama ve filtreleme sistemi
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimeout;
        let currentPage = 1;
        let isLoading = false;
        
        // Filtre elementlerini seç
        const searchInput = document.getElementById('search');
        const productSelect = document.getElementById('product');
        const typeSelect = document.getElementById('type');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const searchLoading = document.getElementById('search-loading');
        const filterLoading = document.getElementById('filter-loading');
        
        // Müşteri ID'sini al
        const customerId = document.getElementById('customer-filter') ? 
            document.getElementById('customer-filter').value : null;
        
        // Arama fonksiyonu
        function performSearch(page = 1) {
            if (isLoading) return;
            
            isLoading = true;
            currentPage = page;
            
            // Loading göstergelerini göster
            if (searchInput.value.trim()) {
                searchLoading.classList.remove('hidden');
            }
            filterLoading.classList.remove('hidden');
            
            // Filtre parametrelerini hazırla
            const params = new URLSearchParams();
            if (customerId) params.append('customer', customerId);
            if (page > 1) params.append('page', page);
            if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
            if (productSelect.value) params.append('product', productSelect.value);
            if (typeSelect.value) params.append('type', typeSelect.value);
            if (dateFromInput.value) params.append('date_from', dateFromInput.value);
            if (dateToInput.value) params.append('date_to', dateToInput.value);
            
            // AJAX isteği gönder
            fetch(`api_search.php?${params.toString()}`)
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        throw new Error('Geçersiz yanıt formatı');
                    }
                    const data = await response.json();
                    if (!response.ok) {
                        const msg = data && data.message ? data.message : 'Arama sırasında bir hata oluştu.';
                        throw new Error(msg);
                    }
                    if (!data.success) {
                        const msg = data && data.message ? data.message : 'Arama sırasında bir hata oluştu.';
                        throw new Error(msg);
                    }
                    updateTable(data.data);
                    updatePagination(data.data.pagination);
                    updateTableTitle(data.data.selected_customer);
                })
                .catch(error => {
                    console.error('Arama hatası:', error);
                    showError(error.message || 'Arama sırasında bir hata oluştu.');
                })
                .finally(() => {
                    isLoading = false;
                    searchLoading.classList.add('hidden');
                    filterLoading.classList.add('hidden');
                });
        }
        
        // Tabloyu güncelle
        function updateTable(data) {
            const tbody = document.getElementById('transactions-tbody');
            const customerId = document.getElementById('customer-filter') ? 
                document.getElementById('customer-filter').value : null;
            
            if (data.transactions.length === 0) {
                const colspan = customerId ? '7' : '8';
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="text-center py-12 text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="bg-gray-100 rounded-full p-4 mb-2">
                                    <i class="bi bi-receipt text-5xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-medium">Henüz işlem bulunmuyor</h4>
                                <p class="text-sm text-gray-400 max-w-md">
                                    ${customerId ? 
                                        'Bu müşteri için henüz bir işlem kaydı oluşturulmamış. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.' :
                                        'Sistemde henüz bir işlem kaydı bulunmuyor. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.'
                                    }
                                </p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.transactions.forEach((row, index) => {
                const date = new Date(row.olusturma_zamani);
                const formattedDate = date.toLocaleDateString('tr-TR') + ' ' + 
                    date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
                
                const amount = parseFloat(row.miktar).toLocaleString('tr-TR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                const isDebit = row.odeme_tipi === 'borc';
                const typeBadge = isDebit ? 
                    '<span class="badge-debit flex items-center w-fit"><i class="bi bi-arrow-down-right mr-1"></i> Borç</span>' :
                    '<span class="badge-credit flex items-center w-fit"><i class="bi bi-arrow-up-right mr-1"></i> Tahsilat</span>';
                
                const productName = row.product_count && row.product_count > 0 ? 
                    `<span class="badge badge-primary">${row.product_count} kalem ürün</span>` :
                    (row.urun_isim ? 
                        `<span class="badge badge-outline">${escapeHtml(row.urun_isim)}</span>` :
                        (row.new_product_name ? 
                            `<span class="badge badge-outline">${escapeHtml(row.new_product_name)}</span>` :
                            '<span class="text-gray-400">-</span>'));
                
                const description = row.aciklama ? 
                    escapeHtml(row.aciklama) : 
                    '<span class="text-gray-400 italic">Not girilmedi</span>';
                
                const userName = row.kullanici_isim || 'Sistem';
                
                const customerLink = customerId ? '' : 
                    `<a href="islemler.php?customer=${row.musteri_id}" class="text-primary-600 hover:text-primary-900 font-medium">${escapeHtml(row.musteri_isim)}</a>`;
                
                const customerCell = customerId ? '' : `<td>${customerLink}</td>`;
                
                html += `
                    <tr class="animate-fadeIn" style="animation-delay: ${0.3 + (index * 0.05)}s">
                        ${customerCell}
                        <td>${productName}</td>
                        <td>${formattedDate}</td>
                        <td class="font-medium">${amount} ₺</td>
                        <td>${typeBadge}</td>
                        <td>${description}</td>
                        <td>
                            <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full" title="İşlemi ekleyen kullanıcı">
                                <i class="bi bi-person-fill mr-1 text-primary-500"></i>
                                ${escapeHtml(userName)}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <a href="yazdir.php?id=${row.id}" class="btn btn-outline btn-sm" title="Yazdır" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="islemler.php?edit=${row.id}${customerId ? '&customer=' + customerId : ''}${currentPage > 1 ? '&page=' + currentPage : ''}" class="btn btn-outline btn-sm text-primary" title="Düzenle">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="islemler.php?delete=${row.id}${customerId ? '&customer=' + customerId : ''}${currentPage > 1 ? '&page=' + currentPage : ''}" class="btn btn-outline btn-sm text-danger" title="Sil" onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Sayfalama güncelle
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');
            
            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<nav class="inline-flex rounded-md shadow-sm" aria-label="Sayfalama">';
            
            // Önceki sayfa
            if (pagination.current_page > 1) {
                html += `<button onclick="performSearch(${pagination.current_page - 1})" class="px-3 py-2 border border-gray-300 rounded-l-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-left"></i>
                </button>`;
            }
            
            // Sayfa numaraları
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i == 1 || i == pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const isActive = i === pagination.current_page;
                    const activeClass = isActive ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 hover:bg-gray-100';
                    html += `<button onclick="performSearch(${i})" class="px-3 py-2 border border-gray-300 ${activeClass}">
                        ${i}
                    </button>`;
                } else if ((i == 2 && pagination.current_page > 3) || (i == pagination.total_pages - 1 && pagination.current_page < pagination.total_pages - 2)) {
                    html += '<span class="px-3 py-2 border border-gray-300 bg-white text-gray-700">...</span>';
                }
            }
            
            // Sonraki sayfa
            if (pagination.current_page < pagination.total_pages) {
                html += `<button onclick="performSearch(${pagination.current_page + 1})" class="px-3 py-2 border border-gray-300 rounded-r-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-right"></i>
                </button>`;
            }
            
            html += '</nav>';
            container.innerHTML = html;
        }
        
        // Tablo başlığını güncelle
        function updateTableTitle(selectedCustomer) {
            const title = document.getElementById('table-title');
            if (selectedCustomer) {
                title.textContent = `${selectedCustomer.isim} - İşlem Geçmişi`;
            } else {
                title.textContent = 'Son İşlemler';
            }
        }
        
        // Hata mesajı göster
        function showError(message) {
            const tbody = document.getElementById('transactions-tbody');
            const customerId = document.getElementById('customer-filter') ? 
                document.getElementById('customer-filter').value : null;
            tbody.innerHTML = `
                <tr>
                    <td colspan="${customerId ? '7' : '8'}" class="text-center py-12 text-red-500">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <i class="bi bi-exclamation-triangle text-5xl"></i>
                            <h4 class="text-lg font-medium">Hata</h4>
                            <p class="text-sm">${message}</p>
                        </div>
                    </td>
                </tr>
            `;
        }
        
        // HTML escape fonksiyonu
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Event listeners
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(1);
                }, 500);
            });
        }
        
        if (productSelect) {
            productSelect.addEventListener('change', () => performSearch(1));
        }
        
        if (typeSelect) {
            typeSelect.addEventListener('change', () => performSearch(1));
        }
        
        if (dateFromInput) {
            dateFromInput.addEventListener('change', () => performSearch(1));
        }
        
        if (dateToInput) {
            dateToInput.addEventListener('change', () => performSearch(1));
        }
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                productSelect.value = '';
                typeSelect.value = '';
                dateFromInput.value = '';
                dateToInput.value = '';
                performSearch(1);
            });
        }
        
        // Global fonksiyon olarak tanımla
        window.performSearch = performSearch;
    });</script><?php  goto S2W5z; FDYFT: if ($search) { $conditions[] = "\x28\x4c\x4f\x57\105\122\x28\x6d\x2e\x69\x73\x69\155\51\40\x4c\111\113\x45\x20\x4c\x4f\x57\x45\122\50\x3f\x29\x20\x4f\122\40\114\x4f\x57\105\x52\x28\x69\x2e\x61\x63\151\x6b\x6c\x61\155\x61\51\x20\x4c\x49\113\105\40\114\117\127\x45\122\x28\x3f\51\x29"; $params[] = "\45{$search}\x25"; $params[] = "\x25{$search}\45"; } goto OED23; VqmJD: $transactions = $stmt->fetchAll(); goto I_99R; N2jAp: foreach ($transactions as $row) { $hasTransactions = true; $index++; ?>
<tr class="animate-fadeIn"style="animation-delay:<?php  echo 0.3 + $index * 0.05; ?>
s"><?php  if (!$customerId) { ?>
<td><a class="font-medium hover:text-primary-900 text-primary-600"href="islemler.php?customer=<?php  echo $row["\155\x75\163\164\x65\x72\151\137\151\144"]; ?>
"><?php  echo htmlspecialchars($row["\x6d\165\x73\164\x65\x72\x69\x5f\151\x73\151\155"]); ?>
</a></td><?php  } ?>
<td><?php  if (isset($row["\160\162\157\x64\165\143\x74\x5f\143\157\165\156\x74"]) && $row["\x70\x72\157\144\x75\x63\164\137\143\157\165\156\164"] > 0) { $subProductStmt = $pdo->prepare("\123\x45\114\105\x43\x54\x20\x69\56\x2a\x2c\x20\x75\x2e\x69\163\151\155\x20\x41\x53\40\165\162\165\x6e\137\x69\163\x69\155\x20\106\122\x4f\x4d\40\x69\x73\x6c\145\155\154\x65\x72\x20\x69\x20\x4c\105\x46\124\x20\x4a\117\111\x4e\40\165\x72\165\156\154\x65\162\40\x75\40\117\x4e\40\165\56\x69\144\40\x3d\x20\151\56\165\x72\165\x6e\137\x69\144\x20\x57\x48\x45\x52\x45\x20\x69\x2e\160\141\x72\x65\x6e\164\137\164\162\x61\156\x73\x61\143\164\151\x6f\156\x5f\151\x64\x20\75\x20\77\x20\x4f\x52\x44\x45\x52\x20\102\x59\40\x69\56\151\144\x20\101\123\x43"); $subProductStmt->execute(array($row["\x69\x64"])); $subProducts = $subProductStmt->fetchAll(); $productNames = array(); foreach ($subProducts as $subProduct) { if ($subProduct["\165\162\165\x6e\x5f\151\x73\151\x6d"]) { $productNames[] = $subProduct["\x75\162\165\156\137\x69\x73\x69\x6d"]; } else { $manualName = getManualProduct($subProduct["\x69\144"]); $productNames[] = $manualName ?: "\x4d\141\x6e\x75\x65\x6c\40\303\234\162\xc3\xbc\x6e"; } } ?>
<span class="badge badge-outline"><?php  echo htmlspecialchars(implode("\x20\55\40", $productNames)); ?>
</span><?php  } elseif (isset($row["\165\x72\165\x6e\137\x69\163\x69\x6d"]) && $row["\165\x72\165\156\x5f\x69\x73\151\x6d"]) { ?>
<span class="badge badge-outline"><?php  echo htmlspecialchars($row["\x75\162\x75\156\137\x69\x73\x69\155"]); ?>
</span><?php  } else { $manualName = getManualProduct($row["\151\x64"]); if ($manualName) { ?>
<span class="badge badge-outline"><?php  echo htmlspecialchars($manualName); ?>
</span><?php  } else { ?>
<span class="text-gray-400">-</span><?php  } } ?>
</td><td><?php  echo date("\x64\x2e\x6d\x2e\x59\x20\x48\x3a\x69", strtotime($row["\157\x6c\x75\x73\164\x75\x72\155\x61\137\x7a\141\155\x61\156\151"])); ?>
</td><td class="font-medium"><?php  echo number_format($row["\x6d\151\x6b\x74\141\162"], 2, "\54", "\56"); ?>
₺</td><td><?php  if ($row["\x6f\x64\145\155\x65\x5f\x74\151\160\x69"] === "\142\x6f\x72\x63") { ?>
<span class="flex items-center w-fit badge-debit"><i class="bi mr-1 bi-arrow-down-right"></i> Borç</span><?php  } else { ?>
<span class="flex items-center w-fit badge-credit"><i class="bi mr-1 bi-arrow-up-right"></i> Tahsilat</span><?php  } ?>
</td><td><?php  if ($row["\141\x63\151\x6b\x6c\x61\155\141"]) { echo htmlspecialchars($row["\141\143\x69\153\154\141\x6d\141"]); } else { ?>
<span class="text-gray-400 italic">Not girilmedi</span><?php  } ?>
</td><td><span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full"title="İşlemi ekleyen kullanıcı"><i class="bi text-primary-500 mr-1 bi-person-fill"></i><?php  echo isset($row["\x6b\165\154\154\141\156\x69\143\151\x5f\x69\x73\x69\155"]) && !empty($row["\x6b\165\x6c\x6c\x61\156\151\143\x69\x5f\x69\x73\x69\x6d"]) ? htmlspecialchars($row["\153\165\154\154\x61\156\x69\x63\151\x5f\151\x73\151\x6d"]) : "\x53\x69\163\x74\145\x6d"; ?>
</span></td><td class="text-right"><div class="flex gap-2 justify-end"><a class="btn btn-outline btn-sm"href="yazdir.php?id=<?php  echo $row["\x69\144"]; ?>
"title="Yazdır"target="_blank"><i class="bi bi-printer"></i> </a><a class="btn btn-outline btn-sm text-primary"href="islemler.php?edit=<?php  echo $row["\151\x64"]; echo $customerId ? "\x26\x63\165\x73\164\x6f\x6d\x65\x72\x3d" . $customerId : ''; echo isset($_GET["\x70\x61\147\x65"]) ? "\x26\x70\x61\147\145\x3d" . $_GET["\160\x61\147\x65"] : ''; ?>
"title="Düzenle"><i class="bi bi-pencil-square"></i> </a><a class="btn btn-outline btn-sm text-danger"href="islemler.php?delete=<?php  echo $row["\151\144"]; echo $customerId ? "\46\x63\165\x73\x74\157\x6d\x65\x72\75" . $customerId : ''; echo isset($_GET["\x70\x61\x67\x65"]) ? "\x26\160\x61\147\145\x3d" . $_GET["\x70\x61\147\x65"] : ''; ?>
"title="Sil"onclick='return confirm("Bu işlemi silmek istediğinize emin misiniz?")'><i class="bi bi-trash"></i></a></div></td></tr><?php  } goto Y28FM; pFymI: echo $dateTo; goto ERe5s; ni0ZO: $perPage = 10; goto JatSO; q9EiU: foreach ($params as $param) { $stmt->bindValue($paramIndex++, $param); } goto mXkLA; CScai: if ($selectedCustomer) { ?>
<span class="font-medium"><?php  echo htmlspecialchars($selectedCustomer["\151\163\x69\155"]); ?>
</span>müşterisi için işlemler<?php  } else { ?>
Tüm müşteriler için işlem ekle ve geçmişi görüntüle<?php  } goto vNA9X; QihyR: ?>
</div></div><div id="products-container"><div class="gap-4 grid grid-cols-1 md:grid-cols-12 mb-3 product-row"><div class="col-span-1 md:col-span-4"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-box-seam"></i> Ürün</label><div class="relative"><input class="form-input product-search"autocomplete="off"placeholder="Ürün ara veya yeni ürün adı yazın..."> <input class="product-id"name="products[0][product_id]"required type="hidden"> <input class="new-product-name"name="products[0][new_product_name]"type="hidden"><div class="bg-white border border-gray-300 absolute hidden max-h-60 overflow-y-auto rounded-md shadow-lg w-full z-10 product-suggestions"></div></div></div><div class="col-span-1 md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-arrow-left-right"></i> İşlem Türü</label> <select class="form-select type-select"name="products[0][type]"><option value="borc">Borç</option><option value="tahsilat">Tahsilat</option></select></div><div class="col-span-1 md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-123"></i> Adet</label> <input class="form-input quantity-input"value="1"name="products[0][quantity]"required min="1"type="number"></div><div class="col-span-1 md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-currency-exchange"></i> Birim Fiyat (₺)</label> <input class="form-input unit-price-input"name="products[0][unit_price]"placeholder="0,00"required></div><div class="col-span-1 md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-calculator"></i> Toplam (₺)</label> <input class="form-input amount-input"name="products[0][amount]"placeholder="0,00"readonly></div><div class="col-span-1 md:col-span-2"><label class="flex items-center form-label"><i class="bi text-primary-500 mr-2 bi-chat-left-text"></i> Not</label> <input class="form-input"name="products[0][note]"placeholder=""></div><div class="flex col-span-1 items-end md:col-span-1"><button class="btn btn-outline hidden remove-product text-red-500"type="button"title="Kaldır"><i class="bi bi-trash"></i></button></div></div></div><div class="flex items-center justify-between mt-4"><button class="btn btn-outline btn-sm"type="button"id="add-product"><i class="bi mr-1 bi-plus-circle"></i> Ürün Ekle</button> <button class="flex items-center btn btn-primary hover:shadow-md shadow-sm transition-all"type="submit"id="submitButton"><i class="bi mr-2 bi-plus-circle"></i> İşlemleri Ekle</button></div></form></div></div><div class="animate-fadeIn card-hover shadow-lg mb-6"><div class="card-header"><h3 class="flex items-center card-title"><i class="bi mr-2 text-primary-600 bi-search"></i> Arama ve Filtreleme</h3></div><div class="p-5"><div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4"><?php  goto rBaHp; iHw78: $typeFilter = isset($_GET["\164\x79\160\x65"]) ? $_GET["\164\x79\x70\x65"] : ''; goto ZC3LI; ZEB0h: $search = isset($_GET["\163\x65\141\162\x63\150"]) ? trim($_GET["\163\x65\x61\162\x63\x68"]) : ''; goto Qq8y1; A16NZ: if ($customerId) { $conditions[] = "\151\56\155\165\x73\x74\x65\162\x69\x5f\x69\x64\40\75\x20\x3f"; $params[] = $customerId; } goto FDYFT; r3UiU: $currentUserId = isset($_SESSION["\165\x73\145\162"]["\x69\144"]) ? $_SESSION["\x75\163\x65\162"]["\151\x64"] : 0; goto wAE07; Nrm3d: if (!empty($conditions)) { $countSql .= "\127\x48\105\x52\x45\x20\x28\x69\56\151\163\137\155\x61\151\x6e\137\x74\x72\141\156\x73\x61\143\x74\151\157\x6e\x20\75\40\61\40\117\122\40\x69\56\x69\x73\x5f\155\x61\x69\156\x5f\x74\x72\141\156\163\x61\x63\164\x69\157\156\40\x49\123\40\116\125\x4c\x4c\51\40\x41\116\104\40" . implode("\x20\x41\116\x44\40", $conditions); } else { $countSql .= "\x57\110\x45\122\x45\x20\50\x69\x2e\151\163\137\x6d\141\151\x6e\137\164\x72\x61\x6e\x73\141\143\x74\x69\x6f\x6e\x20\x3d\x20\x31\x20\x4f\122\x20\151\x2e\151\163\137\x6d\x61\x69\156\137\164\x72\x61\156\x73\141\143\x74\x69\157\156\40\111\123\40\x4e\125\114\x4c\x29"; } goto Wje_A; uJp1r: ?>
<div class="flex gap-4 flex-col mb-6 md:flex-row md:items-center md:justify-between"><div><h1 class="flex items-center font-bold text-2xl text-gray-800"><i class="bi mr-2 text-primary-600 bi-cash-coin"></i> İşlem Yönetimi</h1><p class="text-sm text-gray-600 mt-1"><?php  goto CScai; c0x9B: ?>
</span></h3><?php  goto alcuf; Q4tcs: $countStmt->execute($params); goto V6UjN; dS1pK: $products = $pdo->query("\x53\105\114\x45\103\124\40\x69\144\54\x20\x69\163\151\155\x20\106\x52\117\115\40\x75\162\x75\x6e\154\145\162\x20\x4f\x52\104\x45\122\40\142\171\x20\151\x73\151\x6d\x20\101\123\103")->fetchAll(); goto Ovo1y; jUxQP: ?>
</tbody></table></div></div></div></div><div class="flex justify-center mt-6"id="pagination-container"><?php  goto mhr6b; Mc4RW: echo $typeFilter === "\x74\x61\x68\163\151\154\141\164" ? "\x73\145\x6c\x65\x63\x74\x65\x64" : ''; goto kJbWi; he166: if (!empty($conditions)) { $whereClause = "\x57\x48\105\122\x45\x20" . implode("\40\x41\116\x44\40", $conditions); } goto kg0S2; suU9s: ?>
<div class="form-group"><label class="form-label"for="search">Arama</label><div class="relative"><span class="flex items-center absolute inset-y-0 left-0 pl-3"><i class="bi bi-search text-gray-400"></i> </span><input class="form-input pl-10"value="<?php  goto IfaQR; w_M1H: echo $dateFrom; goto yLfsO; YHFdB: $page = isset($_GET["\160\x61\147\x65"]) ? max(1, (int) $_GET["\x70\141\147\x65"]) : 1; goto ni0ZO; rM3uI: if ($currentUserId) { $currentUserName = isset($_SESSION["\165\x73\145\x72"]["\x6e\x61\x6d\x65"]) ? $_SESSION["\165\163\x65\162"]["\156\141\x6d\x65"] : "\123\x69\163\x74\x65\x6d"; } goto L3_cY; yliMd: if (isset($_GET["\163\165\143\143\145\x73\163"])) { ?>
<div class="animate-fadeIn mb-6 bg-green-100 border-green-500 border-l-4 p-4 rounded shadow-sm text-green-700"role="alert"><div class="flex items-center"><div class="py-1"><i class="bi mr-3 bi-check-circle-fill text-green-500"></i></div><div><p class="font-medium">Başarılı!</p><p class="text-sm"><?php  if ($_GET["\x73\165\x63\143\145\x73\x73"] == "\x32") { echo "\304\260\305\237\x6c\x65\x6d\x20\x62\141\305\x9f\x61\x72\304\xb1\x79\x6c\x61\x20\x67\303\274\x6e\143\x65\154\x6c\145\x6e\x64\151\x2e"; } else { if ($_GET["\163\165\143\x63\x65\x73\x73"] == "\63") { echo "\xc4\xb0\xc5\x9f\x6c\x65\x6d\x20\x62\141\305\x9f\141\162\304\xb1\x79\154\x61\x20\x73\151\x6c\x69\156\144\x69\56"; } else { echo "\xc4\xb0\305\237\x6c\145\x6d\x20\x62\x61\xc5\237\141\162\xc4\xb1\171\154\141\x20\x65\153\x6c\x65\x6e\x64\x69\56"; } } ?>
</p></div></div></div><?php  } goto N8SQg; mXkLA: $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT); goto NKfJB; mhr6b: if ($totalPages > 1) { ?>
<nav aria-label="Sayfalama"class="inline-flex rounded-md shadow-sm"><?php  if ($page > 1) { ?>
<a class="bg-white border border-gray-300 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-l-md"href="islemler.php?<?php  echo http_build_query(array_merge($_GET, array("\x70\x61\147\145" => $page - 1))); ?>
"><i class="bi bi-chevron-left"></i> </a><?php  } for ($i = 1; $i <= $totalPages; $i++) { if ($i == 1 || $i == $totalPages || $i >= $page - 1 && $i <= $page + 1) { ?>
<a class="border px-3 py-2 border-gray-300<?php  echo $i == $page ? "\x62\147\x2d\x70\x72\151\155\141\x72\171\x2d\x36\60\x30\40\x74\145\x78\164\55\x77\x68\x69\x74\x65\x20\142\157\162\x64\x65\x72\55\160\162\151\155\x61\x72\x79\x2d\66\x30\x30" : "\142\147\x2d\167\x68\151\x74\x65\x20\164\145\x78\164\x2d\x67\x72\141\171\55\67\60\60\x20\150\157\166\145\x72\x3a\142\147\55\147\x72\x61\x79\55\61\60\60"; ?>
"href="islemler.php?<?php  echo http_build_query(array_merge($_GET, array("\160\x61\x67\145" => $i))); ?>
"><?php  echo $i; ?>
</a><?php  } elseif ($i == 2 && $page > 3 || $i == $totalPages - 1 && $page < $totalPages - 2) { ?>
<span class="bg-white border border-gray-300 px-3 py-2 text-gray-700">...</span><?php  } } if ($page < $totalPages) { ?>
<a class="bg-white border border-gray-300 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-r-md"href="islemler.php?<?php  echo http_build_query(array_merge($_GET, array("\x70\141\x67\145" => $page + 1))); ?>
"><i class="bi bi-chevron-right"></i> </a><?php  } ?>
</nav><?php  } goto hqCU6; PREnF: $customers = $pdo->query("\x53\x45\114\105\103\124\40\x69\144\x2c\40\x69\163\151\155\x20\x46\122\x4f\115\40\155\x75\x73\164\145\162\151\x6c\145\x72\40\117\x52\x44\x45\122\x20\x42\x59\40\x69\x73\151\155\40\101\x53\103")->fetchAll(); goto dS1pK; QY9kf: $conditions = array(); goto m6sN7; ZB0Sg: $whereClause = ''; goto he166; OED23: if ($productFilter) { $conditions[] = "\x69\56\x75\162\165\156\x5f\x69\x64\40\75\x20\77"; $params[] = $productFilter; } goto ee2SM; RT1jV: require_login(); goto SgqDC; dtmzr: foreach ($products as $product) { ?>
<option value="<?php  echo $product["\x69\144"]; ?>
"<?php  echo $productFilter == $product["\151\x64"] ? "\x73\x65\x6c\145\143\x74\145\144" : ''; ?>
><?php  echo htmlspecialchars($product["\x69\163\x69\x6d"]); ?>
</option><?php  } goto UD_tS; nH5ys: ?>
<th><i class="bi text-primary-500 mr-1 bi-box-seam"></i> Ürün</th><th><i class="bi text-primary-500 mr-1 bi-calendar-date"></i> Tarih</th><th><i class="bi text-primary-500 mr-1 bi-currency-exchange"></i> Tutar (₺)</th><th><i class="bi text-primary-500 mr-1 bi-arrow-left-right"></i> Tür</th><th><i class="bi text-primary-500 mr-1 bi-chat-left-text"></i> Açıklama</th><th><i class="bi text-primary-500 mr-1 bi-person-plus"></i> Ekleyen</th><th class="text-right"><i class="bi text-primary-500 bi-gear-fill"></i> İşlemler</th></tr></thead><tbody id="transactions-tbody"><?php  goto YL2jQ; oRhV3: ?>
"id="search"placeholder="Müşteri adı veya not..."><div class="flex items-center absolute inset-y-0 hidden pr-3 right-0"id="search-loading"><div class="text-primary-500 spinner-border spinner-border-sm"role="status"><span class="sr-only">Yükleniyor...</span></div></div></div></div><div class="form-group"><label class="form-label"for="product">Ürün</label> <select class="form-select"id="product"><option value="">Tüm Ürünler</option><?php  goto dtmzr; ERe5s: ?>
"id="date_to"type="date"></div></div><div class="flex items-center col-span-full gap-2 justify-end mt-2"><div class="hidden"id="filter-loading"><div class="flex items-center text-primary-500"><div class="spinner-border spinner-border-sm mr-2"role="status"><span class="sr-only">Yükleniyor...</span></div><span class="text-sm">Filtreleniyor...</span></div></div></div></div></div></div><div class="animate-fadeIn card-hover shadow-lg"style="animation-delay:.2s"><div class="card-header"><div class="flex items-center justify-between"><h3 class="flex items-center card-title"><i class="bi mr-2 text-primary-600 bi-clock-history"></i> <span id="table-title"><?php  goto nwq3Q; yLfsO: ?>
"id="date_from"type="date"></div><div class="form-group"><label class="form-label"for="date_to">Bitiş Tarihi</label> <input class="form-input"value="<?php  goto pFymI; NeS1x: if (!$customerId) { ?>
<th><i class="bi text-primary-500 mr-1 bi-person-badge"></i> Müşteri</th><?php  } goto nH5ys; vNA9X: ?>
</p></div><?php  goto v7RL_; SgqDC: $pdo = get_pdo_connection(); goto S40nZ; O8PdW: if (isset($_GET["\x64\145\x6c\145\164\145"])) { $transactionId = (int) $_GET["\144\145\x6c\x65\x74\145"]; $pdo->beginTransaction(); try { $stmt = $pdo->prepare("\123\x45\x4c\x45\x43\x54\40\52\x20\106\x52\x4f\115\x20\151\163\154\145\155\154\145\x72\40\x57\x48\x45\122\x45\40\x69\x64\40\x3d\x20\x3f"); $stmt->execute(array($transactionId)); $transaction = $stmt->fetch(); if ($transaction) { if ($transaction["\157\144\x65\155\x65\x5f\x74\151\160\x69"] === "\142\157\162\143") { $pdo->prepare("\125\120\x44\x41\124\x45\x20\x6d\x75\163\164\145\162\151\x6c\x65\x72\x20\x53\x45\x54\40\164\165\x74\x61\x72\x20\x3d\x20\x74\x75\164\x61\162\40\x2d\x20\x3f\x20\127\110\105\x52\x45\x20\x69\144\40\x3d\x20\x3f")->execute(array($transaction["\x6d\x69\x6b\164\141\x72"], $transaction["\x6d\165\x73\164\145\162\151\137\151\x64"])); } else { $pdo->prepare("\125\120\104\x41\x54\105\40\155\x75\x73\x74\x65\x72\151\x6c\145\162\x20\123\x45\124\40\164\165\164\141\x72\40\75\40\x74\165\x74\x61\162\40\53\40\77\40\x57\x48\x45\122\x45\x20\151\144\40\75\x20\x3f")->execute(array($transaction["\155\x69\x6b\x74\141\x72"], $transaction["\x6d\165\163\164\x65\162\151\137\x69\144"])); } $pdo->prepare("\x44\x45\114\x45\x54\x45\x20\x46\x52\x4f\x4d\40\x69\163\x6c\145\x6d\154\x65\x72\x20\x57\110\x45\x52\x45\40\151\144\40\x3d\40\x3f")->execute(array($transactionId)); $pdo->commit(); $redirect_url = "\151\x73\154\x65\155\x6c\145\x72\56\x70\150\160"; $query_params = array(); if ($customerId) { $query_params["\143\165\163\164\x6f\155\145\x72"] = $customerId; } if (isset($_GET["\x70\x61\x67\145"])) { $query_params["\x70\141\147\145"] = $_GET["\160\141\147\x65"]; } $query_params["\x73\x75\143\x63\145\163\163"] = "\x33"; $redirect_url .= "\77" . http_build_query($query_params); header("\x4c\157\x63\x61\x74\x69\157\156\72\40" . $redirect_url); die; } } catch (Exception $e) { $pdo->rollBack(); $error = "\x53\151\154\155\x65\x20\151\305\x9f\x6c\145\x6d\x69\40\142\x61\305\x9f\141\162\xc4\261\x73\304\261\x7a\x3a\40" . $e->getMessage(); } } goto d5aco; JatSO: $offset = ($page - 1) * $perPage; goto ZEB0h; l4Lvl: if ($customerId) { $st = $pdo->prepare("\123\105\114\105\x43\x54\x20\x2a\40\x46\122\117\x4d\x20\x6d\x75\163\x74\x65\162\x69\154\145\162\40\x57\x48\105\x52\105\40\151\144\40\75\x20\77"); $st->execute(array($customerId)); $selectedCustomer = $st->fetch(); } goto YHFdB; YL2jQ: $hasTransactions = false; goto Y78n3; alcuf: if ($customerId && $selectedCustomer) { ?>
<a class="flex items-center btn btn-secondary btn-sm"href="musteri_rapor.php?customer=<?php  echo $customerId; ?>
"><i class="bi mr-2 bi-printer"></i> Yazdır </a><?php  } goto EEXRZ; si70v: ?>