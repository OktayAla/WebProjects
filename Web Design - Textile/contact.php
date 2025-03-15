<?php require_once 'includes/header.php'; ?>

<!-- Sayfa başlığı ve arka plan resmi -->
<div class="page-header bg-texture py-5 mb-5" 
     style="background-image: url('img/contact/contact_header.jpg');">
    <div class="container">
        <h1 class="display-4 text-white text-center">İletişim</h1>
    </div>
</div>

<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">

                <!-- İletişim bilgileri -->
                <h2>İletişim Bilgileri</h2>
                <p class="lead">Sizinle iletişime geçmekten mutluluk duyarız.</p>
                
                <div class="contact-info mt-4">
                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill me-3 fs-4 text-accent"></i>
                        <div>
                            <h5>Adres</h5>
                            <p>Merkezefendi Cad. Pamukkale Mah. No:1<br>Denizli, Türkiye</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-telephone-fill me-3 fs-4 text-accent"></i>
                        <div>
                            <h5>Telefon</h5>
                            <p>+90 (258) 000 0000</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-envelope-fill me-3 fs-4 text-accent"></i>
                        <div>
                            <h5>E-posta</h5>
                            <p>info@elegancetextile.com</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                
                <!-- İletişim formu -->
                <form id="contactForm" class="contact-form">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Adınız Soyadınız" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="E-posta Adresiniz" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-select">
                            <option selected>Konu Seçiniz</option>
                            <option>Ürün Bilgisi</option>
                            <option>İş Birliği</option>
                            <option>Diğer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="5" placeholder="Mesajınız" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Google Harita -->
<div class="map-section mt-5">
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100821.71688566607!2d29.02392296782693!3d37.78428842697761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c73fb1950a7c51%3A0x49c41819c0fd3179!2sDenizli%2C%20T%C3%BCrkiye!5e0!3m2!1str!2str!4v1682157000000!5m2!1str!2str"
        width="100%" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy">
    </iframe>
</div>

<?php require_once 'includes/footer.php'; ?>