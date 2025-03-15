// HTML elementlerini değişkenlere atama işlemleri
const kus = document.getElementById("kus"); // Kuş
const engel = document.getElementById("engel"); // Üst engel görseli
const engel2 = document.getElementById("engel2"); // Alt engel görseli
const baslangicPopup = document.getElementById("baslangic-popup"); // Başlangıç popup
const oyunBittiPopup = document.getElementById("oyun-bitti-popup"); // Bitiş popup
const sonPuan = document.getElementById("son-puan"); // Oyuncunun toplam puano
const baslatButon = document.getElementById("baslat-buton"); // Oyunu başlatma
const yenidenBaslatButon = document.getElementById("yeniden-baslat-buton"); // Yeniden başlatma
const arkaplanKonteyner = document.getElementById("arkaplan-konteyner"); // Arka plan
const puanGoster = document.getElementById("puan-goster"); // Puanı gösterme

// Oyun değişkenleri
let kusYukseklik = 250; // Kuşun başlangıç yüksekliği
let yerCekimi = 2; // Yer çekimi kuvveti
let engelSol = 400; // Birinci engelin yatay pozisyonu
let engel2Sol = 400; // İkinci engelin yatay pozisyonu
let bosluk = 200; // Engeller arasındaki boşluk
let puan = 0; // Oyuncunun puanı
let oyunHizi = 5; // Oyunun hızı
let oyunBittiMi = false; // Oyunun bitip bitmediğini belirten değer
let engelGecildiMi = false; // Engelin geçilip geçilmediğini belirten değer
let oyunBasladiMi = false; // Oyunun başlayıp başlamadığını belirten değer
let kusDonusAcisi = 0; // Kuşun dönüş açısı

/*
Oyunun başlangıcını kontrol eden fonksiyon
*/
function oyunuBaslat() {
    baslangicPopup.style.display = "none";
    oyunBasladiMi = true;
    oyunuSifirla();
}

/*
Kuşun hareketinin sağlandığı fonksiyon. Kuşun zıplaması ve düşüş açışını ayarlar
*/
function zipla() {
    if (kusYukseklik > 0 && !oyunBittiMi && oyunBasladiMi) {
        kusYukseklik -= 50;
        kus.style.top = kusYukseklik + "px";
        kusDonusAcisi = -20; // Zıpladığında kafası yukarı kalkar
        kus.style.transform = `translateY(-50%) rotate(${kusDonusAcisi}deg)`;
    }
}

/*
Kuş hareket ettiğinde aşağıya doğru düşmesini sağlayan yerçekimi fonksiyonu. Eğer kuş yere değerse oyunu bitirir
*/
function yerCekimiUygula() {
    if (!oyunBittiMi && oyunBasladiMi) {
        kusYukseklik += yerCekimi;
        kus.style.top = kusYukseklik + "px";

        // Kuş düşerken kafası aşağı eğilir
        if (kusDonusAcisi < 20) {
            kusDonusAcisi += 1; // Yavaşça aşağı eğilir
            kus.style.transform = `translateY(-50%) rotate(${kusDonusAcisi}deg)`;
        }

        if (kusYukseklik >= 560) {
            oyunuBitir();
        }
    }
}

/*
Engellerin hareketini yapan fonksiyon
 */
function engelleriHareketEttir() {
    if (!oyunBittiMi && oyunBasladiMi) {
        engelSol -= oyunHizi;
        engel2Sol -= oyunHizi;

        if (engelSol < -60) {
            engeliSifirla();
        }

        engel.style.left = engelSol + "px";
        engel2.style.left = engel2Sol + "px";
    }
}

/*
Akraplanın sağdan sola doğru yatay düzlemde hareketini sağlayan fonksiyon
*/
function arkaplanHareketEttir() {
    if (!oyunBittiMi && oyunBasladiMi) {
        const arkaplanSol = parseFloat(arkaplanKonteyner.style.left) || 0;
        arkaplanKonteyner.style.left = (arkaplanSol - oyunHizi / 2) + "px";

        if (arkaplanSol <= -400) {
            arkaplanKonteyner.style.left = "0";
        }
    }
}

/*
Engellerin rastgele yükseklikte ve aralıkta oluşumunu sağlar
*/
function engeliSifirla() {
    const minYukseklik = 100;
    const maxYukseklik = 300;
    const engelYukseklik = Math.floor(Math.random() * (maxYukseklik - minYukseklik + 1)) + minYukseklik;
    const engel2Yukseklik = 600 - engelYukseklik - bosluk;

    engel.style.height = engelYukseklik + "px";
    engel2.style.height = engel2Yukseklik + "px";

    engelSol = 400;
    engel2Sol = 400;

    engelGecildiMi = false;
}

/*
Kuşun engellerle çarpışıp çarpışmadığını kontrol eden fonksiyondur. Eğer engellere çarptıysa oyunu bitirir; çarpmadıysa puanı arttırır.
*/
function carpismaKontrol() {
    const kusRect = kus.getBoundingClientRect();
    const engelRect = engel.getBoundingClientRect();
    const engel2Rect = engel2.getBoundingClientRect();

    if (
        kusRect.left < engelRect.right &&
        kusRect.right > engelRect.left &&
        kusRect.top < engelRect.bottom
    ) {
        oyunuBitir();
    }

    if (
        kusRect.left < engel2Rect.right &&
        kusRect.right > engel2Rect.left &&
        kusRect.bottom > engel2Rect.top
    ) {
        oyunuBitir();
    }

    if (engelSol + 60 < kusRect.left && !engelGecildiMi) {
        puan++;
        puanGoster.textContent = puan;
        engelGecildiMi = true;
        if (puan % 5 === 0) {
            oyunHizi += 1;
            yerCekimi += 0.2;
        }
    }
}

/*
Oyun bittiyse popup gösterir ve puanı gösterir
*/
function oyunuBitir() {
    oyunBittiMi = true;
    sonPuan.textContent = puan;
    oyunBittiPopup.style.display = "block";
}

/*
Oyunu sıfırlayarak tekrardan sıfır başlangıca hazırlar
*/
function oyunuSifirla() {
    oyunBittiMi = false;
    kusYukseklik = 250;
    engelSol = 400;
    engel2Sol = 400;
    puan = 0;
    puanGoster.textContent = puan;
    oyunHizi = 5;
    yerCekimi = 2;
    kus.style.top = kusYukseklik + "px";
    kusDonusAcisi = 0; // Dönüş açısını sıfırla
    kus.style.transform = `translateY(-50%) rotate(${kusDonusAcisi}deg)`;
    engel.style.left = engelSol + "px";
    engel2.style.left = engel2Sol + "px";
    oyunBittiPopup.style.display = "none";
    engelGecildiMi = false;
    engeliSifirla();
}

/*
Butonların tıklama işlevi ve klavye tuşlarına basıldığında çalışacak fonksiyonları tanımlar
*/
baslatButon.addEventListener("click", oyunuBaslat); // Başlat butonuna tıklanıldığında oyunu başlatır.
yenidenBaslatButon.addEventListener("click", oyunuSifirla); // Yeniden başlat butonuna tıklanıldığında oyunu sıfırlar.
document.addEventListener("keydown", zipla); // Herhangi bir tuşa basıldığında zıpla fonksiyonunu çağırır.

// Oyun döngüsü
setInterval(() => {
    if (!oyunBittiMi && oyunBasladiMi) {
        yerCekimiUygula();
        engelleriHareketEttir();
        arkaplanHareketEttir();
        carpismaKontrol();
    }
}, 20);