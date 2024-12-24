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
    condit TINYINT(1) DEFAULT 0, -- Nowe pole
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
CREATE INDEX idx_ads_active_condit ON ads(active, condit);
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

--dodawanie do ogloszen
-- Przykładowy skrypt do generowania danych testowych
INSERT INTO ads (title, description, price, localization, category_id, image_path, user_id, phone_number, active, condit)
VALUES
    ('Canon EOS 5D Camera', 'High-quality DSLR camera perfect for professionals.', 2500.00, 'Warsaw', 1, 'noImage.jpg', 5, '123456789', 1, 0),
    ('Office Chair', 'Ergonomic chair for comfortable working.', 150.00, 'Krakow', 3, 'noImage.jpg', 5, '987654321', 1, 1),
    ('Smartphone Samsung Galaxy', 'Latest model, 128GB storage.', 999.99, 'Wroclaw', 4, 'noImage.jpg', 5, '456123789', 1, 0),
    ('Running Shoes', 'Comfortable and lightweight running shoes.', 75.00, 'Poznan', 5, 'noImage.jpg', 5, '321654987', 1, 1),
    ('Electric Guitar', 'Perfect for beginners and professionals.', 299.99, 'Gdansk', 7, 'noImage.jpg', 5, '789456123', 1, 0),
    ('Apartment for Rent', 'Two-bedroom apartment in the city center.', 1200.00, 'Lodz', 12, 'noImage.jpg', 5, '852741963', 1, 1),
    ('Mountain Bike', 'Durable bike for all terrains.', 500.00, 'Szczecin', 9, 'noImage.jpg', 5, '951753486', 1, 0),
    ('Piano Lessons', 'Learn piano with an experienced teacher.', 30.00, 'Katowice', 7, 'noImage.jpg', 5, '357159284', 1, 1),
    ('Pet Supplies', 'Wide range of supplies for dogs and cats.', 50.00, 'Rzeszow', 11, 'noImage.jpg', 5, '654987321', 1, 0),
    ('Yoga Mat', 'Non-slip yoga mat for daily practice.', 25.00, 'Lublin', 10, 'noImage.jpg', 5, '123789456', 1, 1),
    ('Gaming Laptop', 'High-performance laptop for gaming.', 3500.00, 'Warsaw', 4, 'noImage.jpg', 5, '111222333', 1, 0),
    ('Leather Jacket', 'Stylish and durable leather jacket.', 200.00, 'Krakow', 5, 'noImage.jpg', 5, '444555666', 1, 1),
    ('Baby Stroller', 'Comfortable and safe stroller for your baby.', 300.00, 'Wroclaw', 3, 'noImage.jpg', 5, '777888999', 1, 0),
    ('Electric Scooter', 'Eco-friendly and fast electric scooter.', 800.00, 'Poznan', 9, 'noImage.jpg', 5, '111444777', 1, 1),
    ('Digital Piano', 'Perfect for home practice and concerts.', 600.00, 'Gdansk', 7, 'noImage.jpg', 5, '222555888', 1, 0),
    ('House for Sale', 'Spacious family house in a quiet area.', 250000.00, 'Lodz', 12, 'noImage.jpg', 5, '333666999', 1, 1),
    ('Treadmill', 'Keep fit with this high-quality treadmill.', 700.00, 'Szczecin', 9, 'noImage.jpg', 5, '444777111', 1, 0),
    ('Guitar Lessons', 'Affordable guitar lessons for all levels.', 40.00, 'Katowice', 7, 'noImage.jpg', 5, '555888222', 1, 1),
    ('Cat Tree', 'Perfect playground for your cat.', 90.00, 'Rzeszow', 11, 'noImage.jpg', 5, '666999333', 1, 0),
    ('Fitness Tracker', 'Track your workouts and health metrics.', 50.00, 'Lublin', 10, 'noImage.jpg', 5, '777111444', 1, 1),
    ('Used Car', 'Reliable car in good condition.', 5000.00, 'Warsaw', 6, 'noImage.jpg', 5, '888222555', 1, 0),
    ('Winter Jacket', 'Warm and comfortable jacket for winter.', 120.00, 'Krakow', 5, 'noImage.jpg', 5, '999333666', 1, 1),
    ('Gaming Console', 'PlayStation 5 in excellent condition.', 400.00, 'Wroclaw', 4, 'noImage.jpg', 5, '123456789', 1, 0),
    ('Garden Tools Set', 'Complete set of tools for your garden.', 60.00, 'Poznan', 3, 'noImage.jpg', 5, '987654321', 1, 1),
    ('Electric Violin', 'Great instrument for modern musicians.', 350.00, 'Gdansk', 7, 'noImage.jpg', 5, '456123789', 1, 0),
    ('Room for Rent', 'Cozy room available in shared apartment.', 400.00, 'Lodz', 12, 'noImage.jpg', 5, '321654987', 1, 1),
    ('Camping Tent', 'Durable tent for outdoor adventures.', 150.00, 'Szczecin', 9, 'noImage.jpg', 5, '789456123', 1, 0),
    ('Cooking Classes', 'Learn to cook delicious meals.', 50.00, 'Katowice', 7, 'noImage.jpg', 5, '852741963', 1, 1),
    ('Dog Bed', 'Comfortable bed for your dog.', 40.00, 'Rzeszow', 11, 'noImage.jpg', 5, '951753486', 1, 0),
    ('Yoga Blocks', 'Essential for improving your yoga practice.', 20.00, 'Lublin', 10, 'noImage.jpg', 5, '357159284', 1, 1);

