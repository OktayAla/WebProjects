        // Canvas ayarları
        const canvas = document.createElement('canvas');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        document.body.appendChild(canvas);
        const ctx = canvas.getContext('2d');

        // Görseller
        const oyuncuResmi = new Image();
        oyuncuResmi.src = 'oyuncu.png'; // Oyuncu gemisi görseli

        const dusmanResmi = new Image();
        dusmanResmi.src = 'dusman.png'; // Düşman gemisi görseli

        const asteroidResmi = new Image();
        asteroidResmi.src = 'asteroid.png'; // Asteroid görseli

        const uyduResmi = new Image();
        uyduResmi.src = 'uydu.png'; // Uydu görseli

        const patlamaResmi = new Image();
        patlamaResmi.src = 'patlama.png'; // Patlama efekti görseli

        // Değişkenler
        const oyuncu = { x: canvas.width / 2 - 40, y: canvas.height - 120, genislik: 80, yukseklik: 120, hiz: 7, can: 10 }; // Oyuncu gemisi özellikleri
        const mermiler = []; // Mermilerin listesi
        const dusmanlar = []; // Düşmanların listesi
        const asteroidler = []; // Asteroidlerin listesi
        const uydular = []; // Uyduların listesi
        const yildizlar = []; // Arka plan yıldızlarının listesi
        const patlamalar = []; // Patlama efektlerinin listesi
        let puan = 0; // Oyuncunun puanı
        let kare = 0; // Oyun döngüsü kare sayacı

        // Klavye kontrolü
        const tuslar = {};
        window.addEventListener('keydown', (e) => tuslar[e.code] = true);
        window.addEventListener('keyup', (e) => tuslar[e.code] = false);

        // Yıldızların arka plan efekti
        for (let i = 0; i < 100; i++) {
            yildizlar.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, boyut: Math.random() * 2 + 1, hiz: Math.random() * 2 + 1 });
        }

        // Yıldızları güncelleme fonksiyonu
        function yildizlariGuncelle() {
            yildizlar.forEach(yildiz => {
                yildiz.y += yildiz.hiz;
                if (yildiz.y > canvas.height) {
                    yildiz.y = 0;
                    yildiz.x = Math.random() * canvas.width;
                }
            });
        }

        // Yıldızları çizme fonksiyonu
        function yildizlariCiz() {
            ctx.fillStyle = 'white';
            yildizlar.forEach(yildiz => {
                ctx.beginPath();
                ctx.arc(yildiz.x, yildiz.y, yildiz.boyut, 0, Math.PI * 2);
                ctx.fill();
            });
        }

        // Oyuncu hareketi fonksiyonu
        function oyuncuyuHareketEttir() {
            if (tuslar['ArrowLeft'] && oyuncu.x > 0) oyuncu.x -= oyuncu.hiz; // Sol ok tuşu ile sola hareket
            if (tuslar['ArrowRight'] && oyuncu.x + oyuncu.genislik < canvas.width) oyuncu.x += oyuncu.hiz; // Sağ ok tuşu ile sağa hareket
            if (tuslar['Space'] && kare % 10 === 0) {
                mermiler.push({ x: oyuncu.x + oyuncu.genislik / 2 - 5, y: oyuncu.y, genislik: 10, yukseklik: 20, hiz: 8 }); // Boşluk tuşu ile mermi at
            }
        }

        // Mermileri güncelleme fonksiyonu
        function mermileriGuncelle() {
            mermiler.forEach((mermi, indeks) => {
                mermi.y -= mermi.hiz; // Mermiyi yukarı hareket ettir
                if (mermi.y + mermi.yukseklik < 0) mermiler.splice(indeks, 1); // Mermi ekran dışına çıkarsa kaldır
            });
        }

        // Mermileri çizme fonksiyonu
        function mermileriCiz() {
            ctx.fillStyle = 'yellow';
            mermiler.forEach(mermi => {
                ctx.fillRect(mermi.x, mermi.y, mermi.genislik, mermi.yukseklik); // Mermiyi çiz
            });
        }

        // Düşman oluşturma fonksiyonu
        function dusmanlariOlustur() {
            if (kare % 500 === 0) {
                const x = Math.random() * (canvas.width - 80);
                const can = Math.floor(Math.random() * 3) + 1;
                dusmanlar.push({
                    xBaslangic: x, // Başlangıç x konumu
                    y: 0,
                    genislik: 80,
                    yukseklik: 80,
                    hiz: 1 + Math.random(),
                    can: can,
                    hareketGenisligi: 20 + Math.random() * 30, // Hareket genişliği (rastgele)
                    hareketHizi: 0.02 + Math.random() * 0.03, // Hareket hızı (rastgele)
                    hareketPozisyonu: Math.random() * Math.PI * 2, // Rastgele başlangıç pozisyonu
                });
            }
        }

        // Düşmanları güncelleme fonksiyonu
        function dusmanlariGuncelle() {
            dusmanlar.forEach((dusman, indeks) => {
                dusman.y += dusman.hiz;
                dusman.hareketPozisyonu += dusman.hareketHizi;
                dusman.x = dusman.xBaslangic + Math.sin(dusman.hareketPozisyonu) * dusman.hareketGenisligi;

                if (dusman.y > canvas.height) {
                    dusmanlar.splice(indeks, 1);
                    oyuncu.can--;
                }
            });
        }

        // Düşmanları çizme fonksiyonu
        function dusmanlariCiz() {
            dusmanlar.forEach(dusman => {
                ctx.drawImage(dusmanResmi, dusman.x, dusman.y, dusman.genislik, dusman.yukseklik); // Düşmanı çiz
                // Can göstergesini çiz
            });
        }

        // Asteroid oluşturma fonksiyonu
        function asteroidleriOlustur() {
            if (kare % 950 === 0) {
                const x = Math.random() * (canvas.width - 100);
                const can = Math.floor(Math.random() * 5) + 3;
                asteroidler.push({
                    x,
                    y: 0,
                    genislik: 80,
                    yukseklik: 80,
                    hiz: 1.0,
                    can: can,
                    donmeAcisi: 0, // Başlangıç dönüş açısı
                    donmeHizi: (Math.random() - 0.5) * 2, // Rastgele dönüş hızı (-1 ile 1 arasında)
                });
            }
        }

        // Asteroidleri güncelleme fonksiyonu
        function asteroidleriGuncelle() {
            asteroidler.forEach((asteroid, indeks) => {
                asteroid.y += asteroid.hiz;
                asteroid.donmeAcisi += asteroid.donmeHizi; // Dönüş açısını güncelle
                if (asteroid.donmeAcisi > 360) {
                    asteroid.donmeAcisi -= 360; // 360 dereceyi aşarsa sıfırla
                } else if (asteroid.donmeAcisi < 0) {
                    asteroid.donmeAcisi += 360; // 0 derecenin altına düşerse 360'a tamamla
                }
                if (asteroid.y > canvas.height) {
                    asteroidler.splice(indeks, 1);
                }
            });
        }

        // Asteroidleri çizme fonksiyonu
        function asteroidleriCiz() {
            asteroidler.forEach(asteroid => {
                ctx.save(); // Canvas durumunu kaydet
                ctx.translate(asteroid.x + asteroid.genislik / 2, asteroid.y + asteroid.yukseklik / 2); // Asteroidin merkezine taşı
                ctx.rotate(asteroid.donmeAcisi * Math.PI / 180); // Döndür
                ctx.drawImage(asteroidResmi, -asteroid.genislik / 2, -asteroid.yukseklik / 2, asteroid.genislik, asteroid.yukseklik); // Asteroidi çiz
                ctx.restore(); // Canvas durumunu geri yükle
            });
        }

        // Uydu oluşturma fonksiyonu
        function uydulariOlustur() {
            if (kare % 800 === 0) {
                const x = Math.random() * (canvas.width - 80);
                const can = Math.floor(Math.random() * 4) + 2;
                uydular.push({
                    x,
                    y: 0,
                    genislik: 100,
                    yukseklik: 100,
                    hiz: 1.2,
                    can: can,
                    donmeAcisi: 0, // Başlangıç dönüş açısı
                    donmeHizi: (Math.random() - 0.5) * 1.5, // Rastgele dönüş hızı
                    olcek: 1, // Başlangıç ölçeği
                    olcekHizi: (Math.random() - 0.5) * 0.01, // Ölçek değişimi hızı (küçülüp büyüme efekti için)
                });
            }
        }

        // Uyduları güncelleme fonksiyonu
        function uydulariGuncelle() {
            uydular.forEach((uydu, indeks) => {
                uydu.y += uydu.hiz;
                uydu.donmeAcisi += uydu.donmeHizi; // Dönüş açısını güncelle
                uydu.olcek += uydu.olcekHizi; // Ölçeği güncelle

                // Ölçeğin belirli aralıkta kalmasını sağla
                if (uydu.olcek > 1.1) {
                    uydu.olcekHizi = -Math.abs(uydu.olcekHizi); // Küçülmeye başla
                } else if (uydu.olcek < 0.9) {
                    uydu.olcekHizi = Math.abs(uydu.olcekHizi); // Büyümeye başla
                }

                if (uydu.donmeAcisi > 360) {
                    uydu.donmeAcisi -= 360;
                } else if (uydu.donmeAcisi < 0) {
                    uydu.donmeAcisi += 360;
                }

                if (uydu.y > canvas.height) {
                    uydular.splice(indeks, 1);
                }
            });
        }

        // Uyduları çizme fonksiyonu
        function uydulariCiz() {
            uydular.forEach(uydu => {
                ctx.save(); // Canvas durumunu kaydet
                ctx.translate(uydu.x + uydu.genislik / 2, uydu.y + uydu.yukseklik / 2); // Uydunun merkezine taşı
                ctx.rotate(uydu.donmeAcisi * Math.PI / 180); // Döndür
                ctx.scale(uydu.olcek, uydu.olcek); // Ölçekle
                ctx.drawImage(uyduResmi, -uydu.genislik / 2, -uydu.yukseklik / 2, uydu.genislik, uydu.yukseklik); // Uyduyu çiz
                ctx.restore(); // Canvas durumunu geri yükle
            });
        }
        // Patlama efektini güncelleme fonksiyonu
        function patlamalariGuncelle() {
            patlamalar.forEach((patlama, indeks) => {
                patlama.alpha -= 0.02; // Alpha değerini azaltarak görseli kaybet
                patlama.scale += 0.05; // Ölçeği artırarak büyüt
                if (patlama.alpha <= 0) {
                    patlamalar.splice(indeks, 1); // Alpha değeri 0 olduğunda patlamayı kaldır
                }
            });
        }

        // Patlama efektini çizme fonksiyonu
        function patlamalariCiz() {
            patlamalar.forEach(patlama => {
                ctx.globalAlpha = patlama.alpha; // Alpha değerini ayarla
                ctx.save(); // Canvas durumunu kaydet
                ctx.translate(patlama.x + patlama.genislik / 2, patlama.y + patlama.yukseklik / 2); // Patlamanın merkezine taşı
                ctx.scale(patlama.scale, patlama.scale); // Ölçeği uygula
                ctx.drawImage(patlamaResmi, -patlama.genislik / 2, -patlama.yukseklik / 2, patlama.genislik, patlama.yukseklik); // Patlamayı çiz
                ctx.restore(); // Canvas durumunu geri yükle
                ctx.globalAlpha = 1; // Alpha değerini sıfırla
            });
        }

        // Çarpışma kontrolü fonksiyonu
        function carpismalariKontrolEt() {
            dusmanlar.forEach((dusman, dIndex) => {
                mermiler.forEach((mermi, mIndex) => {
                    if (
                        mermi.x < dusman.x + dusman.genislik &&
                        mermi.x + mermi.genislik > dusman.x &&
                        mermi.y < dusman.y + dusman.yukseklik &&
                        mermi.y + mermi.yukseklik > dusman.y
                    ) {
                        dusman.can--; // Düşmanın canını azalt
                        mermiler.splice(mIndex, 1); // Mermiyi kaldır
                        if (dusman.can <= 0) {
                            dusmanlar.splice(dIndex, 1); // Düşmanı kaldır
                            puan += 20; // Puanı artır
                            patlamalar.push({ x: dusman.x, y: dusman.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                        }
                    }
                });

                if (
                    oyuncu.x < dusman.x + dusman.genislik &&
                    oyuncu.x + oyuncu.genislik > dusman.x &&
                    oyuncu.y < dusman.y + dusman.yukseklik &&
                    oyuncu.y + oyuncu.yukseklik > dusman.y
                ) {
                    dusmanlar.splice(dIndex, 1); // Düşmanı kaldır
                    oyuncu.can--; // Oyuncunun canını azalt
                    patlamalar.push({ x: dusman.x, y: dusman.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                }
            });

            asteroidler.forEach((asteroid, aIndex) => {
                mermiler.forEach((mermi, mIndex) => {
                    if (
                        mermi.x < asteroid.x + asteroid.genislik &&
                        mermi.x + mermi.genislik > asteroid.x &&
                        mermi.y < asteroid.y + asteroid.yukseklik &&
                        mermi.y + mermi.yukseklik > asteroid.y
                    ) {
                        asteroid.can--; // Asteroidin canını azalt
                        mermiler.splice(mIndex, 1); // Mermiyi kaldır
                        if (asteroid.can <= 0) {
                            asteroidler.splice(aIndex, 1); // Asteroidi kaldır
                            puan += 15; // Puanı artır
                            patlamalar.push({ x: asteroid.x, y: asteroid.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                        }
                    }
                });

                if (
                    oyuncu.x < asteroid.x + asteroid.genislik &&
                    oyuncu.x + oyuncu.genislik > asteroid.x &&
                    oyuncu.y < asteroid.y + asteroid.yukseklik &&
                    oyuncu.y + oyuncu.yukseklik > asteroid.y
                ) {
                    asteroidler.splice(aIndex, 1); // Asteroidi kaldır
                    oyuncu.can--; // Oyuncunun canını azalt
                    patlamalar.push({ x: asteroid.x, y: asteroid.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                }
            });

            uydular.forEach((uydu, uIndex) => {
                mermiler.forEach((mermi, mIndex) => {
                    if (
                        mermi.x < uydu.x + uydu.genislik &&
                        mermi.x + mermi.genislik > uydu.x &&
                        mermi.y < uydu.y + uydu.yukseklik &&
                        mermi.y + mermi.yukseklik > uydu.y
                    ) {
                        uydu.can--; // Uydunun canını azalt
                        mermiler.splice(mIndex, 1); // Mermiyi kaldır
                        if (uydu.can <= 0) {
                            uydular.splice(uIndex, 1); // Uyduyu kaldır
                            puan += 10; // Puanı artır
                            patlamalar.push({ x: uydu.x, y: uydu.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                        }
                    }
                });

                if (
                    oyuncu.x < uydu.x + uydu.genislik &&
                    oyuncu.x + oyuncu.genislik > uydu.x &&
                    oyuncu.y < uydu.y + uydu.yukseklik &&
                    oyuncu.y + oyuncu.yukseklik > uydu.y
                ) {
                    uydular.splice(uIndex, 1); // Uyduyu kaldır
                    oyuncu.can--; // Oyuncunun canını azalt
                    patlamalar.push({ x: uydu.x, y: uydu.y, genislik: 80, yukseklik: 80, alpha: 1, scale: 1 }); // Patlama efekti ekle
                }
            });
        }

        // Can ve puan bilgisi çizimi fonksiyonu
        function HUDCiz() {
            ctx.fillStyle = 'white';
            ctx.font = '20px Arial';
            ctx.fillText(`Puan: ${puan}`, 10, 30); // Puanı çiz
            ctx.fillText(`Can: ${oyuncu.can}`, 10, 60); // Canı çiz
        }

        // Oyun döngüsü fonksiyonu
        function oyunDongusu() {
            kare++;

            // Temizle
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Güncellemeler
            yildizlariGuncelle();
            oyuncuyuHareketEttir();
            mermileriGuncelle();
            dusmanlariOlustur();
            dusmanlariGuncelle();
            asteroidleriOlustur();
            asteroidleriGuncelle();
            uydulariOlustur();
            uydulariGuncelle();
            carpismalariKontrolEt();
            patlamalariGuncelle();

            // Çizimler
            yildizlariCiz();
            ctx.drawImage(oyuncuResmi, oyuncu.x, oyuncu.y, oyuncu.genislik, oyuncu.yukseklik); // Oyuncuyu çiz
            mermileriCiz();
            dusmanlariCiz();
            asteroidleriCiz();
            uydulariCiz();
            patlamalariCiz();
            HUDCiz();

            if (oyuncu.can > 0) {
                requestAnimationFrame(oyunDongusu); // Oyun döngüsünü devam ettir
            } else {
                oyunBitti(); // Oyun bitti ekranını göster
            }
        }

        // Oyun bitti fonksiyonu
        function oyunBitti() {
            const oyunBittiDiv = document.createElement('div');
            oyunBittiDiv.className = 'oyun-bitti';
            oyunBittiDiv.innerHTML =
                `
                <h1>Oyun Bitti</h1>
                <p>Puan: ${puan}</p>
                <button class="yeniden-basla-butonu" 
                onclick="oyunuYenidenBaslat()">Yeniden Başla</button>
            `;
            document.body.appendChild(oyunBittiDiv);
        }

        // Oyunu yeniden başlatma fonksiyonu
        function oyunuYenidenBaslat() {
            document.body.innerHTML = '';
            oyuncu.can = 10; // Oyuncunun canını ayarla
            puan = 0; // Puanı sıfırla
            dusmanlar.length = 0; // Düşmanları temizle
            mermiler.length = 0; // Mermileri temizle
            asteroidler.length = 0; // Asteroidleri temizle
            uydular.length = 0; // Uyduları temizle
            patlamalar.length = 0; // Patlamaları temizle
            kare = 0; // Kare sayacını sıfırla
            document.body.appendChild(canvas);
            oyunDongusu(); // Oyunu yeniden başlat
        }

        // Oyunu başlat
        oyunDongusu();