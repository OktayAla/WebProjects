-- CustomerTrackingPanel Veritabanı Güncelleme Scripti
-- Bu script ürün yönetimi özelliğini eklemek için gerekli tabloları oluşturur

-- Ürünler tablosu oluşturma (basitleştirilmiş)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Örnek 10 ürün ekleme
INSERT INTO products (name) VALUES
('Pamuklu T-Shirt'),
('Kot Pantolon'),
('Spor Ayakkabı'),
('Cep Telefonu Kılıfı'),
('Bluetooth Kulaklık'),
('Kahve Makinesi'),
('Kitap: İş Dünyası'),
('Yoga Matı'),
('Şarj Kablosu'),
('Su Şişesi');

-- Transactions tablosuna product_id kolonu ekleme (eğer yoksa)
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS product_id INT NULL;

-- Foreign key constraint ekleme (eğer yoksa)
ALTER TABLE transactions 
ADD CONSTRAINT fk_transactions_product 
FOREIGN KEY (product_id) REFERENCES products(id) 
ON DELETE SET NULL;

-- Index ekleme (performans için)
CREATE INDEX IF NOT EXISTS idx_transactions_product_id ON transactions(product_id);
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name);

-- Mevcut işlemlerde product_id NULL olarak ayarlanır
UPDATE transactions SET product_id = NULL WHERE product_id = 0 OR product_id IS NULL;

-- Veritabanı güncelleme tamamlandı mesajı
SELECT 'Veritabanı başarıyla güncellendi!' AS message;
