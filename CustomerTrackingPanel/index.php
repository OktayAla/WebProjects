<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
 goto pJQJE; WvdNR: $recentStmtGlobal = $pdo->query("\123\x45\114\x45\103\124\40\151\x2e\151\x64\x2c\40\x69\56\155\x69\x6b\164\x61\162\x2c\40\x69\56\157\x64\145\155\x65\137\x74\x69\x70\151\54\x20\x69\56\141\x63\151\x6b\154\x61\155\x61\x2c\x20\x69\x2e\157\154\x75\163\x74\x75\x72\x6d\x61\137\x7a\x61\155\x61\156\x69\x2c\xa\x9\11\155\x2e\151\x73\151\155\40\x41\123\40\155\x75\x73\x74\145\162\151\x5f\151\163\x69\x6d\x2c\x20\103\117\x41\x4c\x45\x53\103\x45\50\x75\x2e\151\163\151\155\x2c\47\55\47\x29\40\101\x53\x20\x75\x72\x75\x6e\x5f\x69\x73\x69\x6d\xa\x9\40\x46\122\x4f\115\40\x69\163\x6c\x65\x6d\154\x65\162\x20\x69\xa\11\x20\x4a\117\x49\x4e\x20\155\165\163\164\x65\162\x69\154\x65\x72\x20\x6d\x20\x4f\x4e\x20\155\56\x69\x64\40\x3d\40\151\x2e\x6d\165\163\x74\x65\162\151\137\151\144\12\x9\x20\x4c\x45\106\124\40\x4a\117\x49\116\40\x75\162\x75\x6e\154\x65\x72\40\165\40\x4f\x4e\x20\165\x2e\x69\x64\40\75\40\x69\x2e\x75\162\165\x6e\137\x69\x64\xa\x9\x20\x4f\122\104\x45\122\40\x42\131\x20\x69\x2e\157\154\x75\163\164\x75\162\x6d\x61\137\x7a\141\x6d\141\156\151\x20\x44\x45\x53\x43\xa\x9\x20\114\x49\x4d\111\124\x20\61\60"); goto rbVpr; N9HC9: ?>
