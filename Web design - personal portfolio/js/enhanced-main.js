// Enhanced Main JavaScript for Portfolio

document.addEventListener('DOMContentLoaded', function () {
    // AOS'u devre dışı bırak
    AOS.init({
        disable: true
    });

    // Tüm AOS elementlerini direkt görünür yap
    document.querySelectorAll('[data-aos]').forEach(function (el) {
        el.style.opacity = '1';
        el.style.transform = 'none';
    });

    // Add floating effect to specific elements
    addFloatingEffect();

    // Initialize smooth scrolling
    initSmoothScroll();

    // Initialize skill animations
    initSkillAnimations();

    // Add scroll indicator
    addScrollIndicator();

    // Add gradient text effect
    addGradientTextEffect();

    // Add reveal animations
    addRevealAnimations();

    // Add form animations
    enhanceFormInteractions();

    // Add project card interactions
    enhanceProjectCards();

    // Add contact info interactions
    enhanceContactInfo();

    // Add optimized parallax effect
    initParallaxEffect();

    // Initialize text animations
    initTextAnimations();

    // Section görünürlük kontrolü için IntersectionObserver ekle
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    // Tüm section'ları gözlemle
    document.querySelectorAll('section').forEach(section => {
        section.classList.add('visible');
    });

    // Progress bar'ları hemen başlat
    setProgressBarsImmediately();
});

// Progress bar'ları anında ayarla - hiçbir animasyon olmadan
function setProgressBarsImmediately() {
    // Python için
    setProgressBar('Python', '45%');
    
    // Java için
    setProgressBar('Java', '20%');
    
    // C# için
    setProgressBar('C#', '30%');
    
    // HTML/CSS için
    setProgressBar('HTML/CSS', '80%');
    
    // JavaScript için
    setProgressBar('JavaScript', '60%');
    
    // PHP için
    setProgressBar('PHP', '50%');
    
    // MS Windows için
    setProgressBar('MS Windows', '95%');
    
    // MS Office için
    setProgressBar('MS Office', '80%');
    
    // Donanım için
    setProgressBar('Donanım', '70%');
    
    // Ağ Sistemleri için
    setProgressBar('Ağ Sistemleri', '60%');
    
    // Robotik Sistemler için
    setProgressBar('Robotik Sistemler', '40%');
    
    // Yapay Zeka için
    setProgressBar('Yapay Zeka', '20%');
    
    // Data-percentage özelliği olan progress bar'ları da ayarla
    const otherProgressBars = document.querySelectorAll('.progress-bar[data-percentage]');
    otherProgressBars.forEach(bar => {
        const percentage = bar.getAttribute('data-percentage');
        bar.style.width = percentage + '%';
        bar.style.transition = "none";
    });
}

// Belirli bir beceri için progress bar'ı ayarla
function setProgressBar(skillName, percentage) {
    // Beceri adına göre elementi bul
    const skillElement = Array.from(document.querySelectorAll('.skill')).find(skill => {
        return skill.textContent.includes(skillName);
    });
    
    if (skillElement) {
        const progressBar = skillElement.querySelector('.progress-bar');
        if (progressBar) {
            // Progress bar'ı anında ayarla
            progressBar.style.transition = "none";
            progressBar.style.width = percentage;
        }
    }
}

// Initialize skill animations - IntersectionObserver'ı kaldır
function initSkillAnimations() {
    // Tüm skill elementlerini seç
    const skills = document.querySelectorAll('.skill');
    
    // Her skill için hover efektlerini koru ama progress bar animasyonunu kaldır
    skills.forEach(skill => {
        // Skill'i görünür yap
        skill.classList.add('animated');
    });
    
    // Progress bar'ları anında ayarla
    setProgressBarsImmediately();
}

// Sayfa tamamen yüklendikten sonra progress bar'ları tekrar kontrol et
window.addEventListener('load', function() {
    // Progress bar'ları anında ayarla
    setProgressBarsImmediately();
});

// Sayfa kaydırıldığında progress bar'ları tekrar ayarla
window.addEventListener('scroll', function() {
    // Progress bar'ları anında ayarla
    setProgressBarsImmediately();
});

// Add floating animation to elements - optimized to reduce DOM operations
function addFloatingEffect() {
    // Add floating class to elements that should float
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

// Initialize smooth scrolling for all anchor links - optimized for performance
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                // Smooth scroll to target
                window.scrollTo({
                    top: targetElement.offsetTop - 50,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Add scroll indicator to hero section
function addScrollIndicator() {
    const heroSection = document.querySelector('.hero');
    const existingIndicator = document.querySelector('.scroll-down');

    if (heroSection && !existingIndicator) {
        const scrollIndicator = document.createElement('div');
        scrollIndicator.className = 'scroll-down';
        scrollIndicator.innerHTML = '<i class="fas fa-chevron-down fa-2x pulse"></i>';
        heroSection.appendChild(scrollIndicator);

        // Add click event to scroll to about section
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

// Add gradient text effect to headings - optimized to reduce reflows
function addGradientTextEffect() {
    const headings = document.querySelectorAll('h2, .highlight');
    headings.forEach(heading => {
        if (!heading.classList.contains('gradient-text')) {
            heading.classList.add('gradient-text');
        }
    });
}

// Add reveal animations to sections using IntersectionObserver for better performance
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

// Enhance form interactions with optimized event handling
function enhanceFormInteractions() {
    const formInputs = document.querySelectorAll('.form-control');

    formInputs.forEach(input => {
        // Add focus effect
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        // Remove focus effect
        input.addEventListener('blur', function () {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
}

// Enhance project cards with optimized event delegation
function enhanceProjectCards() {
    const projectCards = document.querySelectorAll('.project-card');

    projectCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05) translateY(-10px)';
            this.style.boxShadow = '0 15px 30px rgba(201, 160, 255, 0.3)';

            // Enhance image
            const img = this.querySelector('img');
            if (img) img.style.transform = 'scale(1.1)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = '';
            this.style.boxShadow = '';

            // Reset image
            const img = this.querySelector('img');
            if (img) img.style.transform = '';
        });
    });
}

// Enhance contact info with hover effects
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

// Add parallax effect to sections
function initParallaxEffect() {
    window.addEventListener('scroll', function () {
        const scrollY = window.scrollY || window.pageYOffset;

        // Apply parallax effect to hero section
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            heroSection.style.backgroundPositionY = scrollY * 0.5 + 'px';
        }
    });
}

// Initialize text animations
function initTextAnimations() {
    // Get the bouncing text element
    const bouncingTextElement = document.querySelector('.bouncing-text');

    if (bouncingTextElement) {
        // Get the original text
        const originalText = bouncingTextElement.innerHTML;

        // Use a more robust approach to handle nested spans with multiple classes
        // First, extract the highlighted content with its classes
        const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
        const matches = [...originalText.matchAll(highlightRegex)];

        // If we found highlighted spans
        if (matches.length > 0) {
            // Get the text before the first highlight
            const beforeHighlight = originalText.split(matches[0][0])[0];
            // Get the text after the last highlight
            const afterHighlight = originalText.split(matches[matches.length - 1][0])[1];

            let newHtml = '';

            // Process text before highlight
            for (let i = 0; i < beforeHighlight.length; i++) {
                const char = beforeHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${i * 0.05}s">${char}</span>`;
                }
            }

            // Add the highlighted spans with their original classes
            matches.forEach(match => {
                newHtml += `<span class="${match[1]}">${match[2]}</span>`;
            });

            // Process text after highlight
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