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
    // ve tıklandığında "Hakkımda" bölümüne kaydır
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', () => {
            const aboutSection = document.querySelector('#about');
            if (aboutSection) {
                window.scrollTo({
                    top: aboutSection.offsetTop,
                    behavior: 'smooth'
                });
            }
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

    updateSlider();

    function updateSlider() {
        // Smooth scroll animation with transform
        wrapper.style.transform = `translateX(-${currentPage * (100 / 3)}%)`;
        updateButtons();
    }

    function updateButtons() {
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

    // Touch events için swipe desteği
    let touchStartX = 0;
    let touchEndX = 0;

    wrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    });

    wrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        const difference = touchStartX - touchEndX;
        
        if (Math.abs(difference) > 50) { // minimum swipe mesafesi
            if (difference > 0 && currentPage < totalSlides - 1) {
                currentPage++;
                updateSlider();
            } else if (difference < 0 && currentPage > 0) {
                currentPage--;
                updateSlider();
            }
        }
    });
}

