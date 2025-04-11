/*
* Bu JavaScript dosyası, web sitesinin ana işlevselliğini kontrol eden temel dosyadır.
* İçerdiği temel işlevler:
* - Sayfa içi yumuşak kaydırma
* - Yetenek çubuklarının animasyonları
* - Aşağı kaydırma göstergesi
* - Proje kartları hover efektleri
* - Sayfa yükleme ve başlatma işlemleri
*/

// Sayfa yüklendiğinde tüm başlatma fonksiyonlarını çağır
document.addEventListener('DOMContentLoaded', function () {

    // Yumuşak kaydırma işlemini başlat
    initSmoothScroll();

    // Yetenek çubuklarının animasyonlarını başlat
    initSkillAnimations();

    // Aşağı kaydırma göstergesini başlat
    initScrollIndicator();

    // Proje kartlarının hover efektlerini başlat
    initProjectCards();

    // Proje slider'ını başlat
    initProjectSlider();
});

// Sayfa içi yumuşak kaydırma işlemini başlat
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            // Eğer hedef element varsa, sayfayı yumuşak bir şekilde kaydır
            // ve kaydırma işlemi tamamlandığında, hedef elementin üst kısmını 50px yukarıda göster
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 50,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Yetenek çubuklarının animasyonlarını başlat
function initSkillAnimations() {
    // Görünürlük gözlemcisi oluştur
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            // Element görünür olduğunda
            if (entry.isIntersecting) {
                // İlerleme çubuklarını seç ve animasyonu uygula
                const progressBars = entry.target.querySelectorAll('.progress-bar');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }
        });
    }, { threshold: 0.2 });

    const skillsSection = document.querySelector('.skills');
    if (skillsSection) {
        observer.observe(skillsSection);
    }
}

// Aşağı kaydırma göstergesini başlat
function initScrollIndicator() {
    const scrollIndicator = document.querySelector('.scroll-down');

    // Eğer kaydırma göstergesi varsa, tıklama olayını dinle
    // ve tıklandığında sıradaki bölüme kaydır
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', () => {
            scrollToNextSection();
        });
    }
}

// Sıradaki bölüme kaydırma işlevi
function scrollToNextSection() {
    const sections = document.querySelectorAll('section');
    const scrollPosition = window.scrollY + window.innerHeight / 2; // Adjusted to consider the middle of the viewport
    let nextSection = null;

    // Mevcut konumdan sonraki ilk bölümü bul
    for (let i = 0; i < sections.length; i++) {
        const section = sections[i];
        const sectionTop = section.offsetTop;
        
        if (sectionTop > scrollPosition) {
            nextSection = section;
            break;
        }
    }

    // Eğer sonraki bölüm bulunduysa, o bölüme kaydır
    if (nextSection) {
        window.scrollTo({
            top: nextSection.offsetTop,
            behavior: 'smooth'
        });
    } else {
        // Eğer sonraki bölüm bulunamadıysa, ilk bölüme kaydır (sayfa başına dön)
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}

// Proje kartlarının hover efektlerini başlat
function initProjectCards() {
    // Animasyon ve efektler kaldırıldı: hover efektleri devre dışı
}

// Yetenek çubuklarının başlangıç animasyonlarını ayarla
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const skillPercentage = bar.parentElement.previousElementSibling?.querySelector('.skill-percentage');

        // Eğer yüzde değeri varsa, başlangıçta çubuğun genişliğini 0% yap ve sonra gerçek yüzdeye ayarla
        if (skillPercentage) {
            const percentage = skillPercentage.textContent;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = percentage;
            }, 500);
        }
    });
});

// AOS (Animate On Scroll) kütüphanesini başlat
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 800,
        easing: 'ease-out',
        once: false,
        mirror: true
    });
}

// Proje slider'ını başlat
function initProjectSlider() {
    const wrapper = document.querySelector('.project-wrapper');
    const prevBtn = document.querySelector('.prev-arrow');
    const nextBtn = document.querySelector('.next-arrow');
    const slideGroups = wrapper.querySelectorAll('.slide-group');
    let currentPage = 0;
    const totalSlides = slideGroups.length;

    if (!wrapper || !prevBtn || !nextBtn) return;

    // Mobil cihaz kontrolü
    const isMobile = window.innerWidth <= 576;
    
    // Only set these dynamically for non-mobile devices
    // For mobile devices, we use CSS to handle the width
    if (!isMobile) {
        // Set the width of the wrapper dynamically based on the number of slide groups
        wrapper.style.width = `${totalSlides * 100}%`;
        
        // Ensure each slide group has the correct width
        slideGroups.forEach(group => {
            group.style.width = `${100 / totalSlides}%`;
        });
    }
    
    // Slider'ı başlangıç durumuna getir
    updateSlider();

    function updateSlider() {
        // For mobile, we use a simpler transform since CSS handles the widths
        if (isMobile) {
            wrapper.style.transform = `translateX(-${currentPage * 33.333}%)`;
        } else {
            // For desktop, use the dynamic calculation
            wrapper.style.transform = `translateX(-${currentPage * (100 / totalSlides)}%)`;
        }
        updateButtons();
    }

    function updateButtons() {
        // Mobil cihazlarda butonları görünür yap
        if (isMobile) {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
        
        prevBtn.style.opacity = currentPage === 0 ? "0.3" : "1";
        prevBtn.disabled = currentPage === 0;
        nextBtn.style.opacity = currentPage === totalSlides - 1 ? "0.3" : "1";
        nextBtn.disabled = currentPage === totalSlides - 1;
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage < totalSlides - 1) {
            currentPage++;
            updateSlider();
        }
    });

    // Touch events için geliştirilmiş swipe desteği
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartTime = 0;
    let touchEndTime = 0;

    wrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartTime = new Date().getTime();
    });

    wrapper.addEventListener('touchmove', (e) => {
        // Sayfa kaydırma davranışını engelle
        e.preventDefault();
    }, { passive: false });

    wrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        touchEndTime = new Date().getTime();
        
        const difference = touchStartX - touchEndX;
        const timeDiff = touchEndTime - touchStartTime;
        
        // Hızlı swipe için daha düşük eşik değeri, yavaş swipe için daha yüksek
        const swipeThreshold = timeDiff < 300 ? 30 : 50;
        
        if (Math.abs(difference) > swipeThreshold) {
            if (difference > 0 && currentPage < totalSlides - 1) {
                // Sağa swipe
                currentPage++;
                updateSlider();
            } else if (difference < 0 && currentPage > 0) {
                // Sola swipe
                currentPage--;
                updateSlider();
            }
        }
    });
    
    // Pencere boyutu değiştiğinde slider'ı güncelle
    window.addEventListener('resize', () => {
        const newIsMobile = window.innerWidth <= 576;
        
        // If switching between mobile and desktop, reload to apply correct styling
        if (newIsMobile !== isMobile) {
            location.reload();
        }
        
        // Sayfa yeniden boyutlandırıldığında slider'ı sıfırla
        currentPage = 0;
        updateSlider();
    });
}

