document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.hero-slider .swiper-container', {
        // Otomatik geçiş
        autoplay: {
            delay: 10000, // 10 saniye
            disableOnInteraction: false,
        },
        
        // Geçiş efekti
        effect: 'fade', // fade efekti
        fadeEffect: {
            crossFade: true
        },
        
        // Sonsuz döngü
        loop: true,
        
        // Navigasyon okları
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        
        // Sayfa noktaları
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        
        // Geçiş animasyonu süresi
        speed: 1000,

        // Responsive breakpoints
        breakpoints: {
            // >= 320px
            320: {
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    enabled: false
                }
            },
            // >= 768px
            768: {
                navigation: {
                    enabled: true,
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                }
            }
        },
        
        // Touch interaction settings
        touchRatio: 1,
        touchAngle: 45,
        grabCursor: true,
    });
});
