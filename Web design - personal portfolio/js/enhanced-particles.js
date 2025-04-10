/*
* Bu JavaScript dosyası, gelişmiş parçacık sistemi için özelleştirilmiş sınıf ve işlevleri içerir.
* İçerdiği temel işlevler:
* - Çoklu renk desteği ile gelişmiş parçacık sistemi
* - Parlaklık ve dalgalanma efektleri
* - Gelişmiş fare etkileşimleri
* - Performans optimizasyonları
* - Özelleştirilebilir parçacık davranışları
*/

// Gelişmiş Parçacık Ağı sınıfı
class EnhancedParticleNetwork {
    constructor(canvasId, options = {}) {
        // Canvas elementini al ve kontrol et
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error('Canvas element not found');
            return;
        }

        // Temel ayarları başlat
        this.ctx = this.canvas.getContext('2d');
        this.particleCount = options.particleCount || 120;  // Parçacık sayısı
        this.colors = options.colors || ['#c9a0ff', '#00e6d2', '#d4af37'];  // Varsayılan renkler
        this.lineColor = options.lineColor || 'rgba(201, 160, 255, 0.2)'; // Varsayılan çizgi rengi
        this.maxDistance = options.maxDistance || 150; // Maksimum mesafe
        this.speed = options.speed || 0.8; // Parçacıkların hızı
        this.sizeRange = options.sizeRange || { min: 1, max: 4 }; // Parçacık boyut aralığı
        this.particles = []; // Parçacık dizisi
        this.mousePosition = { // Mouse pozisyonu
            x: null,
            y: null
        };
        this.interactionRadius = options.interactionRadius || 200; // Etkileşim yarıçapı
        this.interactionStrength = options.interactionStrength || 10; // Etkileşim gücü
        this.responsive = options.responsive !== false; // Responsive ayarı (varsayılan true)
        this.glowEffect = options.glowEffect || false; // Parlaklık efekti
        this.waveEffect = options.waveEffect || false; // Dalgalanma efekti
        this.waveSpeed = options.waveSpeed || 0.05; // Dalgalanma hızı
        this.waveHeight = options.waveHeight || 15; // Dalgalanma yüksekliği
        this.frameCount = 0;

        // Canvas boyutunu ayarla
        this.resizeCanvas();
        this.createParticles();
        this.setupEventListeners();
        this.animate();
    }

    // Canvas boyutunu pencere boyutuna göre ayarla
    resizeCanvas() {

        // Responsive ayarlandıysa
        if (this.responsive) {
            // Pencere boyutunu al
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        }
    }

    // Parçacıkları oluştur ve başlangıç değerlerini ata
    createParticles() {
        this.particles = [];

        // Parçacık sayısı kadar döngü yap
        for (let i = 0; i < this.particleCount; i++) {
            const color = this.colors[Math.floor(Math.random() * this.colors.length)];
            const size = Math.random() * (this.sizeRange.max - this.sizeRange.min) + this.sizeRange.min;
            const opacity = Math.random() * 0.5 + 0.5;

            // Parçacık nesnesini oluştur ve başlangıç değerlerini ata
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

    // Olay dinleyicilerini ayarla (mouse, pencere boyutu değişimi vb.)
    setupEventListeners() {
        // Mouse hareketi takibi
        window.addEventListener('mousemove', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mousePosition = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        });

        // Mouse canvas dışına çıktığında
        window.addEventListener('mouseleave', () => {
            this.mousePosition = {
                x: null,
                y: null
            };
        });

        // Pencere boyutu değiştiğinde
        if (this.responsive) {
            window.addEventListener('resize', () => {
                this.resizeCanvas();
                this.createParticles();
            });
        }

        // Sayfa kaydırıldığında parçacıkları güncelle
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

    // Ana animasyon döngüsü
    animate() {
        this.frameCount++;
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Parçacıkları güncelle ve çiz
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];

            p.x += p.vx;
            p.y += p.vy;

            // Dalgalanma efekti varsa, parçacığın y pozisyonunu güncelle
            if (this.waveEffect) {
                p.y += Math.sin((p.x * 0.01) + (this.frameCount * this.waveSpeed)) * this.waveHeight * 0.01;
            }

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

            // Etkileşim varsa, parçacıkların boyutunu ve opaklığını güncelle
            if (this.mousePosition.x !== null && this.mousePosition.y !== null) {
                const dx = p.x - this.mousePosition.x;
                const dy = p.y - this.mousePosition.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                // Etkileşim yarıçapı içinde ise, parçacığın boyutunu ve opaklığını güncelle
                if (distance < this.interactionRadius) {
                    const force = (this.interactionRadius - distance) / this.interactionRadius;
                    const angle = Math.atan2(dy, dx);

                    p.vx += Math.cos(angle) * force * this.interactionStrength * 0.01;
                    p.vy += Math.sin(angle) * force * this.interactionStrength * 0.01;

                    p.size = p.originalSize * (1 + force * 1.5);
                    p.opacity = Math.min(1, p.opacity + force * 0.3);
                } else {
                    p.size = p.originalSize;
                    p.opacity = p.originalOpacity;
                }
            } else {
                p.size = p.originalSize;
                p.opacity = p.originalOpacity;
            }

            // Parçacığın boyutunu ve opaklığını sınırla
            const maxVelocity = 2;
            const velocity = Math.sqrt(p.vx * p.vx + p.vy * p.vy);
            if (velocity > maxVelocity) {
                p.vx = (p.vx / velocity) * maxVelocity;
                p.vy = (p.vy / velocity) * maxVelocity;
            }

            // Parçacığı çiz
            this.drawParticle(p);

            // Parçacıkların birbirleriyle bağlantılarını çiz
            for (let j = i + 1; j < this.particles.length; j++) {
                const p2 = this.particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                // Eğer mesafe maksimum mesafeden küçükse, çizgi çiz
                if (distance < this.maxDistance) {
                    const opacity = 1 - (distance / this.maxDistance);

                    // Çizgi rengini ve opaklığını ayarla
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

    // Tek bir parçacığı çiz
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

// Sayfa yüklendiğinde parçacık sistemini başlat
document.addEventListener('DOMContentLoaded', function () {
    const particlesContainer = document.getElementById('particles-container');
    if (!particlesContainer) return;

    particlesContainer.style.display = 'block';

    let canvas = document.getElementById('particles-canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'particles-canvas';
        particlesContainer.appendChild(canvas);
    }

    // Yeni parçacık ağı oluşturulurken ayarları optimize ediyoruz.
    new EnhancedParticleNetwork('particles-canvas', {
        particleCount: 80, // performans için azaltıldı
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
        glowEffect: false, // ekstradan gelen hesaplama kaldırıldı
        waveEffect: false, // dalgalanma efekti kaldırıldı
        waveSpeed: 0.03,
        waveHeight: 20
    });
});