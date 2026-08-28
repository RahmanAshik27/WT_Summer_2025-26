-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2026 at 01:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pawcare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `pet_type` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `customer_id`, `doctor_id`, `pet_name`, `pet_type`, `appointment_date`, `appointment_time`, `reason`, `status`, `created_at`) VALUES
(1, 2, 1, 'Bruno', 'Dog', '2026-08-30', '10:30:00', 'Routine health examination', 'Completed', '2026-08-26 07:41:45'),
(2, 3, 2, 'Milo', 'Cat', '2026-09-02', '12:00:00', 'Low appetite and weakness', 'Confirmed', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `item_type` enum('pet','product') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `customer_id`, `item_type`, `item_id`, `quantity`, `created_at`) VALUES
(1, 2, 'product', 1, 2, '2026-08-26 07:41:45'),
(2, 2, 'product', 8, 1, '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_agent_id` int(11) NOT NULL,
  `delivery_status` enum('Assigned','Picked Up','Out for Delivery','Delivered','Failed','Cancelled') DEFAULT 'Assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivered_at` datetime DEFAULT NULL,
  `delivery_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`delivery_id`, `order_id`, `delivery_agent_id`, `delivery_status`, `assigned_at`, `delivered_at`, `delivery_note`) VALUES
(1, 1, 6, 'Delivered', '2026-08-26 07:41:45', NULL, 'Contact customer before delivery.');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `available_days` varchar(100) DEFAULT NULL,
  `available_time` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('Available','Unavailable') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `available_days`, `available_time`, `bio`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Small Animal Medicine', 'DVM', 5, 800.00, 'Sunday, Tuesday, Thursday', '10:00 AM - 4:00 PM', 'Experienced veterinarian specializing in dogs, cats and small animals.', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(2, 5, 'Pet Surgery and General Care', 'DVM, MS in Veterinary Surgery', 7, 1000.00, 'Monday, Wednesday, Saturday', '11:00 AM - 5:00 PM', 'Veterinary doctor experienced in general treatment and minor pet surgery.', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `prescription` text DEFAULT NULL,
  `treatment_notes` text DEFAULT NULL,
  `next_visit_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `appointment_id`, `diagnosis`, `prescription`, `treatment_notes`, `next_visit_date`, `created_at`) VALUES
