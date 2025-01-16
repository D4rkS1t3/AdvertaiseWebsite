-- Tworzenie bazy danych
CREATE DATABASE IF NOT EXISTS advertisement;
USE advertisement;

-- Tabela użytkowników (users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64),
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    reset_token VARCHAR(64), -- Token do resetowania hasła
    token_expiry DATETIME, -- Data ważności tokena resetowania
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela kategorii (categories)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Tabela ogłoszeń (ads)
CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    localization VARCHAR(100) NOT NULL,
    category_id INT NULL,
    image_path TEXT,
    user_id INT NOT NULL,
    phone_number VARCHAR(15) NULL,
    active TINYINT(1) DEFAULT 1,
    condit TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indeksy dla optymalizacji wyszukiwania
CREATE INDEX idx_user_username ON users(username);
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_ads_category_id ON ads(category_id);
CREATE INDEX idx_ads_user_id ON ads(user_id);
CREATE INDEX idx_ads_active ON ads(active);
CREATE INDEX idx_ads_active_condit ON ads(active, condit);
CREATE INDEX idx_ads_active_category ON ads(active, category_id);
CREATE INDEX idx_ads_active_user ON ads(active, user_id);
CREATE INDEX idx_ads_active_created ON ads(active, created_at);

-- Dodanie kategorii do tabeli categories
INSERT INTO categories (id, name) VALUES
(1, 'Photography'),
(2, 'For Business'),
(3, 'Home & Garden'),
(4, 'Electronics'),
(5, 'Fashion'),
(6, 'Automotive'),
(7, 'Music & Education'),
(8, 'Jobs'),
(9, 'Sports & Hobbies'),
(10, 'Health & Beauty'),
(11, 'Animals'),
(12, 'Real Estate')
ON DUPLICATE KEY UPDATE name = VALUES(name);
















-- Dodaj użytkownika testowego ręcznie lub innym skryptem, ponieważ hasło wymaga hashowania.























-- Dodanie przykładowych danych do tabeli ads
INSERT INTO ads (title, description, price, localization, category_id, image_path, user_id, phone_number, active, condit)
VALUES
    ('Canon EOS 5D Camera', 'High-quality DSLR camera perfect for professionals.', 2500.00, 'Warsaw', 1, 'noImage.jpg', 1, '123456789', 1, 0),
    ('Office Chair', 'Ergonomic chair for comfortable working.', 150.00, 'Krakow', 3, 'noImage.jpg', 1, '987654321', 1, 1),
    ('Smartphone Samsung Galaxy', 'Latest model, 128GB storage.', 999.99, 'Wroclaw', 4, 'noImage.jpg', 1, '456123789', 1, 0),
    ('Running Shoes', 'Comfortable and lightweight running shoes.', 75.00, 'Poznan', 5, 'noImage.jpg', 1, '321654987', 1, 1),
    ('Electric Guitar', 'Perfect for beginners and professionals.', 299.99, 'Gdansk', 7, 'noImage.jpg', 1, '789456123', 1, 0),
    ('Apartment for Rent', 'Two-bedroom apartment in the city center.', 1200.00, 'Lodz', 12, 'noImage.jpg', 1, '852741963', 1, 1),
    ('Mountain Bike', 'Durable bike for all terrains.', 500.00, 'Szczecin', 9, 'noImage.jpg', 1, '951753486', 1, 0),
    ('Piano Lessons', 'Learn piano with an experienced teacher.', 30.00, 'Katowice', 7, 'noImage.jpg', 1, '357159284', 1, 1),
    ('Pet Supplies', 'Wide range of supplies for dogs and cats.', 50.00, 'Rzeszow', 11, 'noImage.jpg', 1, '654987321', 1, 0),
    ('Yoga Mat', 'Non-slip yoga mat for daily practice.', 25.00, 'Lublin', 10, 'noImage.jpg', 1, '123789456', 1, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);


