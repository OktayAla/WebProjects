// Sayfalama için gerekli değişkenler
    let currentPage = 1;
    const itemsPerPage = 6;
    
    // Sayfa türüne göre doğru card sınıfını seç
    const isLezzetPage = window.location.href.includes('lezzet-duraklari');
    const cardSelector = isLezzetPage ? '.culinary-card' : '.destination-card';
    const cards = document.querySelectorAll(cardSelector);
    
    const pageNumbers = document.querySelector('.page-numbers');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const pagination = document.querySelector('.pagination');

    // Toplam sayfa sayısını hesapla
    const totalPages = Math.ceil(cards.length / itemsPerPage);

    // Sayfa numaralarını oluştur
    function createPageNumbers() {
        pageNumbers.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.classList.add('page-number');
            if (i === currentPage) button.classList.add('active');
            button.textContent = i;
            button.addEventListener('click', () => goToPage(i));
            pageNumbers.appendChild(button);
        }
    }

    // Sayfada gösterilecek öğeleri güncelle
    function updateDisplay() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        cards.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Sayfa numaralarını güncelle
        createPageNumbers();

        // Önceki/Sonraki butonlarını güncelle
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    // Belirli bir sayfaya git
    function goToPage(page) {
        currentPage = page;
        updateDisplay();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Önceki/Sonraki sayfa butonları için event listener'lar
    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) goToPage(currentPage - 1);
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage < totalPages) goToPage(currentPage + 1);
    });

    // İlk yükleme
    updateDisplay();