-- =========================================
-- PawCare Database Schema
-- Pet Shop and Veterinary Service Management System
-- =========================================

DROP DATABASE IF EXISTS pawcare_db;

CREATE DATABASE pawcare_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE pawcare_db;

-- =========================================
-- 1. USERS
-- =========================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) UNIQUE,
    gender ENUM('Male', 'Female', 'Other') NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer', 'doctor', 'delivery') NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================
-- 2. DOCTORS
-- =========================================

CREATE TABLE doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    specialization VARCHAR(100) NOT NULL,
    qualification VARCHAR(150),
    experience_years INT DEFAULT 0,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    available_days VARCHAR(100),
    available_time VARCHAR(100),
    bio TEXT,
    status ENUM('Available', 'Unavailable')
        DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- =========================================
-- 3. PET CATEGORIES
-- =========================================

CREATE TABLE pet_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- 4. PETS
-- =========================================

CREATE TABLE pets (
    pet_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    pet_name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    age VARCHAR(30),
    gender ENUM('Male', 'Female', 'Mixed')
        DEFAULT 'Mixed',
    color VARCHAR(50),
    weight VARCHAR(30),
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    health_status ENUM('Healthy', 'Under Treatment')
        DEFAULT 'Healthy',
    vaccination_status ENUM('Vaccinated', 'Not Vaccinated')
        DEFAULT 'Vaccinated',
    image VARCHAR(255)
        DEFAULT 'default_pet.jpg',
    description TEXT,
    status ENUM('Available', 'Out of Stock')
        DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
        REFERENCES pet_categories(category_id)
);

-- =========================================
-- 5. PRODUCT CATEGORIES
-- =========================================

CREATE TABLE product_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- 6. PRODUCTS
-- =========================================

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image VARCHAR(255)
        DEFAULT 'default_product.jpg',
    status ENUM('Available', 'Out of Stock')
        DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
        REFERENCES product_categories(category_id)
);

-- =========================================
-- 7. CARTS
-- =========================================

CREATE TABLE carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    item_type ENUM('pet', 'product') NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT unique_cart_item
        UNIQUE (customer_id, item_type, item_id)
);

-- =========================================
-- 8. ORDERS
-- =========================================

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
   total_amount DECIMAL(10,2) NOT NULL,

delivery_address TEXT NOT NULL,

delivery_method ENUM(
    'Pathao Fast',
    'PetPanda Go',
    'Speed Fast',
    'Jhinku BD',
    'Shop Pickup'
)
NOT NULL DEFAULT 'Pathao Fast',

payment_method ENUM(
    'bKash',
    'Nagad',
    'Rocket',
    'Credit Card',
    'Cash on Delivery'
)
NOT NULL DEFAULT 'Cash on Delivery',
    payment_status ENUM('Pending', 'Paid')
        DEFAULT 'Pending',
    order_status ENUM(
        'Pending',
        'Confirmed',
        'Processing',
        'Shipped',
        'Delivered',
        'Cancelled'
    ) DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
        REFERENCES users(user_id)
);

-- =========================================
-- 9. ORDER ITEMS
-- =========================================

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_type ENUM('pet', 'product') NOT NULL,
    item_id INT NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE
);

-- =========================================
-- 10. APPOINTMENTS
-- =========================================

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    doctor_id INT NOT NULL,
    pet_name VARCHAR(100) NOT NULL,
    pet_type VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM(
        'Pending',
        'Confirmed',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
        REFERENCES users(user_id),

    FOREIGN KEY (doctor_id)
        REFERENCES doctors(doctor_id)
);

-- =========================================
-- 11. MEDICAL RECORDS
-- =========================================

CREATE TABLE medical_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL UNIQUE,
    diagnosis TEXT NOT NULL,
    prescription TEXT,
    treatment_notes TEXT,
    next_visit_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (appointment_id)
        REFERENCES appointments(appointment_id)
        ON DELETE CASCADE
);

-- =========================================
-- 12. DELIVERIES
-- =========================================

CREATE TABLE deliveries (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    delivery_agent_id INT NOT NULL,
    delivery_status ENUM(
        'Assigned',
        'Picked Up',
        'Out for Delivery',
        'Delivered',
        'Failed',
        'Cancelled'
    ) DEFAULT 'Assigned',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at DATETIME NULL,
    delivery_note TEXT,

    FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE,

    FOREIGN KEY (delivery_agent_id)
        REFERENCES users(user_id)
);

-- =========================================
-- 13. REVIEWS
-- =========================================

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    item_type ENUM('pet', 'product') NOT NULL,
    item_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    status ENUM('Visible', 'Hidden')
        DEFAULT 'Visible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CHECK (rating BETWEEN 1 AND 5)
);