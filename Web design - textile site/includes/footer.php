<?php
 goto FE4VR; OUswR: echo date("\131"); goto eXN8F; FE4VR: ?>
<footer class="text-white bg-dark py-4"><div class="container"><div class="row"><div class="col-md-4 mb-4 mb-md-0"><h5>Elegance</h5><p>Elegance Textile, yüksek kaliteli tekstil ürünleri üretimi ve ihracatı konusunda uzmanlaşmış bir firmadır. Müşterilerimize en iyi hizmeti sunmak için sürekli olarak yenilikçi çözümler geliştiriyoruz.</p><div class="social-links"><a class="text-white me-3"href="#"><i class="bi bi-facebook"></i></a> <a class="text-white me-3"href="#"><i class="bi bi-instagram"></i></a> <a class="text-white me-3"href="#"><i class="bi bi-linkedin"></i></a></div></div><div class="col-md-4 mb-4 mb-md-0"></div><div class="col-md-4"><h5>İletişim</h5><address class="mb-0"><p><i class="bi me-2 bi-geo-alt"></i> Merkezefendi Cad. Pamukkale Mah. No:1<br>Denizli, Türkiye</p><p><i class="bi me-2 bi-telephone"></i> +90 (258) 000 0000</p><p><i class="bi me-2 bi-envelope"></i> info@elegancetextile.com</p></address></div></div><hr class="mt-4"><div class="pt-3 text-center"><p class="mb-0">©<?php  goto OUswR; eXN8F: ?>
Elegance Textile. Tüm hakları saklıdır.</p></div></div></footer><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"defer></script><script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"defer></script><script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script><script>// Sayfa yüklendiğinde çalışacak scriptler
        document.addEventListener('DOMContentLoaded', function() {
            // AOS başlatma
            AOS.init({
                duration: 800,
                once: true,
                startEvent: 'load'
            });
            
            // Lazy loading
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });</script>