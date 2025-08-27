-- CustomerTrackingPanel Veritabanı Güncelleme Scripti
-- Bu script ürün yönetimi özelliğini eklemek için gerekli tabloları oluşturur

-- Ürünler tablosu oluşturma
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Örnek 10 ürün ekleme
INSERT INTO products (name, description, price, category) VALUES
('Pamuklu T-Shirt', '100% pamuk, nefes alabilir kumaş', 89.99, 'Giyim'),
('Kot Pantolon', 'Modern kesim, rahat fit', 199.99, 'Giyim'),
('Spor Ayakkabı', 'Günlük kullanım için ideal', 299.99, 'Ayakkabı'),
('Cep Telefonu Kılıfı', 'Silikon koruyucu kılıf', 29.99, 'Aksesuar'),
('Bluetooth Kulaklık', 'Kablosuz, uzun pil ömrü', 149.99, 'Elektronik'),
('Kahve Makinesi', 'Otomatik filtre kahve makinesi', 399.99, 'Ev Aletleri'),
('Kitap: İş Dünyası', 'Yönetim ve liderlik rehberi', 49.99, 'Kitap'),
('Yoga Matı', 'Profesyonel yoga matı', 79.99, 'Spor'),
('Şarj Kablosu', 'USB-C hızlı şarj kablosu', 39.99, 'Elektronik'),
('Su Şişesi', '1L paslanmaz çelik su şişesi', 59.99, 'Aksesuar');

-- Transactions tablosuna product_id kolonu ekleme (eğer yoksa)
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS product_id INT NULL;

-- Foreign key constraint ekleme (eğer yoksa)
ALTER TABLE transactions 
ADD CONSTRAINT fk_transactions_product 
FOREIGN KEY (product_id) REFERENCES products(id) 
ON DELETE SET NULL;

-- Index ekleme (performans için)
CREATE INDEX IF NOT EXISTS idx_transactions_product_id ON transactions(product_id);
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category);
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name);

-- Mevcut işlemlerde product_id NULL olarak ayarlanır
UPDATE transactions SET product_id = NULL WHERE product_id = 0 OR product_id IS NULL;

-- Veritabanı güncelleme tamamlandı mesajı
SELECT 'Veritabanı başarıyla güncellendi!' AS message;
