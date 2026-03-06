-- База данных: perfect_doors
-- Создание необходимых таблиц для управления каталогом и заказами

-- Таблица категорий товаров
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица товаров (двери)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Таблица заказов
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    order_date DATE,
    status ENUM('new', 'in_progress', 'completed', 'cancelled') DEFAULT 'new',
    total_amount DECIMAL(10, 2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица обращений клиентов (с формы обратной связи)
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    is_processed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Заполнение категорий (если пусто)
INSERT INTO categories (name, description) VALUES 
('Входные двери', 'Входные двери для квартир и домов'),
('Межкомнатные двери', 'Межкомнатные двери различных типов'),
('Раздвижные двери', 'Раздвижные системы'),
('Складные двери', 'Складные двери-книжки');

-- Заполнение тестовых товаров (если пусто)
INSERT INTO products (name, description, price, category_id, image) VALUES 
('Эконом', 'Входная дверь эконом класса', 8500, 1, 'econom.png'),
('Стандарт', 'Входная дверь стандарт класса', 15800, 1, 'standart.png'),
('Премиум', 'Входная дверь премиум класса', 24050, 1, 'premium.png'),
('Терморазрыв', 'Входная дверь с терморазрывом', 29380, 1, 'termorazriv.png');

-- Примеры статусов заказов:
-- new - новый заказ
-- in_progress - в работе
-- completed - выполнен
-- cancelled - отменен

