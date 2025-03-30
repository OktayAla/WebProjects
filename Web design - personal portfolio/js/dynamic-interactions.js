/*
* Bu JavaScript dosyası, web sitesindeki tüm dinamik etkileşimleri ve animasyonları yönetir.
* İçerdiği temel işlevler:
* - Manyetik element efektleri (elementlerin fareyi takip etmesi)
* - Parallax kaydırma efektleri
* - Sayfa kaydırma ilerleme çubuğu
* - Özelleştirilmiş fare imleci takipçisi
* - Metin görünüm animasyonları
* - Yetenek çubukları animasyonları
* - Proje kartları için 3D hover efektleri
* - Form etkileşim animasyonları
* - Sayfa kaydırma tabanlı animasyonlar
*/

// Sayfa yüklendiğinde dinamik etkileşimleri başlat
document.addEventListener('DOMContentLoaded', function () {
    initDynamicInteractions();
});

// Tüm dinamik etkileşimleri başlatan ana fonksiyon
function initDynamicInteractions() {
    initMagneticElements();
    initParallaxEffects();
    initScrollProgress();
    initCursorFollower();
    initRevealTextAnimations();
    initEnhancedSkillAnimations();
    initEnhancedProjectCards();
    initFormAnimations();
    initScrollTriggeredAnimations();
}

// Manyetik element efektlerini başlat
function initMagneticElements() {
    const magneticElements = document.querySelectorAll('.magnetic-element');

    // Her manyetik element için mouse takibi ve çekim efekti
    magneticElements.forEach(element => {
        element.addEventListener('mousemove', function (e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            const distance = Math.sqrt(x * x + y * y);
            const maxDistance = Math.sqrt(rect.width * rect.width + rect.height * rect.height) / 2;

            const strength = 15 * (1 - distance / maxDistance);

            this.style.transform = `translate(${x * strength / 10}px, ${y * strength / 10}px) rotateX(${y * strength / 50}deg) rotateY(${-x * strength / 50}deg)`;
        });

        element.addEventListener('mouseleave', function () {
            this.style.transform = '';
        });
    });
}

// Parallax efektlerini başlat
function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.parallax-bg');

    // Kaydırma olayını dinle ve arka plan pozisyonunu güncelle
    window.addEventListener('scroll', function () {
        const scrollY = window.scrollY || window.pageYOffset;

        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-speed') || 0.5;
            element.style.backgroundPositionY = `${scrollY * speed}px`;
        });
    });

    document.querySelectorAll('section[id]').forEach(section => {
        if (getComputedStyle(section).backgroundImage !== 'none') {
            section.classList.add('parallax-bg');
            section.setAttribute('data-speed', '0.3');
        }
    });
}

// Kaydırma ilerleme çubuğunu başlat
function initScrollProgress() {
    // İlerleme çubuğunu oluştur (eğer yoksa)
    if (!document.querySelector('.scroll-progress')) {
        const scrollProgress = document.createElement('div');
        scrollProgress.className = 'scroll-progress';
        document.body.appendChild(scrollProgress);
    }

    window.addEventListener('scroll', function () {
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercentage = (scrollTop / scrollHeight) * 100;

        document.querySelector('.scroll-progress').style.width = `${scrollPercentage}%`;
    });
}

// İmleç takipçisini başlat
function initCursorFollower() {
    // İmleç takipçisi elementini oluştur (eğer yoksa)
    if (!document.querySelector('.cursor-follower')) {
        const cursorFollower = document.createElement('div');
        cursorFollower.className = 'cursor-follower';
        document.body.appendChild(cursorFollower);
    }

    const cursor = document.querySelector('.cursor-follower');

    // Mouse hareketini takip et
    document.addEventListener('mousemove', function (e) {
        cursor.style.left = `${e.clientX}px`;
        cursor.style.top = `${e.clientY}px`;
    });

    document.querySelectorAll('a, button, .btn, .project-card, .skill').forEach(element => {
        element.addEventListener('mouseenter', function () {
            cursor.style.transform = 'translate(-50%, -50%) scale(1.5)';
            cursor.style.background = 'rgba(138, 43, 226, 0.3)';
        });

        element.addEventListener('mouseleave', function () {
            cursor.style.transform = 'translate(-50%, -50%) scale(1)';
            cursor.style.background = 'rgba(138, 43, 226, 0.2)';
        });
    });
}

