// Enhanced Particle Network Animation
// Based on the original particles.js but with improved performance and visual effects

class EnhancedParticleNetwork {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error('Canvas element not found');
            return;
        }
        
        
        this.ctx = this.canvas.getContext('2d');
        this.particleCount = options.particleCount || 120;
        this.colors = options.colors || ['#c9a0ff', '#00e6d2', '#d4af37'];
        this.lineColor = options.lineColor || 'rgba(201, 160, 255, 0.2)';
        this.maxDistance = options.maxDistance || 150;
        this.speed = options.speed || 0.8;
        this.sizeRange = options.sizeRange || { min: 1, max: 4 };
        this.particles = [];
        this.mousePosition = {
            x: null,
            y: null
        };
        this.interactionRadius = options.interactionRadius || 200;
        this.interactionStrength = options.interactionStrength || 10;
        this.responsive = options.responsive !== false;
        this.glowEffect = options.glowEffect || false;
        this.waveEffect = options.waveEffect || false;
        this.waveSpeed = options.waveSpeed || 0.05;
        this.waveHeight = options.waveHeight || 15;
        this.frameCount = 0;
        
        // Initialize
        this.resizeCanvas();
        this.createParticles();
        this.setupEventListeners();
        this.animate();
    }
    
    resizeCanvas() {
        if (this.responsive) {
            // Make canvas full screen
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        }
    }
    
    createParticles() {
        this.particles = [];
        for (let i = 0; i < this.particleCount; i++) {
            const color = this.colors[Math.floor(Math.random() * this.colors.length)];
            const size = Math.random() * (this.sizeRange.max - this.sizeRange.min) + this.sizeRange.min;
            const opacity = Math.random() * 0.5 + 0.5;
            
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: Math.random() * this.speed * 2 - this.speed,
                vy: Math.random() * this.speed * 2 - this.speed,
                size: size,
                color: color,
                originalSize: size,
                opacity: opacity,
                originalOpacity: opacity
            });
        }
    }
    
    setupEventListeners() {
        // Mouse move event
        window.addEventListener('mousemove', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mousePosition = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        });
        
        // Mouse leave event
        window.addEventListener('mouseleave', () => {
            this.mousePosition = {
                x: null,
                y: null
            };
        });
        
        // Window resize event
        if (this.responsive) {
            window.addEventListener('resize', () => {
                this.resizeCanvas();
                this.createParticles();
            });
        }
        
        // Scroll event for parallax effect
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY || window.pageYOffset;
            this.particles.forEach(p => {
                p.y += scrollY * 0.01 * (Math.random() - 0.5);
                
                // Keep particles within canvas
                if (p.y < 0) p.y = this.canvas.height;
                if (p.y > this.canvas.height) p.y = 0;
            });
        });
    }
    
    animate() {
        this.frameCount++;
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Update and draw particles
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];
            
            // Move particles
            p.x += p.vx;
            p.y += p.vy;
            
            // Wave effect
            if (this.waveEffect) {
                p.y += Math.sin((p.x * 0.01) + (this.frameCount * this.waveSpeed)) * this.waveHeight * 0.01;
            }
            
            // Bounce off edges
            if (p.x < 0) {
                p.x = 0;
                p.vx *= -1;
            } else if (p.x > this.canvas.width) {
                p.x = this.canvas.width;
                p.vx *= -1;
            }
            
            if (p.y < 0) {
                p.y = 0;
                p.vy *= -1;
            } else if (p.y > this.canvas.height) {
                p.y = this.canvas.height;
                p.vy *= -1;
            }
            
            // Mouse interaction
            if (this.mousePosition.x !== null && this.mousePosition.y !== null) {
                const dx = p.x - this.mousePosition.x;
                const dy = p.y - this.mousePosition.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < this.interactionRadius) {
                    // Calculate repulsion force
                    const force = (this.interactionRadius - distance) / this.interactionRadius;
                    const angle = Math.atan2(dy, dx);
                    
                    // Apply force to particle velocity
                    p.vx += Math.cos(angle) * force * this.interactionStrength * 0.01;
                    p.vy += Math.sin(angle) * force * this.interactionStrength * 0.01;
                    
                    // Increase size when near mouse
                    p.size = p.originalSize * (1 + force * 1.5);
                    p.opacity = Math.min(1, p.opacity + force * 0.3);
                } else {
                    // Return to original size
                    p.size = p.originalSize;
                    p.opacity = p.originalOpacity;
                }
            } else {
                // Return to original size when mouse is not on canvas
                p.size = p.originalSize;
                p.opacity = p.originalOpacity;
            }
            
            // Limit velocity
            const maxVelocity = 2;
            const velocity = Math.sqrt(p.vx * p.vx + p.vy * p.vy);
            if (velocity > maxVelocity) {
                p.vx = (p.vx / velocity) * maxVelocity;
                p.vy = (p.vy / velocity) * maxVelocity;
            }
            
            // Draw particle
            this.drawParticle(p);
            
            // Draw connections
            for (let j = i + 1; j < this.particles.length; j++) {
                const p2 = this.particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < this.maxDistance) {
                    // Calculate line opacity based on distance
                    const opacity = 1 - (distance / this.maxDistance);
                    
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.strokeStyle = this.lineColor;
                    this.ctx.globalAlpha = opacity * 0.5;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.stroke();
                    this.ctx.globalAlpha = 1;
                }
            }
        }
        
        requestAnimationFrame(this.animate.bind(this));
    }

    drawParticle(particle) {
        this.ctx.beginPath();
        this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        
        if (this.glowEffect) {
            // Parlaklık efekti ekle
            this.ctx.shadowBlur = particle.size * 2;
            this.ctx.shadowColor = particle.color;
        }
        
        this.ctx.fillStyle = particle.color;
        this.ctx.globalAlpha = particle.opacity;
        this.ctx.fill();
        
        // Shadow'u sıfırla
        this.ctx.shadowBlur = 0;
    }
}

// Initialize the particle network when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create canvas element for particles
    const particlesContainer = document.getElementById('particles-container');
    if (!particlesContainer) return;
    
    // Show particles container
    particlesContainer.style.display = 'block';
    
    // Check if canvas already exists
    let canvas = document.getElementById('particles-canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'particles-canvas';
        particlesContainer.appendChild(canvas);
    }
    
    // Initialize enhanced particles
    new EnhancedParticleNetwork('particles-canvas', {
        particleCount: 150,
        colors: [
            'rgba(201, 160, 255, 0.7)',
            'rgba(0, 230, 210, 0.6)',
            'rgba(212, 175, 55, 0.5)'
        ],
        lineColor: 'rgba(201, 160, 255, 0.15)',
        maxDistance: 180,
        speed: 0.4,
        sizeRange: { min: 1, max: 4 },
        responsive: true,
        interactionRadius: 250,
        interactionStrength: 15,
        glowEffect: true,
        waveEffect: true,
        waveSpeed: 0.03,
        waveHeight: 20
    });
});