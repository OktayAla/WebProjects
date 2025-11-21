// Mobil dokunma desteği
(function() {
    const oyun = document.getElementById('oyun');
    
    if (!oyun) return;
    
    // Dokunma olaylarını dinle
    let touchStartTime = 0;
    
    function handleTouchStart(e) {
        e.preventDefault();
        touchStartTime = Date.now();
        
        // Space tuşu olayını tetikle (kuş zıplaması için)
        const spaceEvent = new KeyboardEvent('keydown', {
            key: ' ',
            code: 'Space',
            keyCode: 32,
            which: 32,
            bubbles: true,
            cancelable: true
        });
        document.dispatchEvent(spaceEvent);
    }
    
    function handleTouchEnd(e) {
        e.preventDefault();
        
        // Kısa dokunma için (tıklama benzeri)
        const touchDuration = Date.now() - touchStartTime;
        if (touchDuration < 300) {
            // Click olayını da tetikle (butonlar için)
            const clickEvent = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
            });
            
            // Eğer popup açıksa butona tıklama olayını gönder
            const baslangicPopup = document.getElementById('baslangic-popup');
            const oyunBittiPopup = document.getElementById('oyun-bitti-popup');
            
            if (baslangicPopup && baslangicPopup.style.display !== 'none') {
                const baslatButon = document.getElementById('baslat-buton');
                if (baslatButon) {
                    baslatButon.dispatchEvent(clickEvent);
                }
            } else if (oyunBittiPopup && oyunBittiPopup.style.display !== 'none') {
                const yenidenBaslatButon = document.getElementById('yeniden-baslat-buton');
                if (yenidenBaslatButon) {
                    yenidenBaslatButon.dispatchEvent(clickEvent);
                }
            }
        }
    }
    
    // Oyun alanına dokunma olaylarını ekle
    oyun.addEventListener('touchstart', handleTouchStart, { passive: false });
    oyun.addEventListener('touchend', handleTouchEnd, { passive: false });
    
    // Body'ye de ekle (tüm ekrana dokunma desteği)
    document.body.addEventListener('touchstart', function(e) {
        // Popup'ların dışındaysa oyun kontrolü için
        const target = e.target;
        if (!target.closest('.popup') && !target.closest('button')) {
            handleTouchStart(e);
        }
    }, { passive: false });
    
    document.body.addEventListener('touchend', function(e) {
        const target = e.target;
        if (!target.closest('.popup') && !target.closest('button')) {
            handleTouchEnd(e);
        }
    }, { passive: false });
    
    // Çift dokunma ile zoom'u engelle
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(e) {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
            e.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
})();

