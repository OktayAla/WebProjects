document.addEventListener('DOMContentLoaded', () => {
    const gunler = ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"];
    const aylar = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
    
    function tarihiGuncelle() {
        const simdi = new Date();
        const tarihMetni = document.getElementById('date-text');
        const formatliTarih = `${simdi.getDate()} ${aylar[simdi.getMonth()]} ${simdi.getFullYear()}, ${gunler[simdi.getDay()]}`;
        tarihMetni.textContent = formatliTarih;
    }
    
    tarihiGuncelle();
    
    function telefonNumarasiniDuzenle(telefonNumarasi) {
        return telefonNumarasi.replace(/\s+/g, '').replace(/[^\d]/g, '');
    }
    
    function eczaneKartiOlustur(eczane) {
        const temizTelefon = telefonNumarasiniDuzenle(eczane.telefon);
        
        return `
            <div class="eczane">
                <div class="eczane-header">
                    <div class="eczane-icon">
                    </div>
                    <h3>${eczane.isim}</h3>
                </div>
                <div class="eczane-info">
                    <p><i class="fas fa-map-marker-alt"></i> ${eczane.adres}</p>
                    <p><i class="fas fa-phone-alt"></i> <span class="eczane-telefon">${eczane.telefon}</span></p>
                </div>
            </div>
        `;
    }
    
    async function nobetciEczaneleriGetir() {
        try {
            const response = await fetch('api.php');
            if (!response.ok) {
                throw new Error(`HTTP hatası! durum: ${response.status}`);
            }
            const veri = await response.json();
            
            if (veri.error) {
                console.error('Veri çekme hatası:', veri.error);
                hataGoster('pamukkale-eczane-container', 'Veri çekme hatası: ' + veri.error);
                hataGoster('merkezefendi-eczane-container', 'Veri çekme hatası: ' + veri.error);
                return;
            }
            
            // Pamukkale
            const pamukkaleContainer = document.getElementById('pamukkale-eczane-container');
            if (pamukkaleContainer) {
                if (veri.pamukkale && veri.pamukkale.length > 0) {
                    pamukkaleContainer.innerHTML = veri.pamukkale.map(eczane => eczaneKartiOlustur(eczane)).join('');
                } else {
                    pamukkaleContainer.innerHTML = '<div class="error-message">Pamukkale için nöbetçi eczane bulunamadı.</div>';
                }
            } else {
                console.error('Pamukkale eczane container bulunamadı!');
            }
            
            // Merkezefendi
            const merkezefendiContainer = document.getElementById('merkezefendi-eczane-container');
            if (merkezefendiContainer) {
                if (veri.merkezefendi && veri.merkezefendi.length > 0) {
                    merkezefendiContainer.innerHTML = veri.merkezefendi.map(eczane => eczaneKartiOlustur(eczane)).join('');
                } else {
                    merkezefendiContainer.innerHTML = '<div class="error-message">Merkezefendi için nöbetçi eczane bulunamadı.</div>';
                }
            } else {
                console.error('Merkezefendi eczane container bulunamadı!');
            }
            
        } catch (error) {
            console.error('Veri çekme hatası:', error);
            hataGoster('pamukkale-eczane-container', 'Veri çekme hatası: ' + error.message);
            hataGoster('merkezefendi-eczane-container', 'Veri çekme hatası: ' + error.message);
        }
    }
    
    function hataGoster(containerId, mesaj) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `<div class="error-message">${mesaj}</div>`;
        }
    }
    
    nobetciEczaneleriGetir();
    
    setInterval(() => {
        tarihiGuncelle();
        nobetciEczaneleriGetir();
    }, 3600000); // 60 dakika
});