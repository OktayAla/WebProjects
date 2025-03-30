// Dynamic Interactions JS - Enhanced Website Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dynamic interactions
    initDynamicInteractions();
});

function initDynamicInteractions() {
    // Initialize magnetic elements
    initMagneticElements();
    
    // Initialize parallax effects
    initParallaxEffects();
    
    // Initialize scroll progress indicator
    initScrollProgress();
    
    // Initialize cursor follower
    initCursorFollower();
    
    // Initialize reveal text animations
    initRevealTextAnimations();
    
    // Initialize enhanced skill animations
    initEnhancedSkillAnimations();
    
    // Initialize enhanced project card interactions
    initEnhancedProjectCards();
    
    // Initialize form animations
    initFormAnimations();
    
    // Initialize scroll-triggered animations
    initScrollTriggeredAnimations();
}

// Magnetic Elements Effect
function initMagneticElements() {
    const magneticElements = document.querySelectorAll('.magnetic-element');
    
    magneticElements.forEach(element => {
        element.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            // Calculate distance from center
            const distance = Math.sqrt(x * x + y * y);
            const maxDistance = Math.sqrt(rect.width * rect.width + rect.height * rect.height) / 2;
            
            // Calculate movement strength (stronger when closer to center)
            const strength = 15 * (1 - distance / maxDistance);
            
            // Apply transform
            this.style.transform = `translate(${x * strength / 10}px, ${y * strength / 10}px) rotateX(${y * strength / 50}deg) rotateY(${-x * strength / 50}deg)`;
        });
        
        element.addEventListener('mouseleave', function() {
            // Reset transform
            this.style.transform = '';
        });
    });
}

// Parallax Effects
function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    window.addEventListener('scroll', function() {
        const scrollY = window.scrollY || window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-speed') || 0.5;
            element.style.backgroundPositionY = `${scrollY * speed}px`;
        });
    });
    
    // Add parallax effect to sections with background
    document.querySelectorAll('section[id]').forEach(section => {
        if (getComputedStyle(section).backgroundImage !== 'none') {
            section.classList.add('parallax-bg');
            section.setAttribute('data-speed', '0.3');
        }
    });
}

// Scroll Progress Indicator
function initScrollProgress() {
    // Create scroll progress element if it doesn't exist
    if (!document.querySelector('.scroll-progress')) {
        const scrollProgress = document.createElement('div');
        scrollProgress.className = 'scroll-progress';
        document.body.appendChild(scrollProgress);
    }
    
    // Update scroll progress on scroll
    window.addEventListener('scroll', function() {
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercentage = (scrollTop / scrollHeight) * 100;
        
        document.querySelector('.scroll-progress').style.width = `${scrollPercentage}%`;
    });
}

// Cursor Follower Effect
function initCursorFollower() {
    // Create cursor follower element if it doesn't exist
    if (!document.querySelector('.cursor-follower')) {
        const cursorFollower = document.createElement('div');
        cursorFollower.className = 'cursor-follower';
        document.body.appendChild(cursorFollower);
    }
    
    const cursor = document.querySelector('.cursor-follower');
    
    // Update cursor position on mouse move
    document.addEventListener('mousemove', function(e) {
        cursor.style.left = `${e.clientX}px`;
        cursor.style.top = `${e.clientY}px`;
    });
    
    // Enlarge cursor on hoverable elements
    document.querySelectorAll('a, button, .btn, .project-card, .skill').forEach(element => {
        element.addEventListener('mouseenter', function() {
            cursor.style.transform = 'translate(-50%, -50%) scale(1.5)';
            cursor.style.background = 'rgba(138, 43, 226, 0.3)';
        });
        
        element.addEventListener('mouseleave', function() {
            cursor.style.transform = 'translate(-50%, -50%) scale(1)';
            cursor.style.background = 'rgba(138, 43, 226, 0.2)';
        });
    });
}

// Reveal Text Animations
function initRevealTextAnimations() {
    const revealTexts = document.querySelectorAll('.reveal-text');
    
    // Create observer for reveal text elements
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                
                // Add staggered animation to each character
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
    
    // Observe each reveal text element
    revealTexts.forEach(text => observer.observe(text));
    
    // Add reveal-text class to section titles if not already present
    document.querySelectorAll('h2.section-title:not(.reveal-text)').forEach(title => {
        title.classList.add('reveal-text');
        observer.observe(title);
    });
}

// Enhanced Skill Animations
function initEnhancedSkillAnimations() {
    const skills = document.querySelectorAll('.skill');
    
    // Create observer for skill elements
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add animated class with delay based on index
                const index = Array.from(skills).indexOf(entry.target);
                entry.target.style.transitionDelay = `${index * 0.1}s`;
                entry.target.classList.add('animated');
                
                // Animate progress bar
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
    
    // Observe each skill element
    skills.forEach(skill => observer.observe(skill));
}

// Enhanced Project Card Interactions
function initEnhancedProjectCards() {
    const projectCards = document.querySelectorAll('.project-card');
    
    projectCards.forEach(card => {
        // Add 3D tilt effect on mouse move
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xPercent = x / rect.width - 0.5;
            const yPercent = y / rect.height - 0.5;
            
            this.style.transform = `perspective(1000px) rotateY(${xPercent * 10}deg) rotateX(${-yPercent * 10}deg) translateZ(10px)`;
            
            // Move image slightly for parallax effect
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = `translateX(${-xPercent * 20}px) translateY(${-yPercent * 20}px) scale(1.1)`;
            }
        });
        
        // Reset on mouse leave
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = '';
            }
        });
    });
}

// Form Animations
function initFormAnimations() {
    const formControls = document.querySelectorAll('.form-control');
    
    formControls.forEach(control => {
        // Add focus animation
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        // Remove focus animation if empty
        control.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input already has value on page load
        if (control.value !== '') {
            control.parentElement.classList.add('focused');
        }
    });
    
    // Add submit button animation
    const submitButton = document.querySelector('form button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('mouseenter', function() {
            this.classList.add('pulse');
        });
        
        submitButton.addEventListener('mouseleave', function() {
            this.classList.remove('pulse');
        });
    }
}

// Scroll-Triggered Animations
function initScrollTriggeredAnimations() {
    // Create observer for sections
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });
    
    // Observe each section
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });
    
    // Add scroll-triggered class to elements
    document.querySelectorAll('.hero h1, .hero p, .about p, .contact h2').forEach(element => {
        element.classList.add('scroll-triggered');
        observer.observe(element);
    });
}