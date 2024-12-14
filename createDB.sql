-- Tabela użytkowników (users)
CREATE TABLE users (
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
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Tabela ogłoszeń (ads)
CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    localization VARCHAR(100) NOT NULL,
    category_id INT NULL,
    image_path TEXT,
    user_id INT NOT NULL,
    phone_number VARCHAR(15) NULL,
    active TINYINT(1) DEFAULT 1, -- Nowe pole
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indeksy dla optymalizacji wyszukiwania
CREATE INDEX idx_user_username ON users(username);
CREATE INDEX idx_user_email ON users(email); -- Indeks na email (szybsze wyszukiwanie przy resetowaniu hasła)
CREATE INDEX idx_ads_category_id ON ads(category_id); -- Indeks na category_id w tabeli ads
CREATE INDEX idx_ads_user_id ON ads(user_id); -- Indeks na user_id w tabeli ads
CREATE INDEX idx_ads_active ON ads(active);
CREATE INDEX idx_ads_active_category ON ads(active, category_id);
CREATE INDEX idx_ads_active_user ON ads(active, session_id);
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
(12, 'Real Estate');
