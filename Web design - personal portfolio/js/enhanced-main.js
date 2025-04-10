/*
* Bu JavaScript dosyası, web sitesinin gelişmiş özelliklerini ve animasyonlarını yöneten ana dosyadır.
* İçerdiği temel işlevler:
* - Gelişmiş sayfa yükleme optimizasyonları
* - AOS (Animate on Scroll) entegrasyonu
* - Yüzen efektler ve gradyan metin efektleri
* - Gelişmiş form etkileşimleri
* - İyileştirilmiş proje kartı animasyonları
* - Parallax efektleri
* - Özelleştirilmiş metin animasyonları
*/

// Sayfa yüklendiğinde tüm başlatma işlemlerini gerçekleştir
document.addEventListener('DOMContentLoaded', function () {
    const isMobile = window.innerWidth <= 768;

    // AOS kütüphanesini mobil cihazlarda devre dışı bırak
    if (typeof AOS !== 'undefined') {
        AOS.init({
            disable: isMobile,
            duration: isMobile ? 0 : 800,
            once: true,
            mirror: false
        });
    }

    // AOS animasyonlarını hemen uygula
    // Tüm elementlerin başlangıç stillerini ayarla
    document.querySelectorAll('[data-aos]').forEach(function (el) {
        el.style.opacity = '1';
        el.style.transform = 'none';
    });

    // Temel animasyon ve etkileşimleri başlat
    addFloatingEffect(); // Yüzen efektleri ekle
    initSmoothScroll(); // Yumuşak kaydırma işlevselliğini başlat
    initSkillAnimations(); // Yetenek animasyonlarını başlat
    addScrollIndicator(); // Aşağı kaydırma göstergesini ekle
    addGradientTextEffect(); // Gradyan metin efektini ekle
    addRevealAnimations(); // Açılış animasyonlarını ekle
    enhanceFormInteractions(); // Form etkileşimlerini geliştir
    enhanceProjectCards(); // Proje kartlarını geliştir
    enhanceContactInfo(); // İletişim bilgilerini geliştir
    initParallaxEffect(); // Parallax efektini başlat
    initTextAnimations(); // Metin animasyonlarını başlat

    // Bölüm görünürlük gözlemcisini oluştur
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('section').forEach(section => {
        section.classList.add('visible');
    });

    // İlerleme çubuklarını hemen ayarla
    setProgressBarsImmediately();
});

// İlerleme çubuklarını belirlenen değerlere ayarla
function setProgressBarsImmediately() {
    const allSkills = document.querySelectorAll('.skill');
    
    allSkills.forEach(skill => {
        if (skill.getAttribute('data-progress-set')) return;
        
        const progressBar = skill.querySelector('.progress-bar');
        const percentageSpan = skill.querySelector('.skill-percentage');
        
        if (progressBar && percentageSpan) {
            const percentage = percentageSpan.textContent;
            requestAnimationFrame(() => {
                progressBar.style.width = percentage;
            });
            skill.setAttribute('data-progress-set', 'true');
        }
    });
}

// Yetenek animasyonlarını başlat
function initSkillAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                requestAnimationFrame(() => {
                    setProgressBarsImmediately();
                });
                observer.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0.1,
        rootMargin: '50px'
    });

    const skillsContainer = document.querySelector('.skills');
    if (skillsContainer) {
        observer.observe(skillsContainer);
    }
}

// Bir elementin görünür olup olmadığını kontrol et
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.bottom >= 0
    );
}

// Sayfa yükleme ve kaydırma olaylarını dinle
window.addEventListener('load', function () {
    setTimeout(setProgressBarsImmediately, 100);
});

// Yüzen efektleri ekle
function addFloatingEffect() {
    const elementsToFloat = [
        { selector: '.hero h1', className: 'floating-slow' },
        { selector: '.hero p', className: 'floating' },
        { selector: '.hero .btn-primary', className: 'floating-fast' },
        { selector: '.about h2', className: 'floating' }
    ];

    elementsToFloat.forEach(item => {
        const elements = document.querySelectorAll(item.selector);
        elements.forEach(el => el.classList.add(item.className));
    });
}

// Yumuşak kaydırma işlevselliğini başlat
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 50,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Aşağı kaydırma göstergesini ekle
function addScrollIndicator() {
    const heroSection = document.querySelector('.hero');
    const existingIndicator = document.querySelector('.scroll-down');

    if (heroSection && !existingIndicator) {
        const scrollIndicator = document.createElement('div');
        scrollIndicator.className = 'scroll-down';
        scrollIndicator.innerHTML = '<i class="fas fa-chevron-down fa-2x pulse"></i>';
        heroSection.appendChild(scrollIndicator);

        scrollIndicator.addEventListener('click', function () {
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

// Gradyan metin efektini ekle
function addGradientTextEffect() {
    const headings = document.querySelectorAll('h2, .highlight');
    headings.forEach(heading => {
        if (!heading.classList.contains('gradient-text')) {
            heading.classList.add('gradient-text');
        }
    });
}

// Açılış animasyonlarını ekle
function addRevealAnimations() {
    const sections = document.querySelectorAll('section');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('revealed')) {
                entry.target.classList.add('revealed', 'reveal');
            }
        });
    }, { threshold: 0.1 });

    sections.forEach(section => observer.observe(section));
}

// Form etkileşimlerini geliştir
function enhanceFormInteractions() {
    const formInputs = document.querySelectorAll('.form-control');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
}

// Proje kartlarını geliştir
function enhanceProjectCards() {
    const projectCards = document.querySelectorAll('.project-card');

    projectCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05) translateY(-10px)';
            this.style.boxShadow = '0 15px 30px rgba(201, 160, 255, 0.3)';

            const img = this.querySelector('img');
            if (img) img.style.transform = 'scale(1.1)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = '';
            this.style.boxShadow = '';

            const img = this.querySelector('img');
            if (img) img.style.transform = '';
        });
    });
}

// İletişim bilgilerini geliştir
function enhanceContactInfo() {
    const contactItems = document.querySelectorAll('.contact-info .d-flex');

    contactItems.forEach(item => {
        item.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.icon-box');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.background = 'var(--gradient-primary)';
                icon.style.color = 'white';
            }
        });

        item.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.icon-box');
            if (icon) {
                icon.style.transform = '';
                icon.style.background = '';
                icon.style.color = '';
            }
        });
    });
}

// Parallax efektini başlat
function initParallaxEffect() {
    window.addEventListener('scroll', function () {
        const scrollY = window.scrollY || window.pageYOffset;

        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            heroSection.style.backgroundPositionY = scrollY * 0.5 + 'px';
        }
    });
}

// Metin animasyonlarını başlat
function initTextAnimations() {
    const bouncingTextElement = document.querySelector('.bouncing-text');

    if (bouncingTextElement) {
        const originalText = bouncingTextElement.innerHTML;

        const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
        const matches = [...originalText.matchAll(highlightRegex)];

        if (matches.length > 0) {
            const beforeHighlight = originalText.split(matches[0][0])[0];
            const afterHighlight = originalText.split(matches[matches.length - 1][0])[1];

            let newHtml = '';

            for (let i = 0; i < beforeHighlight.length; i++) {
                const char = beforeHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${i * 0.05}s">${char}</span>`;
                }
            }

            matches.forEach(match => {
                newHtml += `<span class="${match[1]}">${match[2]}</span>`;
            });

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
    }
}