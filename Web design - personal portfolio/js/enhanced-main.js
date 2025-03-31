// Performans optimizasyonu için modüler yapı
const App = {
    // Uygulama başlatma
    init() {
        this.initAOS();
        this.initCriticalFeatures();
        this.initSecondaryFeatures();
        this.observeSections();
    },

    // AOS başlatma
    initAOS() {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50,
            disable: window.innerWidth < 768
        });
    },

    // Kritik özellikleri başlat
    initCriticalFeatures() {
        requestAnimationFrame(() => {
            this.SmoothScroll.init();
            this.SkillManager.init();
            this.ScrollIndicator.init();
        });
    },

    // İkincil özellikleri başlat
    initSecondaryFeatures() {
        setTimeout(() => {
            this.FloatingEffect.init();
            this.GradientText.init();
            this.RevealAnimations.init();
            this.FormInteractions.init();
            this.ProjectCards.init();
            this.ContactInfo.init();
            this.ParallaxEffect.init();
            this.TextAnimations.init();
        }, 100);
    },

    // Bölümleri gözlemle
    observeSections() {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            },
            { threshold: 0.1 }
        );

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    },

    // Yetenek yönetimi modülü
    SkillManager: {
        skills: {
            software: [
                { name: 'HTML/CSS', percentage: '80%' },
                { name: 'JavaScript', percentage: '60%' },
                { name: 'PHP', percentage: '50%' },
                { name: 'Python', percentage: '45%' },
                { name: 'C#', percentage: '30%' },
                { name: 'Java', percentage: '20%' }
            ],
            system: [
                { name: 'MS Windows', percentage: '95%' },
                { name: 'MS Office', percentage: '80%' },
                { name: 'Donanım', percentage: '70%' },
                { name: 'Ağ Sistemleri', percentage: '60%' },
                { name: 'Robotik Sistemler', percentage: '40%' },
                { name: 'Yapay Zeka', percentage: '20%' }
            ]
        },

        init() {
            this.initializeProgressBars();
            this.initializeSkillAnimations();
        },

        initializeProgressBars() {
            const allSkills = document.querySelectorAll('.skill');
            
            allSkills.forEach(skill => {
                if (skill.getAttribute('data-progress-set') === 'true') return;

                const skillName = skill.querySelector('.d-flex span:first-child')?.textContent.trim();
                const progressBar = skill.querySelector('.progress-bar');
                const percentageSpan = skill.querySelector('.skill-percentage');
                
                const matchedSkill = [...this.skills.software, ...this.skills.system]
                    .find(s => s.name === skillName);
                
                if (matchedSkill && progressBar) {
                    requestAnimationFrame(() => {
                        progressBar.style.width = matchedSkill.percentage;
                        if (percentageSpan) {
                            percentageSpan.textContent = matchedSkill.percentage;
                        }
                        skill.setAttribute('data-progress-set', 'true');
                    });
                }
            });

            document.querySelectorAll('.progress-bar[data-percentage]').forEach(bar => {
                if (bar.getAttribute('data-progress-set') === 'true') return;
                
                const percentage = bar.getAttribute('data-percentage');
                requestAnimationFrame(() => {
                    bar.style.width = `${percentage}%`;
                    bar.setAttribute('data-progress-set', 'true');
                });
            });
        },

        initializeSkillAnimations() {
            document.querySelectorAll('.skill').forEach(skill => {
                skill.classList.add('animated');
            });
        }
    },

    // Yumuşak kaydırma modülü
    SmoothScroll: {
        init() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', this.handleClick.bind(this));
            });
        },

        handleClick(e) {
            e.preventDefault();
            const targetId = e.currentTarget.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 50,
                    behavior: 'smooth'
                });
            }
        }
    },

    // Kaydırma göstergesi modülü
    ScrollIndicator: {
        init() {
            const heroSection = document.querySelector('.hero');
            if (!heroSection || document.querySelector('.scroll-down')) return;

            const indicator = document.createElement('div');
            indicator.className = 'scroll-down';
            indicator.innerHTML = '<i class="fas fa-chevron-down fa-2x pulse"></i>';
            
            indicator.addEventListener('click', () => {
                const aboutSection = document.querySelector('#about');
                if (aboutSection) {
                    window.scrollTo({
                        top: aboutSection.offsetTop,
                        behavior: 'smooth'
                    });
                }
            });

            heroSection.appendChild(indicator);
        }
    },

    // Yüzen efekt modülü
    FloatingEffect: {
        init() {
            const elements = [
                { selector: '.hero h1', className: 'floating-slow' },
                { selector: '.hero p', className: 'floating' },
                { selector: '.hero .btn-primary', className: 'floating-fast' },
                { selector: '.about h2', className: 'floating' }
            ];

            elements.forEach(({ selector, className }) => {
                document.querySelectorAll(selector).forEach(el => {
                    el.classList.add(className);
                });
            });
        }
    },

    // Gradyan metin modülü
    GradientText: {
        init() {
            document.querySelectorAll('h2, .highlight').forEach(heading => {
                if (!heading.classList.contains('gradient-text')) {
                    heading.classList.add('gradient-text');
                }
            });
        }
    },

    // Açılış animasyonları modülü
    RevealAnimations: {
        init() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !entry.target.classList.contains('revealed')) {
                            entry.target.classList.add('revealed', 'reveal');
                        }
                    });
                },
                { threshold: 0.1 }
            );

            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
        }
    },

    // Form etkileşimleri modülü
    FormInteractions: {
        init() {
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', () => {
                    if (input.value === '') {
                        input.parentElement.classList.remove('focused');
                    }
                });
            });
        }
    },

    // Proje kartları modülü
    ProjectCards: {
        init() {
            document.querySelectorAll('.project-card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    requestAnimationFrame(() => {
                        card.style.transform = 'scale(1.05) translateY(-10px)';
                        card.style.boxShadow = '0 15px 30px rgba(201, 160, 255, 0.3)';
                    });
                });

                card.addEventListener('mouseleave', () => {
                    requestAnimationFrame(() => {
                        card.style.transform = '';
                        card.style.boxShadow = '';
                    });
                });
            });
        }
    },

    // İletişim bilgileri modülü
    ContactInfo: {
        init() {
            document.querySelectorAll('.contact-info .d-flex').forEach(item => {
                const icon = item.querySelector('.icon-box');
                if (!icon) return;

                item.addEventListener('mouseenter', () => {
                    requestAnimationFrame(() => {
                        icon.style.transform = 'scale(1.1) rotate(5deg)';
                        icon.style.background = 'var(--gradient-primary)';
                        icon.style.color = 'white';
                    });
                });

                item.addEventListener('mouseleave', () => {
                    requestAnimationFrame(() => {
                        icon.style.transform = '';
                        icon.style.background = '';
                        icon.style.color = '';
                    });
                });
            });
        }
    },

    // Parallax efekt modülü
    ParallaxEffect: {
        init() {
            let ticking = false;
            const heroSection = document.querySelector('.hero');
            
            if (!heroSection) return;

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        const scrollY = window.scrollY || window.pageYOffset;
                        heroSection.style.backgroundPositionY = `${scrollY * 0.5}px`;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }
    },

    // Metin animasyonları modülü
    TextAnimations: {
        init() {
            const element = document.querySelector('.bouncing-text');
            if (!element) return;

            const text = element.innerHTML;
            const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
            const matches = [...text.matchAll(highlightRegex)];

            if (matches.length === 0) return;

            const beforeHighlight = text.split(matches[0][0])[0];
            const afterHighlight = text.split(matches[matches.length - 1][0])[1];

            let newHtml = this.processText(beforeHighlight, 0);
            matches.forEach(match => {
                newHtml += `<span class="${match[1]}">${match[2]}</span>`;
            });
            newHtml += this.processText(afterHighlight, beforeHighlight.length);

            element.innerHTML = newHtml;
        },

        processText(text, startIndex) {
            return text.split('').map((char, i) => {
                return char === ' ' ? ' ' : 
                    `<span style="animation-delay: ${(startIndex + i) * 0.05}s">${char}</span>`;
            }).join('');
        }
    }
};

// Uygulamayı başlat
document.addEventListener('DOMContentLoaded', () => App.init());