-- Database Cafe Ordering
CREATE DATABASE IF NOT EXISTS cafe_ordering;
USE cafe_ordering;

-- Users (waiter, dapur, kasir, admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('waiter','dapur','kasir','admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kategori menu
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-utensils'
);

-- Menu items
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    nama VARCHAR(200) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT '',
    tersedia TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
);

-- Orders
CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_order VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    hp VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT '',
    no_kursi VARCHAR(20) NOT NULL,
    status ENUM('baru','diproses','selesai','dibayar','dibatalkan') DEFAULT 'baru',
    total DECIMAL(12,2) DEFAULT 0,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items
CREATE TABLE detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    menu_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    harga DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    status ENUM('menunggu','dimasak','selesai') DEFAULT 'menunggu',
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Default users
INSERT INTO users (username, password, nama, role) VALUES
('waiter', '$2y$10$dummy', 'Waiter', 'waiter'),
('dapur', '$2y$10$dummy', 'Koki Dapur', 'dapur'),
('kasir', '$2y$10$dummy', 'Kasir', 'kasir'),
('admin', '$2y$10$dummy', 'Administrator', 'admin');

-- Sample kategori
INSERT INTO kategori (nama, icon) VALUES
('Makanan', 'fa-utensils'),
('Minuman', 'fa-coffee'),
('Cemilan', 'fa-cookie-bite');

-- Sample menu
INSERT INTO menu (kategori_id, nama, harga, deskripsi) VALUES
(1, 'Nasi Goreng Spesial', 35000, 'Nasi goreng dengan telur, ayam, dan sayuran'),
(1, 'Mie Goreng', 30000, 'Mie goreng lengkap dengan bakso dan sayur'),
(1, 'Ayam Penyet', 40000, 'Ayam goreng dengan sambal terasi dan lalapan'),
(2, 'Es Teh Manis', 5000, 'Es teh manis segar'),
(2, 'Kopi Hitam', 10000, 'Kopi hitam pilihan'),
(2, 'Jus Jeruk', 15000, 'Jus jeruk peras segar'),
(3, 'Kentang Goreng', 20000, 'Kentang goreng crispy dengan saus sambal'),
(3, 'Pisang Goreng', 12000, 'Pisang goreng madu');
