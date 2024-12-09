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
    category_id INT NULL, -- Klucz obcy do tabeli categories
    image_path VARCHAR(255),
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL, -- Relacja z kategorią
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Relacja z użytkownikiem
);

-- Indeksy dla optymalizacji wyszukiwania
CREATE INDEX idx_user_username ON users(username);
CREATE INDEX idx_user_email ON users(email); -- Indeks na email (szybsze wyszukiwanie przy resetowaniu hasła)
CREATE INDEX idx_ads_category_id ON ads(category_id); -- Indeks na category_id w tabeli ads
CREATE INDEX idx_ads_user_id ON ads(user_id); -- Indeks na user_id w tabeli ads


-- Dodanie kategorii do tabeli categories
INSERT INTO categories (id, name) VALUES
(1, 'Electronics'),
(2, 'Clothing'),
(3, 'Furniture'),
(4, 'Toys'),
(5, 'Books'),
(6, 'Sports'),
(7, 'Home Appliances'),
(8, 'Health & Beauty'),
(9, 'Food & Beverages'),
(10, 'Jewelry');
