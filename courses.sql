CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  instructor VARCHAR(100) NOT NULL,
  price INT NOT NULL,
  old_price INT NOT NULL,
  rating DECIMAL(3,1) DEFAULT 0,
  reviews INT DEFAULT 0,
  lessons INT DEFAULT 0,
  hours INT DEFAULT 0,
  description TEXT,
  short_desc TEXT,
  fullDescription TEXT,
  long_desc TEXT,
  image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
