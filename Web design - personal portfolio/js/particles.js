/*
* Bu JavaScript dosyası, web sitesinin arka planında görünen hareketli parçacık ağını oluşturur.
* İçerdiği temel işlevler:
* - Parçacık sistemi oluşturma ve yönetimi
* - Fare ile etkileşimli parçacık hareketi
* - Parçacıklar arası bağlantı çizgileri
* - Responsive (duyarlı) canvas yönetimi
* - Parçacık animasyonları ve fizik hesaplamaları
*/

// Partikül ağları efekti için gerekli kodlar
// https://github.com/VincentGarreau/particles.js

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


    // Partiküleri oluştur ve başlat
    init() {
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

        // Fare hareketini izlemek için event listener ekle
        window.addEventListener('mousemove', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mousePosition = {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        });

        // Fare tıklama olayını izlemek için event listener ekle
        this.animate();
    }

    // Animasyonu başlat
    animate() {
        this.ctx.clearRect(0, 0, this.width, this.height);

        // Fare pozisyonunu güncelle
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];

            // Partiküleleri hareket ettir
            p.x += p.vx;
            p.y += p.vy;

            // Partiküleleri kanvas dışına cçıkarmayı engelle
            if (p.x < 0 || p.x > this.width) p.vx *= -1;
            if (p.y < 0 || p.y > this.height) p.vy *= -1;

            // Partiküleleri çiz
            this.ctx.beginPath();
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fillStyle = p.color;
            this.ctx.fill();

            // Çizgi bağlantılarını oluştur
            for (let j = i + 1; j < this.particles.length; j++) {
                const p2 = this.particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                // Çizgi bağlantılarını oluştur
                if (distance < this.maxDistance) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.strokeStyle = this.lineColor;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.stroke();
                }
            }

            // Fare ile bağlantı çizgilerini oluştur
            const dx = p.x - this.mousePosition.x;
            const dy = p.y - this.mousePosition.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            // Fare ile bağlantı çizgilerini oluştur
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

    // Canvas boyutunu güncelle
    resize() {
        this.width = this.canvas.width;
        this.height = this.canvas.height;
    }
}

// Partikül ağları efekti için gerekli kodlar
document.addEventListener('DOMContentLoaded', () => {
    const heroSection = document.getElementById('home');
    if (!heroSection) return;

    // Hero bölümünün boyutunu ayarla
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

    // Hero bölümünün boyutunu ayarla
    canvas.width = heroSection.offsetWidth;
    canvas.height = heroSection.offsetHeight;

    // Partikül ağları efekti için kullanılacak ParticleNetwork nesnesini oluştur
    const particleNetwork = new ParticleNetwork(canvas, {
        particleCount: 80,
        color: '#c9a0ff',
        lineColor: 'rgba(201, 160, 255, 0.2)',
        maxDistance: 150,
        speed: 0.5,
        size: 2
    });

    // Yeniden boyutlandırma eventini ayarla
    window.addEventListener('resize', () => {
        canvas.width = heroSection.offsetWidth;
        canvas.height = heroSection.offsetHeight;
        particleNetwork.resize();
    });
});