// Tablo işlemleri için özel JS kodları
document.addEventListener('DOMContentLoaded', function() {
    // Tablo sıralama
    const sortTable = (table, column, asc = true) => {
        const dirModifier = asc ? 1 : -1;
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        // Sıralanmış satırlar
        const sortedRows = rows.sort((a, b) => {
            const aColText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
            const bColText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();

            return aColText > bColText ? (1 * dirModifier) : (-1 * dirModifier);
        });

        // Mevcut satırları kaldır
        while (table.querySelector('tbody').firstChild) {
            table.querySelector('tbody').firstChild.remove();
        }

        // Sıralı satırları ekle
        table.querySelector('tbody').append(...sortedRows);
    };

    // Sıralanabilir tablo başlıkları için event listener
    document.querySelectorAll('th.sortable').forEach(headerCell => {
        headerCell.addEventListener('click', () => {
            const table = headerCell.closest('table');
            const headerIndex = Array.from(headerCell.parentElement.children).indexOf(headerCell);
            const currentIsAscending = headerCell.classList.contains('th-sort-asc');

            // Önceki sıralama işaretlerini temizle
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('th-sort-asc', 'th-sort-desc');
            });

            headerCell.classList.toggle('th-sort-asc', !currentIsAscending);
            headerCell.classList.toggle('th-sort-desc', currentIsAscending);

            sortTable(table, headerIndex, !currentIsAscending);
        });
    });

    // Tablo filtreleme
    document.querySelectorAll('.table-filter').forEach(filter => {
        filter.addEventListener('input', function() {
            const table = document.querySelector(this.dataset.table);
            const searchText = this.value.toLowerCase();

            table.querySelectorAll('tbody tr').forEach(row => {
                const rowText = Array.from(row.children)
                    .map(cell => cell.textContent.toLowerCase())
                    .join(' ');
                row.style.display = rowText.includes(searchText) ? '' : 'none';
            });
        });
    });

    // Toplu seçim işlemleri
    document.querySelectorAll('.select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const table = this.closest('table');
            const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(item => item.checked = this.checked);
        });
    });

    // Sayfalama işlemleri
    const paginateTable = (table, rowsPerPage = 10) => {
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        let currentPage = 1;

        const showPage = (page) => {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        };

        // Sayfalama kontrollerini oluştur
        if(totalPages > 1) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'pagination';
            
            for(let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.addEventListener('click', () => {
                    currentPage = i;
                    showPage(currentPage);
                    
                    // Aktif sayfa stilini güncelle
                    paginationContainer.querySelectorAll('button').forEach(btn => {
                        btn.classList.toggle('active', btn.textContent == currentPage);
                    });
                });
                paginationContainer.appendChild(button);
            }
            
            table.parentNode.insertBefore(paginationContainer, table.nextSibling);
        }

        showPage(currentPage);
    };

    // Sayfalama özelliğini aktif et
    document.querySelectorAll('table.paginated').forEach(table => {
        paginateTable(table);
    });
});
