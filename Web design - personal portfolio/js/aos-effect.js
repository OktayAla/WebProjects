       // AOS (Animate On Scroll) ve EnhancedParticleNetwork kütüphanelerini kullanarak sayfa yükleme ve animasyon efektleri ekleme
       // Bu kütüphaneler, sayfa yüklenirken ve kaydırıldığında animasyonlar ve partikül efektleri oluşturmak için kullanılır.

       // AOS (Animate On Scroll) ayarlar
        AOS.init({
            duration: 600,
            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
            once: true,
            offset: 50,
            delay: 0,
            mirror: false,
            anchorPlacement: 'top-bottom'
        });

        // Sayfa yüklenince tüm animasyonları başlat
        window.addEventListener('load', function () {
            setTimeout(() => {
                // Sayfa yüklendiğinde body'yi görünür yap
                document.body.style.opacity = '1';
                document.body.style.transform = 'none';
            }, 100);
        });

        // Partikül efektleri
        document.addEventListener('DOMContentLoaded', function () {

            // Partikül alanını oluştur
            const particlesContainer = document.getElementById('particles-container'); // Partikül alanı için div oluşturma
            const canvas = document.createElement('canvas'); // Canvas öğesini oluşturma
            canvas.id = 'particles-canvas'; // Canvas id
            particlesContainer.appendChild(canvas); // Canvas'ı div'e ekleme

            // Partikül ağı oluşturma
            new EnhancedParticleNetwork('particles-canvas', {
                particleCount: 100,
                colors: ['#c9a0ff', '#00e6d2', '#d4af37'],
                lineColor: 'rgba(201, 160, 255, 0.2)',
                maxDistance: 150,
                speed: 0.5,
                sizeRange: { min: 1, max: 3 },
                responsive: true,
                interactionRadius: 200,
                interactionStrength: 10
            });
        });