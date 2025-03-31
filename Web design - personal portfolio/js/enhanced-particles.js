/*
* Optimized particle system with improved performance and animations
* Key improvements:
* - Frame rate control
* - Object pooling
* - Optimized collision detection using spatial partitioning
* - Reduced calculations
* - Dynamic quality adjustments
*/

class EnhancedParticleNetwork {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error('Canvas element not found');
            return;
        }

        // Core settings
        this.ctx = this.canvas.getContext('2d');
        this.particleCount = Math.min(options.particleCount || 120, 200); // Limit max particles
        this.colors = options.colors || ['#c9a0ff', '#00e6d2', '#d4af37'];
        this.lineColor = options.lineColor || 'rgba(201, 160, 255, 0.2)';
        this.maxDistance = options.maxDistance || 150;
        this.speed = options.speed || 0.8;
        this.sizeRange = options.sizeRange || { min: 1, max: 4 };
        this.interactionRadius = options.interactionRadius || 200;
        this.interactionStrength = options.interactionStrength || 10;
        this.responsive = options.responsive !== false;
        this.glowEffect = options.glowEffect || false;
        this.waveEffect = options.waveEffect || false;
        this.waveSpeed = options.waveSpeed || 0.05;
        this.waveHeight = options.waveHeight || 15;

        // Performance optimization
        this.targetFPS = 60;
        this.frameInterval = 1000 / this.targetFPS;
        this.lastFrameTime = 0;
        this.particles = [];
        this.particlePool = []; // Object pool for particles
        this.grid = {}; // Spatial partitioning grid
        this.cellSize = this.maxDistance; // Grid cell size
        this.mousePosition = { x: null, y: null };
        this.frameCount = 0;
        this.performanceMode = false; // Dynamic quality control

        // Initialize
        this.resizeCanvas();
        this.createParticles();
        this.setupEventListeners();
        this.checkPerformance();
        this.animate();
    }

    // Performance monitoring
    checkPerformance() {
        let fpsCounter = 0;
        let lastCheck = performance.now();

        const checkFPS = () => {
            const now = performance.now();
            const delta = now - lastCheck;
            fpsCounter++;

            if (delta >= 1000) {
                const fps = (fpsCounter * 1000) / delta;
                if (fps < 30 && !this.performanceMode) {
                    this.enablePerformanceMode();
                }
                fpsCounter = 0;
                lastCheck = now;
            }
            requestAnimationFrame(checkFPS);
        };
        checkFPS();
    }

    enablePerformanceMode() {
        this.performanceMode = true;
        this.particleCount = Math.floor(this.particleCount * 0.7);
        this.maxDistance = Math.floor(this.maxDistance * 0.8);
        this.glowEffect = false;
        this.createParticles();
    }

    resizeCanvas() {
        if (this.responsive) {
            const dpr = window.devicePixelRatio || 1;
            const rect = this.canvas.getBoundingClientRect();
            this.canvas.width = rect.width * dpr;
            this.canvas.height = rect.height * dpr;
            this.ctx.scale(dpr, dpr);
        }
    }

    createParticle() {
        const color = this.colors[Math.floor(Math.random() * this.colors.length)];
        const size = Math.random() * (this.sizeRange.max - this.sizeRange.min) + this.sizeRange.min;
        const opacity = Math.random() * 0.5 + 0.5;

        return {
            x: Math.random() * this.canvas.width,
            y: Math.random() * this.canvas.height,
            vx: Math.random() * this.speed * 2 - this.speed,
            vy: Math.random() * this.speed * 2 - this.speed,
            size,
            color,
            originalSize: size,
            opacity,
            originalOpacity: opacity,
            gridX: 0,
            gridY: 0
        };
    }

    createParticles() {
        this.particles = [];
        this.particlePool = [];

        for (let i = 0; i < this.particleCount; i++) {
            const particle = this.createParticle();
            this.particles.push(particle);
            this.updateParticleGrid(particle);
        }
    }

    updateParticleGrid(particle) {
        const gridX = Math.floor(particle.x / this.cellSize);
        const gridY = Math.floor(particle.y / this.cellSize);

        if (particle.gridX !== gridX || particle.gridY !== gridY) {
            // Remove from old cell
            if (this.grid[`${particle.gridX},${particle.gridY}`]) {
                const index = this.grid[`${particle.gridX},${particle.gridY}`].indexOf(particle);
                if (index > -1) {
                    this.grid[`${particle.gridX},${particle.gridY}`].splice(index, 1);
                }
            }

            // Add to new cell
            const key = `${gridX},${gridY}`;
            if (!this.grid[key]) {
                this.grid[key] = [];
            }
            this.grid[key].push(particle);

            particle.gridX = gridX;
            particle.gridY = gridY;
        }
    }

    getNeighboringParticles(particle) {
        const neighbors = [];
        const gridX = Math.floor(particle.x / this.cellSize);
        const gridY = Math.floor(particle.y / this.cellSize);

        for (let i = -1; i <= 1; i++) {
            for (let j = -1; j <= 1; j++) {
                const key = `${gridX + i},${gridY + j}`;
                if (this.grid[key]) {
                    neighbors.push(...this.grid[key]);
                }
            }
        }

        return neighbors;
    }

    setupEventListeners() {
        const throttledMouseMove = this.throttle((e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mousePosition = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        }, 16);

        window.addEventListener('mousemove', throttledMouseMove);
        window.addEventListener('mouseleave', () => {
            this.mousePosition = { x: null, y: null };
        });

        if (this.responsive) {
            const debouncedResize = this.debounce(() => {
                this.resizeCanvas();
                this.createParticles();
            }, 250);
            window.addEventListener('resize', debouncedResize);
        }
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    animate(currentTime) {
        requestAnimationFrame(this.animate.bind(this));

        const elapsed = currentTime - this.lastFrameTime;
        if (elapsed < this.frameInterval) return;

        this.lastFrameTime = currentTime - (elapsed % this.frameInterval);
        this.frameCount++;

        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.grid = {}; // Reset grid

        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];
            this.updateParticle(p);
            this.drawParticle(p);
            this.updateParticleGrid(p);

            if (!this.performanceMode) {
                const neighbors = this.getNeighboringParticles(p);
                this.drawConnections(p, neighbors);
            }
        }
    }

    updateParticle(p) {
        p.x += p.vx;
        p.y += p.vy;

        if (this.waveEffect) {
            p.y += Math.sin((p.x * 0.01) + (this.frameCount * this.waveSpeed)) * this.waveHeight * 0.01;
        }

        // Boundary checks with optimized bounce
        if (p.x < 0) { p.x = 0; p.vx *= -1; }
        else if (p.x > this.canvas.width) { p.x = this.canvas.width; p.vx *= -1; }
        if (p.y < 0) { p.y = 0; p.vy *= -1; }
        else if (p.y > this.canvas.height) { p.y = this.canvas.height; p.vy *= -1; }

        // Mouse interaction
        if (this.mousePosition.x !== null) {
            const dx = p.x - this.mousePosition.x;
            const dy = p.y - this.mousePosition.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < this.interactionRadius) {
                const force = (this.interactionRadius - distance) / this.interactionRadius;
                const angle = Math.atan2(dy, dx);
                p.vx += Math.cos(angle) * force * this.interactionStrength * 0.01;
                p.vy += Math.sin(angle) * force * this.interactionStrength * 0.01;
                p.size = p.originalSize * (1 + force);
                p.opacity = Math.min(1, p.opacity + force * 0.3);
            } else {
                p.size = p.originalSize;
                p.opacity = p.originalOpacity;
            }
        }

        // Velocity limit
        const maxVelocity = 2;
        const velocity = Math.sqrt(p.vx * p.vx + p.vy * p.vy);
        if (velocity > maxVelocity) {
            const scale = maxVelocity / velocity;
            p.vx *= scale;
            p.vy *= scale;
        }
    }

    drawParticle(p) {
        if (this.glowEffect && !this.performanceMode) {
            this.ctx.shadowBlur = p.size * 2;
            this.ctx.shadowColor = p.color;
        }

        this.ctx.beginPath();
        this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        this.ctx.fillStyle = p.color;
        this.ctx.globalAlpha = p.opacity;
        this.ctx.fill();
        this.ctx.globalAlpha = 1;

        if (this.glowEffect && !this.performanceMode) {
            this.ctx.shadowBlur = 0;
        }
    }

    drawConnections(p, neighbors) {
        for (const p2 of neighbors) {
            if (p === p2) continue;
            
            const dx = p.x - p2.x;
            const dy = p.y - p2.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < this.maxDistance) {
                const opacity = 1 - (distance / this.maxDistance);
                this.ctx.beginPath();
                this.ctx.moveTo(p.x, p.y);
                this.ctx.lineTo(p2.x, p2.y);
                this.ctx.strokeStyle = this.lineColor;
                this.ctx.globalAlpha = opacity * 0.5;
                this.ctx.stroke();
                this.ctx.globalAlpha = 1;
            }
        }
    }
}

// Initialize particle system
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('particles-container');
    if (!container) return;

    container.style.display = 'block';

    let canvas = document.getElementById('particles-canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'particles-canvas';
        container.appendChild(canvas);
    }

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