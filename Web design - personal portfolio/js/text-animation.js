/*
* Bu JavaScript dosyası, web sitesindeki metinsel öğeler için özel animasyonları kontrol eder.
* İçerdiği temel işlevler:
* - Metinlerin harf harf zıplama animasyonları
* - Vurgulanan metin parçalarının özel animasyonları
* - Dalga efekti ile hareket eden metinler
* - Highlight (vurgulama) efektleri için özel işleyiciler
*/

// Yazı animasyonu için gerekli JavaScript kodu
// Bouncing ve wave efektleri için gerekli kodlar
document.addEventListener('DOMContentLoaded', function () {

    // Bouncing efekini uygulamak için gerekli kodlar
    const bouncingTextElement = document.querySelector('.bouncing-text');

    // Eğer bouncing-text sınıfına sahip bir element varsa
    // highlight sınıfına sahip span'ları bul ve bunları ayır
    if (bouncingTextElement) {
        const originalText = bouncingTextElement.innerHTML;

        // highlight sınıfına sahip span'ları bulmak için regex kullan
        const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
        const matches = [...originalText.matchAll(highlightRegex)];

        // Eğer highlight sınıfına sahip span'lar varsa, bunları ayır ve yeni bir HTML oluştur ve animasyon ekle
        if (matches.length > 0) {
            const beforeHighlight = originalText.split(matches[0][0])[0];
            const afterHighlight = originalText.split(matches[matches.length - 1][0])[1];

            // Yeni HTML oluştur
            // beforeHighlight ve afterHighlight kısımlarını ayrı ayrı işlemle
            let newHtml = '';

            // beforeHighlight kısmını işleme al
            for (let i = 0; i < beforeHighlight.length; i++) {
                const char = beforeHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${i * 0.05}s">${char}</span>`;
                }
            }

            // highlight kısmını işleme al
            matches.forEach(match => {
                newHtml += `<span class="${match[1]}">${match[2]}</span>`;
            });

            // afterHighlight kısmını işleme al
            for (let i = 0; i < afterHighlight.length; i++) {
                const char = afterHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${(beforeHighlight.length + i) * 0.05}s">${char}</span>`;
                }
            }

            bouncingTextElement.innerHTML = newHtml;
        }


        // Wave efekti için gerekli kodlar
        const waveTextElements = document.querySelectorAll('.wave-text');
        waveTextElements.forEach(element => {
            const text = element.textContent;
            let newHtml = '';

            // Her bir karakteri ayrı ayrı işleme al
            for (let i = 0; i < text.length; i++) {
                if (text[i] === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${i * 0.1}s">${text[i]}</span>`;
                }
            }

            element.innerHTML = newHtml;
        });
    }
});