// Metin açılış animasyonlarını başlat
function initRevealTextAnimations() {
    const revealTexts = document.querySelectorAll('.reveal-text');

    // Görünürlük gözlemcisi oluştur
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');

                const text = entry.target.textContent;
                let newHtml = '';

                for (let i = 0; i < text.length; i++) {
                    if (text[i] === ' ') {
                        newHtml += ' ';
                    } else {
                        newHtml += `<span class="stagger-item" style="transition-delay: ${i * 0.03}s">${text[i]}</span>`;
                    }
                }

                entry.target.innerHTML = newHtml;
            }
        });
    }, { threshold: 0.2 });

    revealTexts.forEach(text => observer.observe(text));

    document.querySelectorAll('h2.section-title:not(.reveal-text)').forEach(title => {
        title.classList.add('reveal-text');
        observer.observe(title);
    });
}

// Gelişmiş yetenek animasyonlarını başlat
function initEnhancedSkillAnimations() {
    const skills = document.querySelectorAll('.skill');

    // Görünürlük gözlemcisi ile yetenek animasyonlarını tetikle
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const index = Array.from(skills).indexOf(entry.target);
                entry.target.style.transitionDelay = `${index * 0.1}s`;
                entry.target.classList.add('animated');

                const progressBar = entry.target.querySelector('.progress-bar');
                if (progressBar) {
                    const width = progressBar.style.width;
                    progressBar.style.width = '0%';

                    setTimeout(() => {
                        progressBar.style.width = width;
                    }, 300 + index * 100);
                }
            }
        });
    }, { threshold: 0.2 });

    skills.forEach(skill => observer.observe(skill));
}

// Gelişmiş proje kartı etkileşimlerini başlat
function initEnhancedProjectCards() {
    const projectCards = document.querySelectorAll('.project-card');

    // 3D hover efektlerini ekle
    projectCards.forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const xPercent = x / rect.width - 0.5;
            const yPercent = y / rect.height - 0.5;

            this.style.transform = `perspective(1000px) rotateY(${xPercent * 10}deg) rotateX(${-yPercent * 10}deg) translateZ(10px)`;

            const img = this.querySelector('img');
            if (img) {
                img.style.transform = `translateX(${-xPercent * 20}px) translateY(${-yPercent * 20}px) scale(1.1)`;
            }
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = '';

            const img = this.querySelector('img');
            if (img) {
                img.style.transform = '';
            }
        });
    });
}

// Form animasyonlarını başlat
function initFormAnimations() {
    const formControls = document.querySelectorAll('.form-control');

    // Form elemanları için odaklanma efektleri
    formControls.forEach(control => {
        control.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        control.addEventListener('blur', function () {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });

        if (control.value !== '') {
            control.parentElement.classList.add('focused');
        }
    });

    const submitButton = document.querySelector('form button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('mouseenter', function () {
            this.classList.add('pulse');
        });

        submitButton.addEventListener('mouseleave', function () {
            this.classList.remove('pulse');
        });
    }
}

// Kaydırma tetiklemeli animasyonları başlat
function initScrollTriggeredAnimations() {
    // Görünürlük gözlemcisi ile elementleri izle
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });

    // Animasyonlu elementleri belirle ve gözlemciyi ekle
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });

    document.querySelectorAll('.hero h1, .hero p, .about p, .contact h2').forEach(element => {
        element.classList.add('scroll-triggered');
        observer.observe(element);
    });
}