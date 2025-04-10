CREATE DATABASE IF NOT EXISTS sd_app;
USE sd_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Create default admin
INSERT INTO users (name, email, username, password, role)
VALUES ('Admin User', 'admin@example.com', 'admin',
        '$2y$10$JuyK7h1nvwEK3Tz4a/fEQeQkFwR9S2FnHED7fpBOxUMUARJ7wRRTe', 'Admin');


/*
Username: admin
Password: Admin@123
*/