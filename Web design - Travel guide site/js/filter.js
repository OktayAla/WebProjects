document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    
    // Sayfa türüne göre doğru card sınıfını seç
    const isLezzetPage = window.location.href.includes('lezzet-duraklari');
    const cardSelector = isLezzetPage ? '.culinary-card' : '.destination-card';
    const cards = document.querySelectorAll(cardSelector);
    
    const itemsPerPage = 6;
    let currentPage = 1;

    function filterAndPaginateDestinations() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedRegions = Array.from(checkboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        // Önce tüm kartları filtrele
        const filteredCards = Array.from(cards).filter(card => {
            const title = card.querySelector('h2').textContent.toLowerCase();
            
            // Sayfa türüne göre kategori seçicisini belirle
            const categorySelector = isLezzetPage ? '.cuisine-category' : '.destination-category';
            const region = card.querySelector(categorySelector)?.textContent || '';
            
            const description = card.querySelector('p').textContent.toLowerCase();
            
            // Sayfa türüne göre meta seçicisini belirle
            const metaSelector = isLezzetPage ? '.cuisine-meta' : '.destination-meta';
            const location = card.querySelector(metaSelector)?.textContent.toLowerCase() || '';
            
            const allContent = `${title} ${description} ${location}`.toLowerCase();
            const matchesSearch = searchTerm === '' || allContent.includes(searchTerm);
            const matchesRegion = selectedRegions.length === 0 || selectedRegions.includes(region);

            return matchesSearch && matchesRegion;
        });

        // Sayfalama işlemi
        const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
        
        // Sayfa numarası kontrolü
        if (currentPage > totalPages) {
            currentPage = 1;
        }

        // Tüm kartları gizle
        cards.forEach(card => card.style.display = 'none');

        // Sadece mevcut sayfaya ait kartları göster
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        filteredCards.slice(startIndex, endIndex).forEach(card => {
            card.style.display = 'flex';
        });

        // Sayfalama butonlarını güncelle
        updatePaginationButtons(totalPages);
    }

    function updatePaginationButtons(totalPages) {
        const paginationContainer = document.querySelector('.pagination');
        let paginationHTML = '';

        if (totalPages > 1) {
            // Önceki sayfa butonu
            paginationHTML += `
                <button class="page-btn" 
                        onclick="changePage(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}>
                    &laquo; Önceki
                </button>`;

            // Sayfa numaraları
            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `
                    <button class="page-btn ${i === currentPage ? 'active' : ''}" 
                            onclick="changePage(${i})">
                        ${i}
                    </button>`;
            }

            // Sonraki sayfa butonu
            paginationHTML += `
                <button class="page-btn" 
                        onclick="changePage(${currentPage + 1})" 
                        ${currentPage === totalPages ? 'disabled' : ''}>
                    Sonraki &raquo;
                </button>`;
        }

        paginationContainer.innerHTML = paginationHTML;
    }

    // Global fonksiyon olarak tanımla
    window.changePage = function(pageNum) {
        currentPage = pageNum;
        filterAndPaginateDestinations();
    };

    // Event listeners
    searchInput.addEventListener('input', () => {
        currentPage = 1;  // Arama yapılınca ilk sayfaya dön
        filterAndPaginateDestinations();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            currentPage = 1;  // Filtre değişince ilk sayfaya dön
            filterAndPaginateDestinations();
        });
    });
    
    // Sayfa yüklendiğinde filtreleme ve sayfalama işlemini başlat
    filterAndPaginateDestinations();

    // İlk yükleme
    filterAndPaginateDestinations();
});