(1, 1, 'Healthy', 'No medicine required', 'Maintain proper diet, vaccination schedule and regular exercise.', '2026-09-30', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_method` enum('Pathao Fast','PetPanda Go','Speed Fast','Jhinku BD','Shop Pickup') NOT NULL DEFAULT 'Pathao Fast',
  `payment_method` enum('bKash','Nagad','Rocket','Credit Card','Cash on Delivery') NOT NULL DEFAULT 'Cash on Delivery',
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `order_status` enum('Pending','Confirmed','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `delivery_address`, `delivery_method`, `payment_method`, `payment_status`, `order_status`, `order_date`, `updated_at`) VALUES
(1, 2, 1650.00, 'Mirpur, Dhaka', 'Pathao Fast', 'Cash on Delivery', 'Paid', 'Delivered', '2026-08-26 07:41:45', '2026-08-28 11:34:02'),
(2, 8, 45550.00, '243 dokhin khan dhaka', 'Pathao Fast', 'bKash', 'Paid', 'Delivered', '2026-08-26 08:58:01', '2026-08-28 11:34:02'),
(3, 8, 16000.00, '243 dokhin khan dhaka', 'Jhinku BD', 'Cash on Delivery', 'Paid', 'Delivered', '2026-08-26 09:00:32', '2026-08-28 11:34:02'),
(4, 8, 32000.00, '243 dokhin khan dhaka', 'Jhinku BD', 'Credit Card', 'Paid', 'Delivered', '2026-08-26 09:08:26', '2026-08-28 11:34:02'),
(5, 8, 600.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Paid', 'Delivered', '2026-08-27 03:56:15', '2026-08-28 11:34:02'),
(6, 8, 250.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Paid', 'Delivered', '2026-08-27 03:56:20', '2026-08-28 11:34:02'),
(7, 8, 450.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Paid', 'Processing', '2026-08-27 03:56:25', '2026-08-28 11:34:02'),
(8, 8, 32000.00, 'Uttara dhaka 1230', 'Pathao Fast', 'Nagad', 'Paid', 'Processing', '2026-08-27 07:29:31', '2026-08-28 11:34:02'),
(9, 8, 450.00, 'Uttara dhaka 1230', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-27 08:39:07', '2026-08-27 08:39:07'),
(10, 8, 26800.00, 'Ashkona Uttara dhaka 1230', 'Shop Pickup', 'Rocket', 'Pending', 'Pending', '2026-08-27 20:11:48', '2026-08-27 20:11:48'),
(11, 8, 4500.00, 'Ashkona Uttara dhaka 1230', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Cancelled', '2026-08-27 20:20:31', '2026-08-28 11:34:02');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_type` enum('pet','product') NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `item_type`, `item_id`, `item_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 'product', 1, 'Premium Dog Food 1kg', 650.00, 2, 1300.00),
(2, 1, 'product', 8, 'Pet Collar', 350.00, 1, 350.00),
(3, 2, 'pet', 8, 'Mini Lop Rabbit', 8000.00, 1, 8000.00),
(4, 2, 'pet', 5, 'Cockatiel', 4500.00, 1, 4500.00),
(5, 2, 'pet', 4, 'British Shorthair', 32000.00, 1, 32000.00),
(6, 2, 'product', 1, 'Premium Dog Food 1kg', 650.00, 1, 650.00),
(7, 2, 'product', 12, 'Pet Grooming Brush', 400.00, 1, 400.00),
(8, 3, 'pet', 8, 'Mini Lop Rabbit', 8000.00, 2, 16000.00),
(9, 4, 'pet', 4, 'British Shorthair', 32000.00, 1, 32000.00),
(10, 5, 'pet', 7, 'Goldfish', 600.00, 1, 600.00),
(11, 6, 'product', 6, 'Rubber Ball', 250.00, 1, 250.00),
(12, 7, 'product', 2, 'Cat Food 500g', 450.00, 1, 450.00),
(13, 8, 'pet', 4, 'British Shorthair', 32000.00, 1, 32000.00),
(14, 9, 'product', 9, 'Pet Feeding Bowl', 450.00, 1, 450.00),
(15, 10, 'pet', 3, 'Persian Cat', 25000.00, 1, 25000.00),
(16, 10, 'product', 10, 'Pet Carrier Bag', 1800.00, 1, 1800.00),
(17, 11, 'pet', 5, 'Cockatiel', 4500.00, 1, 4500.00);

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `pet_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` varchar(30) DEFAULT NULL,
  `gender` enum('Male','Female','Mixed') DEFAULT 'Mixed',
  `color` varchar(50) DEFAULT NULL,
  `weight` varchar(30) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `health_status` enum('Healthy','Under Treatment') DEFAULT 'Healthy',
  `vaccination_status` enum('Vaccinated','Not Vaccinated') DEFAULT 'Vaccinated',
  `image` varchar(255) DEFAULT 'default_pet.jpg',
  `description` text DEFAULT NULL,
  `status` enum('Available','Out of Stock') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`pet_id`, `category_id`, `pet_name`, `breed`, `age`, `gender`, `color`, `weight`, `price`, `stock`, `health_status`, `vaccination_status`, `image`, `description`, `status`, `created_at`) VALUES
(1, 1, 'Golden Retriever Puppy', 'Golden Retriever', '3 Months', 'Male', 'Golden', '4.5 kg', 45000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and playful Golden Retriever puppy.', 'Available', '2026-08-26 07:41:45'),
(2, 1, 'German Shepherd Puppy', 'German Shepherd', '4 Months', 'Mixed', 'Black and Tan', '6 kg', 50000.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Active and intelligent German Shepherd puppies.', 'Available', '2026-08-26 07:41:45'),
(3, 2, 'Persian Cat', 'Persian', '8 Months', 'Female', 'White', '3 kg', 25000.00, 10, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Calm and friendly long-haired Persian cat.', 'Available', '2026-08-26 07:41:45'),
(4, 2, 'British Shorthair', 'British Shorthair', '6 Months', 'Male', 'Grey', '3.2 kg', 32000.00, 0, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly British Shorthair cat.', 'Available', '2026-08-26 07:41:45'),
(5, 3, 'Cockatiel', 'Cockatiel', '5 Months', 'Mixed', 'Grey and Yellow', '120 gm', 4500.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and social pet bird.', 'Available', '2026-08-26 07:41:45'),
(6, 3, 'Budgerigar', 'Budgie', '4 Months', 'Mixed', 'Green', '40 gm', 1800.00, 22, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and active small pet bird.', 'Available', '2026-08-26 07:41:45'),
(7, 4, 'Goldfish', 'Goldfish', '3 Months', 'Mixed', 'Orange', '50 gm', 600.00, 19, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Beautiful aquarium goldfish.', 'Available', '2026-08-26 07:41:45'),
(8, 5, 'Mini Lop Rabbit', 'Mini Lop', '5 Months', 'Female', 'White and Brown', '1.5 kg', 8000.00, 1, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly domestic rabbit.', 'Available', '2026-08-26 07:41:45'),
(9, 1, 'Labrador Retriever Puppy', 'Labrador Retriever', '4 Months', 'Male', 'Yellow', '5.2 kg', 42000.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and energetic Labrador Retriever puppy.', 'Available', '2026-08-28 04:20:22'),
(10, 1, 'Beagle Puppy', 'Beagle', '5 Months', 'Female', 'Tricolor', '4.8 kg', 35000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Playful and curious Beagle puppy.', 'Available', '2026-08-28 04:20:22'),
(11, 2, 'Siamese Cat', 'Siamese', '7 Months', 'Female', 'Cream and Brown', '3.1 kg', 22000.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Active and affectionate Siamese cat.', 'Available', '2026-08-28 04:20:22'),
(12, 2, 'Bengal Cat', 'Bengal', '8 Months', 'Male', 'Brown Spotted', '4 kg', 38000.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Intelligent and playful Bengal cat.', 'Available', '2026-08-28 04:20:22'),
(13, 3, 'Lovebird', 'Lovebird', '4 Months', 'Mixed', 'Green and Orange', '50 gm', 2500.00, 12, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and social Lovebird.', 'Available', '2026-08-28 04:20:22'),
(14, 4, 'Guppy Fish', 'Guppy', '3 Months', 'Mixed', 'Multicolor', '5 gm', 350.00, 30, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Small colorful freshwater Guppy fish.', 'Available', '2026-08-28 04:20:22'),
(15, 5, 'Dutch Rabbit', 'Dutch', '6 Months', 'Male', 'Black and White', '1.8 kg', 7500.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Calm and friendly Dutch rabbit.', 'Available', '2026-08-28 04:20:22'),
(16, 1, 'Shih Tzu Puppy', 'Shih Tzu', '5 Months', 'Female', 'White and Brown', '4.2 kg', 38000.00, 7, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small, friendly and affectionate Shih Tzu puppy.', 'Available', '2026-08-28 04:25:10'),
(17, 2, 'Maine Coon Cat', 'Maine Coon', '9 Months', 'Male', 'Brown and White', '5.1 kg', 42000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Large, gentle and playful Maine Coon cat.', 'Available', '2026-08-28 04:25:10'),
(18, 3, 'African Lovebird', 'African Lovebird', '5 Months', 'Mixed', 'Green and Red', '55 gm', 3200.00, 8, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and social African Lovebird.', 'Available', '2026-08-28 04:25:10'),
(19, 3, 'Zebra Finch', 'Zebra Finch', '4 Months', 'Mixed', 'Grey and Orange', '18 gm', 1800.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small, active and cheerful Zebra Finch.', 'Available', '2026-08-28 04:25:10'),
(20, 4, 'Betta Fish', 'Betta', '4 Months', 'Mixed', 'Blue and Red', '4 gm', 800.00, 15, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Beautiful and colorful freshwater Betta fish.', 'Available', '2026-08-28 04:25:10'),
(21, 4, 'Molly Fish', 'Molly', '3 Months', 'Mixed', 'Black', '5 gm', 450.00, 20, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Peaceful and easy-care freshwater Molly fish.', 'Available', '2026-08-28 04:25:10'),
(22, 4, 'Angelfish', 'Freshwater Angelfish', '5 Months', 'Mixed', 'Silver and Black', '15 gm', 1200.00, 9, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Elegant freshwater Angelfish suitable for home aquariums.', 'Available', '2026-08-28 04:25:10'),
(23, 5, 'Lionhead Rabbit', 'Lionhead', '6 Months', 'Female', 'White', '1.6 kg', 9000.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly Lionhead rabbit with a soft fluffy coat.', 'Available', '2026-08-28 04:25:10'),
(24, 5, 'Holland Lop Rabbit', 'Holland Lop', '5 Months', 'Male', 'Brown and White', '1.4 kg', 10000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Gentle and playful Holland Lop rabbit.', 'Available', '2026-08-28 04:25:10'),
(25, 5, 'Netherland Dwarf Rabbit', 'Netherland Dwarf', '5 Months', 'Female', 'Grey', '1.1 kg', 8500.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small and energetic Netherland Dwarf rabbit.', 'Available', '2026-08-28 04:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `pet_categories`
--

CREATE TABLE `pet_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_categories`
--

INSERT INTO `pet_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Dog', 'Different breeds of domestic dogs', '2026-08-26 07:41:45'),
(2, 'Cat', 'Different breeds of domestic cats', '2026-08-26 07:41:45'),
(3, 'Bird', 'Pet birds', '2026-08-26 07:41:45'),
(4, 'Fish', 'Aquarium and decorative fish', '2026-08-26 07:41:45'),
(5, 'Rabbit', 'Domestic rabbits', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default_product.jpg',
  `status` enum('Available','Out of Stock') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `brand`, `price`, `stock`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Premium Dog Food 1kg', 'Pedigree', 650.00, 19, 'Nutritious dry food for adult dogs', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 08:58:01'),
(2, 1, 'Cat Food 500g', 'Whiskas', 450.00, 24, 'Dry food for adult cats', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-27 03:56:25'),
(3, 1, 'Rabbit Food 1kg', 'BunnyCare', 550.00, 12, 'Balanced food for domestic rabbits', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(4, 2, 'Pet Vitamin Supplement', 'VetCare', 700.00, 15, 'Vitamin supplement for pets', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(5, 2, 'Pet Antiseptic Spray', 'PetMed', 380.00, 18, 'Antiseptic spray for minor pet wounds', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(6, 3, 'Rubber Ball', 'PetPlay', 250.00, 29, 'Durable rubber toy for dogs', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-27 03:56:20'),
(7, 3, 'Cat Feather Toy', 'KittyFun', 300.00, 20, 'Interactive feather toy for cats', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(8, 4, 'Pet Collar', 'PawStyle', 350.00, 25, 'Adjustable collar for pets', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(9, 4, 'Pet Feeding Bowl', 'PawHome', 450.00, 19, 'Durable pet feeding bowl', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-27 08:39:07'),
(10, 4, 'Pet Carrier Bag', 'TravelPaw', 1800.00, 7, 'Comfortable carrier bag for small pets', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-27 20:11:48'),
(11, 5, 'Pet Shampoo', 'PetCare', 550.00, 15, 'Gentle cleaning shampoo for pets', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(12, 5, 'Pet Grooming Brush', 'PawBrush', 400.00, 17, 'Soft grooming brush for dogs and cats', 'default_product.jpg', 'Available', '2026-08-26 07:41:45', '2026-08-26 08:58:01'),
(13, 1, 'Bird Seed Mix 500g', 'FeatherFresh', 320.00, 18, 'Healthy mixed seed food for common pet birds.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(14, 1, 'Fish Flakes 200g', 'AquaMeal', 280.00, 25, 'Balanced daily flake food for aquarium fish.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(15, 2, 'Pet Calcium Supplement', 'VetCare', 650.00, 10, 'Calcium supplement for supporting healthy bones and teeth.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(16, 2, 'Flea and Tick Spray', 'PetMed', 520.00, 8, 'Pet care spray for controlling fleas and ticks.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(17, 2, 'Pet Ear Cleaning Solution', 'HealthyPaw', 420.00, 4, 'Gentle ear cleaning solution for dogs and cats.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(18, 3, 'Dog Rope Toy', 'PetPlay', 380.00, 16, 'Strong rope toy for chewing and interactive dog play.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(19, 3, 'Cat Toy Mouse', 'KittyFun', 220.00, 24, 'Small interactive mouse toy for cats.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(20, 3, 'Bird Swing Toy', 'FeatherFun', 350.00, 5, 'Colorful hanging swing toy for pet birds.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(21, 4, 'Adjustable Pet Leash', 'PawStyle', 600.00, 14, 'Strong adjustable leash for everyday walking.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(22, 4, 'Pet Water Bottle', 'PawHome', 750.00, 0, 'Portable drinking bottle for pets during travel.', 'default_product.jpg', 'Out of Stock', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(23, 5, 'Pet Nail Clipper', 'PawBrush', 480.00, 12, 'Safe nail clipper suitable for dogs and cats.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(24, 5, 'Pet Cleaning Wipes', 'PetCare', 350.00, 22, 'Soft cleaning wipes for quick daily pet hygiene.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12'),
(25, 5, 'Pet Toothbrush Set', 'FreshPaw', 390.00, 3, 'Toothbrush set designed for maintaining pet dental hygiene.', 'default_product.jpg', 'Available', '2026-08-28 04:34:12', '2026-08-28 04:34:12');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Pet Food', 'Food products for pets', '2026-08-26 07:41:45'),
(2, 'Medicine', 'Pet healthcare and medicine products', '2026-08-26 07:41:45'),
(3, 'Toys', 'Toys for pets', '2026-08-26 07:41:45'),
(4, 'Accessories', 'Pet accessories and daily use products', '2026-08-26 07:41:45'),
(5, 'Grooming', 'Pet grooming and cleaning products', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `item_type` enum('pet','product') NOT NULL,
  `item_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('Visible','Hidden') DEFAULT 'Visible',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `customer_id`, `item_type`, `item_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 2, 'product', 1, 5, 'Good quality food and fast service.', 'Visible', '2026-08-26 07:41:45'),
(2, 3, 'pet', 3, 5, 'The Persian cat was healthy and well cared for.', 'Visible', '2026-08-26 07:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer','doctor','delivery') NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `phone`, `gender`, `password`, `role`, `profile_image`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PawCare Administrator', 'admin', 'admin@pawcare.com', '01700000001', 'Male', '$2y$10$sMylywzYjH6pof2HfyhPIeR/sMjmsgYDC.5.5yxUs4OnakqNdH9ai', 'admin', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-26 07:41:45', '2026-08-27 20:59:59'),
(2, 'Rahim Ahmed', 'rahim_ahmed', 'customer1@pawcare.com', '01700000002', 'Male', '$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW', 'customer', 'default.png', 'Mirpur, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(3, 'Sadia Islam', 'sadia_islam', 'customer2@pawcare.com', '01700000003', 'Female', '$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW', 'customer', 'default.png', 'Uttara, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(4, 'Dr. Hasan Rahman', 'dr_hasan', 'doctor1@pawcare.com', '01700000004', 'Male', '$2y$12$oCi9cf6OD5VZQ3PSLTazseYX.VnAhKTWlTpVeqYzjNa/AzxmhRw0u', 'doctor', 'default.png', 'Dhanmondi, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(5, 'Dr. Nusrat Jahan', 'dr_nusrat', 'doctor2@pawcare.com', '01700000005', 'Female', '$2y$12$oCi9cf6OD5VZQ3PSLTazseYX.VnAhKTWlTpVeqYzjNa/AzxmhRw0u', 'doctor', 'default.png', 'Banani, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(6, 'Karim Uddin', 'karim_delivery', 'delivery1@pawcare.com', '01700000006', 'Male', '$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa', 'delivery', 'default.png', 'Mohammadpur, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(7, 'Rafi Ahmed', 'rafi_delivery', 'delivery2@pawcare.com', '01700000007', 'Male', '$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa', 'delivery', 'default.png', 'Badda, Dhaka', 'active', '2026-08-26 07:41:45', '2026-08-26 07:41:45'),
(8, 'ashikur Rahman Emon', 'ashik', 'ashikikurRahmanEMon@gmail.com', '01626318821', NULL, '$2y$10$rJA2fYkjlWZNhwHCScIDUOgxIU92so8BW24.dI3tt13nP/CY6DowW', 'customer', 'default.png', 'Ashkona Uttara dhaka 1230', 'active', '2026-08-26 08:37:32', '2026-08-27 20:11:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart_item` (`customer_id`,`item_type`,`item_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`delivery_id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `delivery_agent_id` (`delivery_agent_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`pet_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `pet_categories`
--
ALTER TABLE `pet_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pet_categories`
--
ALTER TABLE `pet_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`delivery_agent_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `pet_categories` (`category_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