<div class="card-hover animate-fadeIn shadow-lg mb-8"><div class="card-header"><h3 class="items-center flex card-title"><i class="bi mr-2 text-primary-600 bi-clock-history"></i> Son Yapılan İşlemler</h3></div><div class="p-0"><div class="table-container"><table class="table table-hover"><thead><tr><th>Müşteri</th><th>Ürün</th><th>Tarih</th><th>Tutar (₺)</th><th>Tür</th><th>Açıklama</th></tr></thead><tbody><?php  goto Xn7dJ; AZuou: $userRole = $_SESSION["\165\x73\x65\162"]["\x72\157\x6c"] ?? "\x75\x73\x65\162"; goto WvdNR; V6lbR: ?>
<div class="container mx-auto px-4 py-6"><h1 class="items-center flex font-bold mb-6 text-2xl text-gray-800"><i class="bi mr-2 text-primary-600 bi-speedometer2"></i> Yönetim Paneli</h1><?php  goto sskc1; Xn7dJ: if (!empty($recentTransactions)) { foreach ($recentTransactions as $row) { ?>
<tr><td><?php  echo htmlspecialchars($row["\x6d\165\163\x74\x65\x72\x69\x5f\x69\x73\x69\x6d"]); ?>
</td><td><?php  echo $row["\165\162\165\156\x5f\x69\163\x69\x6d"] ? "\74\x73\160\x61\156\40\143\x6c\141\x73\x73\x3d\42\142\141\x64\x67\x65\40\142\x61\x64\x67\x65\x2d\x6f\x75\164\x6c\151\156\x65\42\x3e" . htmlspecialchars($row["\165\162\x75\156\137\x69\163\x69\x6d"]) . "\x3c\x2f\x73\x70\141\x6e\76" : "\x3c\163\x70\141\x6e\x20\143\154\x61\163\163\x3d\42\164\145\x78\x74\x2d\147\162\141\x79\55\64\60\x30\42\x3e\x2d\74\x2f\163\x70\141\x6e\x3e"; ?>
</td><td><?php  echo date("\x64\56\155\56\x59\x20\x48\72\151", strtotime($row["\157\154\x75\163\164\165\162\x6d\141\x5f\172\141\x6d\141\156\151"])); ?>
</td><td class="font-medium"><?php  echo number_format($row["\x6d\x69\153\164\141\x72"], 2, "\54", "\56"); ?>
₺</td><td><?php  if ($row["\x6f\144\145\x6d\145\x5f\x74\x69\160\x69"] === "\142\157\x72\x63") { ?>
<span class="items-center inline-flex w-fit badge-debit"><i class="bi mr-1 bi-arrow-down-right"></i>Borç</span><?php  } else { ?>
<span class="items-center inline-flex w-fit badge-credit"><i class="bi mr-1 bi-arrow-up-right"></i>Tahsilat</span><?php  } ?>
</td><td><?php  echo $row["\141\143\x69\x6b\154\x61\155\x61"] ? htmlspecialchars($row["\141\143\151\x6b\x6c\141\155\141"]) : "\x3c\163\160\x61\156\40\143\x6c\x61\163\x73\75\x22\164\145\x78\164\55\x67\x72\141\x79\x2d\x34\60\60\40\151\x74\141\154\151\x63\42\x3e\55\x3c\57\x73\160\141\x6e\76"; ?>
</td></tr><?php  } } else { ?>
<tr><td class="text-center py-6 text-gray-500"colspan="6">Kayıt bulunamadı.</td></tr><?php  } goto MTMTQ; NWFZm: require_login(); goto HXbFL; sskc1: if ($userRole === "\141\144\155\151\x6e") { ?>
<div class="grid gap-6 grid-cols-1 mb-8 dashboard-stats lg:grid-cols-4 md:grid-cols-2"><div class="card-hover animate-slideInUp stat-card"style="animation-delay:.1s"><div class="stat-icon"><i class="bi bi-people-fill"></i></div><div class="stat-info"><span class="stat-label">Toplam Müşteri</span> <span class="stat-value"><?php  echo $totalCustomers; ?>
</span></div></div><div class="card-hover animate-slideInUp stat-card"style="animation-delay:.2s"><div class="stat-icon"style="background:linear-gradient(135deg,#0284c7 0,#0369a1 100%)"><i class="bi bi-box-seam"></i></div><div class="stat-info"><span class="stat-label">Toplam Ürün</span> <span class="stat-value text-primary-700"><?php  echo $totalProducts; ?>
</span></div></div><div class="card-hover animate-slideInUp stat-card"style="animation-delay:.3s"><div class="stat-icon"style="background:linear-gradient(135deg,#16a34a 0,#15803d 100%)"><i class="bi bi-receipt"></i></div><div class="stat-info"><span class="stat-label">Toplam İşlem</span> <span class="stat-value text-success-700"><?php  echo $totalTransactions; ?>
</span></div></div><div class="card-hover animate-slideInUp stat-card"style="animation-delay:.4s"style="cursor:pointer"onclick='showDetailModal("sales")'><div class="stat-icon"style="background:linear-gradient(135deg,#f59e0b 0,#d97706 100%)"><i class="bi bi-bag-fill"></i></div><div class="stat-info"><span class="stat-label">Toplam Satış</span> <span class="stat-value text-warning-600"><?php  echo number_format($totalSales, 2, "\x2c", "\x2e"); ?>
₺</span></div></div></div><div class="grid gap-6 grid-cols-1 mb-8 lg:grid-cols-3"><div class="card-hover animate-fadeIn shadow-lg"><div class="card-header"><h3 class="items-center flex card-title"><i class="bi mr-2 text-primary-600 bi-cash-stack"></i> Tahsilat Durumu</h3></div><div class="p-5"><div class="items-center flex flex-col"><div class="h-40 mb-4 relative w-40"><svg class="w-full h-full"viewBox="0 0 36 36"><path class="circle-bg"d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"fill="none"stroke="#eee"stroke-width="3"/><path class="circle"d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"fill="none"stroke="#16a34a"stroke-width="3"stroke-dasharray="<?php  echo min(100, $collectionRate); ?>
, 100"/><text alignment-baseline="central"class="percentage"font-size="8"text-anchor="middle"x="18"y="20.35"><?php  echo number_format($collectionRate, 1, "\54", "\x2e"); ?>
%</text></svg></div><div class="grid gap-4 grid-cols-2 w-full"><div class="text-center"style="cursor:pointer"onclick='showDetailModal("collections")'><p class="text-gray-500 text-sm">Toplam Tahsilat</p><p class="font-bold text-success-600"><?php  echo number_format($totalCollections, 2, "\x2c", "\56"); ?>
₺</p></div><div class="text-center"style="cursor:pointer"onclick='showDetailModal("receivables")'><p class="text-gray-500 text-sm">Toplam Alacak</p><p class="font-bold text-red-600"><?php  echo number_format($totalReceivables, 2, "\x2c", "\56"); ?>
₺</p></div></div></div></div></div><div class="card-hover animate-fadeIn shadow-lg lg:col-span-2"><div class="card-header"><h3 class="items-center flex card-title"><i class="bi mr-2 text-primary-600 bi-graph-up-arrow"></i> Son 14 Gün Borç / Tahsilat</h3></div><div class="p-5"><canvas height="120"id="trendChart"></canvas></div></div></div><div class="grid gap-6 grid-cols-1 mb-8 lg:grid-cols-1"><div class="card-hover animate-fadeIn shadow-lg"><div class="card-header"><h3 class="items-center flex card-title"><i class="bi mr-2 text-primary-600 bi-people"></i> En Aktif Müşteriler</h3></div><div class="p-5"><canvas height="120"id="customersChart"></canvas></div></div></div><div class="card-hover animate-fadeIn shadow-lg mb-8"><div class="card-header"><h3 class="items-center flex card-title"><i class="bi mr-2 text-primary-600 bi-bar-chart"></i> Son 6 Ay Borç / Tahsilat Trendi</h3></div><div class="p-5"><canvas height="100"id="monthlyChart"></canvas></div></div><?php  } goto N9HC9; Tiae8: ?>
<div class="grid gap-6 grid-cols-1 lg:grid-cols-3"></div></div><div class="items-center flex bg-black bg-opacity-50 fixed hidden inset-0 justify-center z-50"id="detailModal"><div class="w-full bg-white max-h-[90vh] max-w-4xl mx-4 overflow-y-auto rounded-lg shadow-xl"><div class="p-5 border-b border-gray-200"><div class="items-center flex justify-between"><h3 class="font-semibold text-gray-900 text-lg"id="modalTitle">Detaylar</h3><button class="hover:text-gray-500 text-gray-400"id="closeDetailModal"><i class="bi bi-x-lg"></i></button></div></div><div class="p-5"><div id="detailContent"></div></div></div></div><script>function showDetailModal(type) {
    const modal = document.getElementById('detailModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('detailContent');
    
    content.innerHTML = '<div class="text-center py-8"><i class="bi bi-hourglass-split text-4xl text-gray-400"></i><p class="mt-2 text-gray-600">Yükleniyor...</p></div>';
    modal.classList.remove('hidden');
    
    switch(type) {
        case 'sales':
            title.textContent = 'Toplam Satış Detayları';
            break;
        case 'collections':
            title.textContent = 'Tahsilat Detayları';
            break;
        case 'receivables':
            title.textContent = 'Alacak Detayları';
            break;
    }
    
    fetch(`detaylar.php?type=${type}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(error => {
            content.innerHTML = '<div class="text-center py-8 text-red-600"><i class="bi bi-exclamation-triangle text-4xl"></i><p class="mt-2">Veri yüklenirken hata oluştu.</p></div>';
        });
}

document.getElementById('closeDetailModal').addEventListener('click', function() {
    document.getElementById('detailModal').classList.add('hidden');
});

// Modal dışına tıklandığında kapat
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});</script><?php  goto ymmA1; pJQJE: require_once __DIR__ . "\x2f\151\x6e\143\x6c\x75\144\x65\163\x2f\141\x75\164\150\x2e\x70\x68\x70"; goto NWFZm; F8nGz: $pdo = get_pdo_connection(); goto AZuou; ymmA1: require_once __DIR__ . "\x2f\151\156\x63\x6c\x75\144\x65\x73\57\146\x6f\x6f\x74\x65\x72\x2e\x70\x68\x70"; goto y1yAt; rbVpr: $recentTransactions = $recentStmtGlobal ? $recentStmtGlobal->fetchAll() : array(); goto zhQuZ; HXbFL: require_once __DIR__ . "\x2f\x69\x6e\143\x6c\x75\x64\145\163\57\x68\145\x61\144\145\x72\x2e\x70\x68\160"; goto F8nGz; OI19C: if ($userRole === "\x61\x64\x6d\151\156") { ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script><script>(function(){
                // Günlük trend verileri
                const dayLabels =<?php  echo json_encode($dayLabels ?? array()); ?>
;
                const borcData =<?php  echo json_encode($borcData ?? array()); ?>
;
                const tahsilatData =<?php  echo json_encode($tahsilatData ?? array()); ?>
;
                
                // Müşteri verileri
                const topCustomers =<?php  echo json_encode($topCustomers ?? array()); ?>
;
                
                // Aylık trend verileri
                const monthLabels =<?php  echo json_encode($monthLabels ?? array()); ?>
;
                const monthlyBorcData =<?php  echo json_encode($monthlyBorcData ?? array()); ?>
;
                const monthlyTahsilatData =<?php  echo json_encode($monthlyTahsilatData ?? array()); ?>
;

                // Günlük trend grafiği
                const trendCtx = document.getElementById('trendChart');
                if (trendCtx && dayLabels.length) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: dayLabels.map(date => {
                                const d = new Date(date);
                                return d.toLocaleDateString('tr-TR', {day: 'numeric', month: 'short'});
                            }),
                            datasets: [
                                { label: 'Borç', data: borcData, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.1)', tension: .3, fill: true },
                                { label: 'Tahsilat', data: tahsilatData, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.1)', tension: .3, fill: true }
                            ]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Müşteri grafiği
                const customersCtx = document.getElementById('customersChart');
                if (customersCtx && topCustomers.length) {
                    new Chart(customersCtx, {
                        type: 'bar',
                        data: {
                            labels: topCustomers.map(c => c.musteri_adi),
                            datasets: [{
                                label: 'İşlem Sayısı',
                                data: topCustomers.map(c => Number(c.islem_sayisi)),
                                backgroundColor: '#6366f1',
                                order: 1
                            }, {
                                label: 'Toplam Tutar (₺)',
                                data: topCustomers.map(c => Number(c.toplam_tutar)),
                                backgroundColor: '#f59e0b',
                                type: 'line',
                                order: 0,
                                yAxisID: 'y1'
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'İşlem Sayısı'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Toplam Tutar (₺)'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            if (context.dataset.label === 'Toplam Tutar (₺)') {
                                                return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                            }
                                            return context.dataset.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Aylık trend grafiği
                const monthlyCtx = document.getElementById('monthlyChart');
                if (monthlyCtx && monthLabels.length) {
                    new Chart(monthlyCtx, {
                        type: 'bar',
                        data: {
                            labels: monthLabels,
                            datasets: [
                                { 
                                    label: 'Borç', 
                                    data: monthlyBorcData, 
                                    backgroundColor: 'rgba(239,68,68,0.7)',
                                    borderColor: '#ef4444',
                                    borderWidth: 1
                                },
                                { 
                                    label: 'Tahsilat', 
                                    data: monthlyTahsilatData, 
                                    backgroundColor: 'rgba(34,197,94,0.7)',
                                    borderColor: '#22c55e',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // SVG daire grafiği için stil
                const circlePath = document.querySelector('.circle');
                if (circlePath) {
                    circlePath.style.transformOrigin = 'center';
                    circlePath.style.transform = 'rotate(-90deg)';
                    circlePath.style.transition = 'stroke-dasharray 0.8s ease';
                }
            })();</script><?php  } goto Tiae8; MTMTQ: ?>
</tbody></table></div></div></div><?php  goto OI19C; zhQuZ: if ($userRole === "\x61\144\155\x69\156") { $totalCustomers = (int) $pdo->query("\123\x45\114\x45\103\x54\x20\103\x4f\x55\x4e\124\50\x2a\x29\40\106\x52\117\x4d\40\155\165\163\164\145\x72\151\x6c\145\162")->fetchColumn(); $totalProducts = (int) $pdo->query("\123\x45\114\x45\x43\x54\x20\x43\117\x55\x4e\124\50\x2a\51\x20\106\x52\117\x4d\40\x75\162\x75\156\154\145\x72")->fetchColumn(); $totalTransactions = (int) $pdo->query("\123\105\114\105\103\124\40\103\117\x55\x4e\x54\x28\x2a\x29\40\x46\122\x4f\x4d\40\151\x73\x6c\x65\x6d\x6c\x65\x72")->fetchColumn(); $totalSales = (double) $pdo->query("\123\105\114\105\x43\x54\40\103\117\x41\114\x45\123\x43\x45\x28\123\x55\115\x28\x6d\151\153\164\x61\162\x29\x2c\x30\x29\x20\x46\122\x4f\115\40\151\x73\154\145\155\x6c\x65\162\x20\x57\110\105\122\x45\40\157\x64\x65\155\x65\x5f\x74\151\x70\x69\40\x49\116\x20\50\x27\142\x6f\162\x63\x27\54\x27\x74\x61\150\163\151\154\x61\164\47\51")->fetchColumn(); $totalCollections = (double) $pdo->query("\x53\105\x4c\x45\103\x54\40\103\x4f\101\114\x45\x53\103\105\x28\123\125\115\x28\155\151\x6b\x74\141\x72\51\x2c\x30\x29\40\x46\122\117\115\x20\151\163\154\x65\155\x6c\x65\162\40\127\x48\x45\x52\105\40\x6f\144\145\x6d\145\x5f\x74\151\160\x69\40\x3d\x20\x27\164\141\x68\x73\x69\x6c\141\x74\47")->fetchColumn(); $totalReceivables = (double) $pdo->query("\123\x45\x4c\105\x43\124\x20\103\x4f\101\x4c\105\123\x43\105\x28\x53\125\x4d\50\155\x69\153\164\x61\162\51\x2c\60\51\x20\106\x52\117\x4d\x20\x69\163\154\145\155\x6c\x65\x72\x20\x57\110\105\122\105\x20\x6f\144\145\155\145\137\164\x69\x70\151\40\x3d\40\x27\x62\x6f\x72\x63\47")->fetchColumn(); $collectionRate = $totalReceivables > 0 ? $totalCollections / $totalReceivables * 100 : 0; $trendStmt = $pdo->query("\123\x45\114\105\x43\x54\x20\104\101\124\x45\x28\157\154\165\x73\164\165\x72\x6d\x61\137\x7a\x61\x6d\141\x6e\151\51\x20\x41\123\40\x67\x75\156\54\12\40\40\x20\40\x20\40\x20\x20\x20\40\40\40\123\125\x4d\50\x43\x41\123\x45\40\127\110\x45\116\40\x6f\144\145\x6d\145\x5f\164\151\160\x69\x20\x3d\40\47\142\157\x72\x63\47\x20\124\110\x45\x4e\40\x6d\x69\x6b\164\x61\x72\x20\x45\x4c\x53\x45\40\x30\40\x45\x4e\104\x29\x20\101\x53\x20\x74\157\160\154\141\155\x5f\x62\157\x72\x63\54\12\40\x20\40\x20\40\x20\40\40\x20\40\x20\x20\x53\125\115\x28\x43\101\x53\x45\40\x57\x48\105\x4e\40\157\144\145\x6d\145\137\x74\151\160\x69\40\75\40\47\x74\x61\x68\163\151\154\141\164\x27\x20\x54\x48\105\x4e\40\155\x69\153\x74\141\162\x20\105\114\123\x45\x20\60\x20\105\116\x44\x29\40\x41\123\40\164\x6f\160\154\141\x6d\x5f\164\x61\x68\163\x69\154\x61\x74\12\40\40\40\x20\x20\40\40\40\x20\x46\x52\117\115\40\151\163\x6c\x65\x6d\154\x65\x72\xa\x20\40\x20\40\x20\40\x20\x20\x20\127\110\105\122\x45\x20\157\154\165\x73\164\165\162\x6d\x61\137\x7a\141\x6d\x61\x6e\151\40\76\x3d\x20\104\101\124\105\137\x53\x55\102\50\103\125\x52\104\101\x54\x45\x28\x29\x2c\40\111\116\124\105\x52\x56\101\x4c\40\x31\x34\40\x44\101\x59\x29\12\40\x20\x20\x20\40\x20\40\x20\40\107\122\x4f\x55\120\40\102\x59\x20\x44\101\124\x45\x28\x6f\154\165\163\x74\x75\x72\155\x61\x5f\172\141\x6d\x61\x6e\151\x29\12\40\40\40\40\x20\x20\40\x20\40\x4f\x52\104\x45\x52\x20\102\131\x20\x67\165\x6e\x20\101\123\103"); $rawTrend = $trendStmt ? $trendStmt->fetchAll() : array(); $dayLabels = array(); $borcData = array(); $tahsilatData = array(); $map = array(); foreach ($rawTrend as $row) { $map[$row["\147\165\x6e"]] = $row; } for ($i = 13; $i >= 0; $i--) { $label = date("\131\x2d\x6d\x2d\x64", strtotime("\55{$i}\40\x64\141\x79")); $dayLabels[] = $label; $borcData[] = isset($map[$label]) ? (double) $map[$label]["\x74\x6f\160\154\141\x6d\137\142\x6f\x72\143"] : 0.0; $tahsilatData[] = isset($map[$label]) ? (double) $map[$label]["\164\x6f\160\154\141\x6d\137\x74\x61\150\163\151\x6c\x61\x74"] : 0.0; } $topCustomersStmt = $pdo->query("\x53\105\114\x45\x43\x54\x20\155\x2e\x69\163\151\x6d\x20\x41\123\x20\155\165\x73\x74\x65\x72\151\x5f\141\144\x69\x2c\40\103\x4f\125\116\x54\50\x69\x2e\151\144\x29\40\x41\x53\x20\x69\163\154\145\x6d\x5f\163\x61\171\151\x73\x69\54\40\123\125\115\x28\x69\x2e\x6d\x69\153\x74\141\162\51\40\x41\x53\40\x74\x6f\160\154\141\x6d\x5f\164\165\164\x61\x72\xa\x20\40\40\x20\x20\40\40\x20\x20\x46\x52\117\x4d\40\151\x73\x6c\145\x6d\x6c\145\x72\x20\x69\12\x20\x20\x20\40\40\x20\x20\x20\x20\112\x4f\x49\x4e\40\x6d\x75\163\x74\145\x72\151\154\145\x72\x20\155\40\x4f\x4e\40\x69\56\x6d\165\163\x74\x65\x72\x69\137\x69\144\40\x3d\40\155\x2e\x69\x64\12\x20\x20\40\x20\40\40\40\x20\40\107\122\x4f\x55\120\40\102\131\x20\151\56\x6d\x75\163\x74\x65\162\151\x5f\151\x64\xa\40\40\40\x20\40\x20\x20\x20\40\117\x52\104\x45\122\x20\x42\x59\40\x69\x73\x6c\145\155\x5f\163\141\171\x69\x73\151\x20\104\x45\123\103\xa\40\x20\40\x20\x20\40\x20\40\40\x4c\111\115\111\124\x20\65"); $topCustomers = $topCustomersStmt ? $topCustomersStmt->fetchAll() : array(); $monthlyStmt = $pdo->query("\x53\105\x4c\x45\103\x54\40\104\101\124\105\137\x46\x4f\122\x4d\101\124\50\x6f\x6c\165\163\x74\x75\x72\155\x61\137\x7a\x61\155\x61\156\151\54\40\x27\45\x59\x2d\45\x6d\x27\51\x20\x41\123\40\x61\x79\x2c\xa\x20\x20\x20\x20\40\x20\40\x20\x20\x20\40\40\x53\x55\115\50\x43\x41\123\105\40\x57\110\105\x4e\x20\157\144\x65\x6d\x65\137\x74\x69\160\x69\40\75\40\x27\x62\x6f\x72\143\47\x20\x54\110\x45\116\40\x6d\151\x6b\x74\x61\162\40\105\x4c\123\105\40\60\x20\105\x4e\104\x29\40\x41\x53\x20\x74\157\160\154\141\155\137\142\x6f\x72\143\54\xa\x20\x20\x20\x20\40\x20\x20\x20\40\40\x20\40\x53\x55\x4d\50\x43\x41\123\x45\x20\127\x48\105\116\40\x6f\144\x65\155\x65\x5f\164\151\160\151\x20\75\x20\47\164\141\x68\163\x69\154\141\164\x27\40\124\x48\x45\116\x20\155\151\153\164\141\162\x20\x45\114\123\105\40\x30\40\105\x4e\x44\51\x20\x41\x53\40\164\157\160\154\x61\x6d\x5f\164\x61\x68\163\x69\x6c\x61\164\12\x20\40\40\x20\x20\x20\40\40\x20\106\122\117\x4d\40\151\x73\154\x65\x6d\x6c\x65\x72\xa\40\40\x20\40\40\40\40\40\40\x57\110\x45\x52\105\x20\x6f\154\x75\163\x74\165\162\155\x61\137\172\x61\x6d\141\x6e\x69\x20\x3e\75\x20\x44\x41\124\x45\x5f\x53\x55\102\x28\103\125\x52\x44\x41\124\x45\50\x29\54\40\x49\x4e\x54\105\122\x56\101\x4c\40\66\x20\x4d\x4f\116\x54\x48\x29\12\40\40\40\40\40\x20\x20\40\40\x47\x52\117\125\120\x20\x42\131\x20\141\171\xa\40\40\x20\40\40\x20\x20\x20\x20\x4f\122\x44\105\x52\x20\x42\x59\40\141\x79\x20\101\x53\103"); $monthlyData = $monthlyStmt ? $monthlyStmt->fetchAll() : array(); $monthLabels = array(); $monthlyBorcData = array(); $monthlyTahsilatData = array(); $monthMap = array(); foreach ($monthlyData as $row) { $monthMap[$row["\141\171"]] = $row; } for ($i = 5; $i >= 0; $i--) { $monthLabel = date("\x59\x2d\155", strtotime("\55{$i}\x20\155\x6f\x6e\164\150")); $monthLabels[] = date("\115\x20\131", strtotime("\x2d{$i}\x20\155\157\x6e\164\x68")); $monthlyBorcData[] = isset($monthMap[$monthLabel]) ? (double) $monthMap[$monthLabel]["\x74\x6f\x70\x6c\x61\x6d\137\142\x6f\x72\143"] : 0.0; $monthlyTahsilatData[] = isset($monthMap[$monthLabel]) ? (double) $monthMap[$monthLabel]["\164\x6f\160\154\141\x6d\137\x74\x61\x68\163\x69\154\x61\x74"] : 0.0; } } goto V6lbR; y1yAt: ?>