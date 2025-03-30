// Particle Network Animation
// Based on https://github.com/VincentGarreau/particles.js

class ParticleNetwork {
    constructor(canvas, options) {
        this.canvas = canvas;
        this.ctx = this.canvas.getContext('2d');
        this.particleCount = options.particleCount || 100;
        this.color = options.color || '#c9a0ff';
        this.lineColor = options.lineColor || 'rgba(201, 160, 255, 0.3)';
        this.maxDistance = options.maxDistance || 120;
        this.speed = options.speed || 1;
        this.size = options.size || 3;
        this.particles = [];
        this.width = canvas.width;
        this.height = canvas.height;
        this.mousePosition = {
            x: this.width / 2,
            y: this.height / 2
        };

        this.init();
    }

    init() {
        // Create particles
        for (let i = 0; i < this.particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.width,
                y: Math.random() * this.height,
                vx: Math.random() * this.speed * 2 - this.speed,
                vy: Math.random() * this.speed * 2 - this.speed,
                size: Math.random() * this.size + 1,
                color: this.color
            });
        }

        // Add mouse move event
        window.addEventListener('mousemove', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mousePosition = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        });

        // Start animation
        this.animate();
    }

    animate() {
        this.ctx.clearRect(0, 0, this.width, this.height);
        
        // Update and draw particles
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];
            
            // Move particles
            p.x += p.vx;
            p.y += p.vy;
            
            // Bounce off edges
            if (p.x < 0 || p.x > this.width) p.vx *= -1;
            if (p.y < 0 || p.y > this.height) p.vy *= -1;
            
            // Draw particle
            this.ctx.beginPath();
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fillStyle = p.color;
            this.ctx.fill();
            
            // Draw connections
            for (let j = i + 1; j < this.particles.length; j++) {
                const p2 = this.particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < this.maxDistance) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.strokeStyle = this.lineColor;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.stroke();
                }
            }
            
            // Connect to mouse position
            const dx = p.x - this.mousePosition.x;
            const dy = p.y - this.mousePosition.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < this.maxDistance * 1.5) {
                this.ctx.beginPath();
                this.ctx.moveTo(p.x, p.y);
                this.ctx.lineTo(this.mousePosition.x, this.mousePosition.y);
                this.ctx.strokeStyle = 'rgba(201, 160, 255, 0.15)';
                this.ctx.lineWidth = 0.5;
                this.ctx.stroke();
            }
        }
        
        requestAnimationFrame(this.animate.bind(this));
    }

    resize() {
        this.width = this.canvas.width;
        this.height = this.canvas.height;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    const heroSection = document.getElementById('home');
    if (!heroSection) return;
    
    // Create canvas element
    const canvas = document.createElement('canvas');
    canvas.id = 'particle-canvas';
    canvas.style.position = 'absolute';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.zIndex = '0'; // Z-index değerini düşürdük
    canvas.style.pointerEvents = 'none'; // Tıklamaları engellemeyi kaldır
    heroSection.appendChild(canvas);
    
    // Set canvas size
    canvas.width = heroSection.offsetWidth;
    canvas.height = heroSection.offsetHeight;
    
    // Initialize particle network
    const particleNetwork = new ParticleNetwork(canvas, {
        particleCount: 80,
        color: '#c9a0ff',
        lineColor: 'rgba(201, 160, 255, 0.2)',
        maxDistance: 150,
        speed: 0.5,
        size: 2
    });
    
    // Handle resize
    window.addEventListener('resize', () => {
        canvas.width = heroSection.offsetWidth;
        canvas.height = heroSection.offsetHeight;
        particleNetwork.resize();
    });
});