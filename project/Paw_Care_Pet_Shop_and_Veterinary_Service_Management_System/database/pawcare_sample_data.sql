-- PawCare Pet Shop and Veterinary Service Management System
-- Functional sample_data.sql
-- Import after schema.sql

USE `pawcare_db`;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `reviews`;
DELETE FROM `medical_records`;
DELETE FROM `appointments`;
DELETE FROM `deliveries`;
DELETE FROM `carts`;
DELETE FROM `order_items`;
DELETE FROM `orders`;
DELETE FROM `products`;
DELETE FROM `pets`;
DELETE FROM `doctors`;
DELETE FROM `product_categories`;
DELETE FROM `pet_categories`;
DELETE FROM `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- users
INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `phone`, `gender`, `password`, `role`, `profile_image`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PawCare Administrator', 'admin', 'admin@pawcare.com', '01700000001', 'Male', '$2y$10$sMylywzYjH6pof2HfyhPIeR/sMjmsgYDC.5.5yxUs4OnakqNdH9ai', 'admin', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-26 13:41:45', '2026-08-28 02:59:59'),
(2, 'Rahim Ahmed', 'rahim_ahmed', 'customer1@pawcare.com', '01700000002', 'Male', '$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW', 'customer', 'default.png', 'Mirpur, Dhaka', 'active', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(3, 'Sadia Islam', 'sadia_islam', 'customer2@pawcare.com', '01700000003', 'Female', '$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW', 'customer', 'default.png', 'Uttara, Dhaka', 'active', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(4, 'Md. Ashikur Rahman Mirza', 'ashik', 'ashik.doctor@pawcare.com', '01710000004', 'Male', '$2y$12$xzs6ypfh98akrIT0wIdTCu6eW3gGmWI7/DCkdgmG412J3VMHnFQya', 'doctor', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(5, 'Md. Mostafizur Rahman', 'mustafiz', 'mustafiz.doctor@pawcare.com', '01710000005', 'Male', '$2y$12$xzs6ypfh98akrIT0wIdTCu6eW3gGmWI7/DCkdgmG412J3VMHnFQya', 'doctor', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(9, 'Shahed Bhuiyan Rony', 'roni', 'roni.doctor@pawcare.com', '01710000009', 'Male', '$2y$12$xzs6ypfh98akrIT0wIdTCu6eW3gGmWI7/DCkdgmG412J3VMHnFQya', 'doctor', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(10, 'Shajia Afrin', 'shajia', 'shajia.doctor@pawcare.com', '01710000010', 'Female', '$2y$12$xzs6ypfh98akrIT0wIdTCu6eW3gGmWI7/DCkdgmG412J3VMHnFQya', 'doctor', 'default.png', 'Dhaka, Bangladesh', 'active', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(6, 'Karim Uddin', 'karim_delivery', 'delivery1@pawcare.com', '01700000006', 'Male', '$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa', 'delivery', 'default.png', 'Mohammadpur, Dhaka', 'active', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(7, 'Rafi Ahmed', 'rafi_delivery', 'delivery2@pawcare.com', '01700000007', 'Male', '$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa', 'delivery', 'default.png', 'Badda, Dhaka', 'active', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(8, 'ashikur Rahman Emon', 'emon_customer', 'ashikikurRahmanEMon@gmail.com', '01626318821', NULL, '$2y$10$rJA2fYkjlWZNhwHCScIDUOgxIU92so8BW24.dI3tt13nP/CY6DowW', 'customer', 'default.png', 'Ashkona Uttara dhaka 1230', 'active', '2026-08-26 14:37:32', '2026-08-28 02:11:30');

-- pet_categories
INSERT INTO `pet_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Dog', 'Different breeds of domestic dogs', '2026-08-26 13:41:45'),
(2, 'Cat', 'Different breeds of domestic cats', '2026-08-26 13:41:45'),
(3, 'Bird', 'Pet birds', '2026-08-26 13:41:45'),
(4, 'Fish', 'Aquarium and decorative fish', '2026-08-26 13:41:45'),
(5, 'Rabbit', 'Domestic rabbits', '2026-08-26 13:41:45');

-- product_categories
INSERT INTO `product_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Pet Food', 'Food products for pets', '2026-08-26 13:41:45'),
(2, 'Medicine', 'Pet healthcare and medicine products', '2026-08-26 13:41:45'),
(3, 'Toys', 'Toys for pets', '2026-08-26 13:41:45'),
(4, 'Accessories', 'Pet accessories and daily use products', '2026-08-26 13:41:45'),
(5, 'Grooming', 'Pet grooming and cleaning products', '2026-08-26 13:41:45');

-- doctors
INSERT INTO `doctors` (`doctor_id`, `user_id`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `available_days`, `available_time`, `bio`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Small Animal Medicine', 'DVM', 5, 800.00, 'Sunday, Tuesday, Thursday', '10:00 AM - 4:00 PM', 'Veterinary specialist experienced in general care of dogs, cats and small animals.', 'Available', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(2, 5, 'Pet Surgery and General Care', 'DVM, MS in Veterinary Surgery', 7, 1000.00, 'Monday, Wednesday, Saturday', '11:00 AM - 5:00 PM', 'Veterinary specialist experienced in general treatment and minor pet surgery.', 'Available', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(3, 9, 'Pet Medicine and Vaccination', 'DVM', 4, 750.00, 'Sunday, Monday, Wednesday', '9:00 AM - 2:00 PM', 'Veterinary specialist focused on pet medicine, vaccination and routine health care.', 'Available', '2026-08-29 10:00:00', '2026-08-29 10:00:00'),
(4, 10, 'Pet Nutrition and General Care', 'DVM', 4, 750.00, 'Tuesday, Thursday, Saturday', '12:00 PM - 6:00 PM', 'Veterinary specialist focused on nutrition, preventive care and general pet health.', 'Available', '2026-08-29 10:00:00', '2026-08-29 10:00:00');

-- pets
INSERT INTO `pets` (`pet_id`, `category_id`, `pet_name`, `breed`, `age`, `gender`, `color`, `weight`, `price`, `stock`, `health_status`, `vaccination_status`, `image`, `description`, `status`, `created_at`) VALUES
(1, 1, 'Golden Retriever Puppy', 'Golden Retriever', '3 Months', 'Male', 'Golden', '4.5 kg', 45000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and playful Golden Retriever puppy.', 'Available', '2026-08-26 13:41:45'),
(2, 1, 'German Shepherd Puppy', 'German Shepherd', '4 Months', 'Mixed', 'Black and Tan', '6 kg', 50000.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Active and intelligent German Shepherd puppies.', 'Available', '2026-08-26 13:41:45'),
(3, 2, 'Persian Cat', 'Persian', '8 Months', 'Female', 'White', '3 kg', 25000.00, 10, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Calm and friendly long-haired Persian cat.', 'Available', '2026-08-26 13:41:45'),
(4, 2, 'British Shorthair', 'British Shorthair', '6 Months', 'Male', 'Grey', '3.2 kg', 32000.00, 0, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly British Shorthair cat.', 'Available', '2026-08-26 13:41:45'),
(5, 3, 'Cockatiel', 'Cockatiel', '5 Months', 'Mixed', 'Grey and Yellow', '120 gm', 4500.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and social pet bird.', 'Available', '2026-08-26 13:41:45'),
(6, 3, 'Budgerigar', 'Budgie', '4 Months', 'Mixed', 'Green', '40 gm', 1800.00, 22, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and active small pet bird.', 'Available', '2026-08-26 13:41:45'),
(7, 4, 'Goldfish', 'Goldfish', '3 Months', 'Mixed', 'Orange', '50 gm', 600.00, 19, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Beautiful aquarium goldfish.', 'Available', '2026-08-26 13:41:45'),
(8, 5, 'Mini Lop Rabbit', 'Mini Lop', '5 Months', 'Female', 'White and Brown', '1.5 kg', 8000.00, 1, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly domestic rabbit.', 'Available', '2026-08-26 13:41:45'),
(9, 1, 'Labrador Retriever Puppy', 'Labrador Retriever', '4 Months', 'Male', 'Yellow', '5.2 kg', 42000.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly and energetic Labrador Retriever puppy.', 'Available', '2026-08-28 10:20:22'),
(10, 1, 'Beagle Puppy', 'Beagle', '5 Months', 'Female', 'Tricolor', '4.8 kg', 35000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Playful and curious Beagle puppy.', 'Available', '2026-08-28 10:20:22'),
(11, 2, 'Siamese Cat', 'Siamese', '7 Months', 'Female', 'Cream and Brown', '3.1 kg', 22000.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Active and affectionate Siamese cat.', 'Available', '2026-08-28 10:20:22'),
(12, 2, 'Bengal Cat', 'Bengal', '8 Months', 'Male', 'Brown Spotted', '4 kg', 38000.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Intelligent and playful Bengal cat.', 'Available', '2026-08-28 10:20:22'),
(13, 3, 'Lovebird', 'Lovebird', '4 Months', 'Mixed', 'Green and Orange', '50 gm', 2500.00, 12, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and social Lovebird.', 'Available', '2026-08-28 10:20:22'),
(14, 4, 'Guppy Fish', 'Guppy', '3 Months', 'Mixed', 'Multicolor', '5 gm', 350.00, 30, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Small colorful freshwater Guppy fish.', 'Available', '2026-08-28 10:20:22'),
(15, 5, 'Dutch Rabbit', 'Dutch', '6 Months', 'Male', 'Black and White', '1.8 kg', 7500.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Calm and friendly Dutch rabbit.', 'Available', '2026-08-28 10:20:22'),
(16, 1, 'Shih Tzu Puppy', 'Shih Tzu', '5 Months', 'Female', 'White and Brown', '4.2 kg', 38000.00, 7, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small, friendly and affectionate Shih Tzu puppy.', 'Available', '2026-08-28 10:25:10'),
(17, 2, 'Maine Coon Cat', 'Maine Coon', '9 Months', 'Male', 'Brown and White', '5.1 kg', 42000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Large, gentle and playful Maine Coon cat.', 'Available', '2026-08-28 10:25:10'),
(18, 3, 'African Lovebird', 'African Lovebird', '5 Months', 'Mixed', 'Green and Red', '55 gm', 3200.00, 8, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Colorful and social African Lovebird.', 'Available', '2026-08-28 10:25:10'),
(19, 3, 'Zebra Finch', 'Zebra Finch', '4 Months', 'Mixed', 'Grey and Orange', '18 gm', 1800.00, 5, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small, active and cheerful Zebra Finch.', 'Available', '2026-08-28 10:25:10'),
(20, 4, 'Betta Fish', 'Betta', '4 Months', 'Mixed', 'Blue and Red', '4 gm', 800.00, 15, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Beautiful and colorful freshwater Betta fish.', 'Available', '2026-08-28 10:25:10'),
(21, 4, 'Molly Fish', 'Molly', '3 Months', 'Mixed', 'Black', '5 gm', 450.00, 20, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Peaceful and easy-care freshwater Molly fish.', 'Available', '2026-08-28 10:25:10'),
(22, 4, 'Angelfish', 'Freshwater Angelfish', '5 Months', 'Mixed', 'Silver and Black', '15 gm', 1200.00, 9, 'Healthy', 'Not Vaccinated', 'default_pet.jpg', 'Elegant freshwater Angelfish suitable for home aquariums.', 'Available', '2026-08-28 10:25:10'),
(23, 5, 'Lionhead Rabbit', 'Lionhead', '6 Months', 'Female', 'White', '1.6 kg', 9000.00, 6, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Friendly Lionhead rabbit with a soft fluffy coat.', 'Available', '2026-08-28 10:25:10'),
(24, 5, 'Holland Lop Rabbit', 'Holland Lop', '5 Months', 'Male', 'Brown and White', '1.4 kg', 10000.00, 4, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Gentle and playful Holland Lop rabbit.', 'Available', '2026-08-28 10:25:10'),
(25, 5, 'Netherland Dwarf Rabbit', 'Netherland Dwarf', '5 Months', 'Female', 'Grey', '1.1 kg', 8500.00, 3, 'Healthy', 'Vaccinated', 'default_pet.jpg', 'Small and energetic Netherland Dwarf rabbit.', 'Available', '2026-08-28 10:25:10');

-- products
INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `brand`, `price`, `stock`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Premium Dog Food 1kg', 'Pedigree', 650.00, 19, 'Nutritious dry food for adult dogs', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 14:58:01'),
(2, 1, 'Cat Food 500g', 'Whiskas', 450.00, 24, 'Dry food for adult cats', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-27 09:56:25'),
(3, 1, 'Rabbit Food 1kg', 'BunnyCare', 550.00, 12, 'Balanced food for domestic rabbits', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(4, 2, 'Pet Vitamin Supplement', 'VetCare', 700.00, 15, 'Vitamin supplement for pets', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(5, 2, 'Pet Antiseptic Spray', 'PetMed', 380.00, 18, 'Antiseptic spray for minor pet wounds', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(6, 3, 'Rubber Ball', 'PetPlay', 250.00, 29, 'Durable rubber toy for dogs', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-27 09:56:20'),
(7, 3, 'Cat Feather Toy', 'KittyFun', 300.00, 20, 'Interactive feather toy for cats', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(8, 4, 'Pet Collar', 'PawStyle', 350.00, 25, 'Adjustable collar for pets', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(9, 4, 'Pet Feeding Bowl', 'PawHome', 450.00, 19, 'Durable pet feeding bowl', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-27 14:39:07'),
(10, 4, 'Pet Carrier Bag', 'TravelPaw', 1800.00, 7, 'Comfortable carrier bag for small pets', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-28 02:11:48'),
(11, 5, 'Pet Shampoo', 'PetCare', 550.00, 15, 'Gentle cleaning shampoo for pets', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(12, 5, 'Pet Grooming Brush', 'PawBrush', 400.00, 17, 'Soft grooming brush for dogs and cats', 'default_product.jpg', 'Available', '2026-08-26 13:41:45', '2026-08-26 14:58:01'),
(13, 1, 'Bird Seed Mix 500g', 'FeatherFresh', 320.00, 18, 'Healthy mixed seed food for common pet birds.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(14, 1, 'Fish Flakes 200g', 'AquaMeal', 280.00, 25, 'Balanced daily flake food for aquarium fish.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(15, 2, 'Pet Calcium Supplement', 'VetCare', 650.00, 10, 'Calcium supplement for supporting healthy bones and teeth.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(16, 2, 'Flea and Tick Spray', 'PetMed', 520.00, 8, 'Pet care spray for controlling fleas and ticks.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(17, 2, 'Pet Ear Cleaning Solution', 'HealthyPaw', 420.00, 4, 'Gentle ear cleaning solution for dogs and cats.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(18, 3, 'Dog Rope Toy', 'PetPlay', 380.00, 16, 'Strong rope toy for chewing and interactive dog play.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(19, 3, 'Cat Toy Mouse', 'KittyFun', 220.00, 24, 'Small interactive mouse toy for cats.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(20, 3, 'Bird Swing Toy', 'FeatherFun', 350.00, 5, 'Colorful hanging swing toy for pet birds.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(21, 4, 'Adjustable Pet Leash', 'PawStyle', 600.00, 14, 'Strong adjustable leash for everyday walking.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(22, 4, 'Pet Water Bottle', 'PawHome', 750.00, 0, 'Portable drinking bottle for pets during travel.', 'default_product.jpg', 'Out of Stock', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(23, 5, 'Pet Nail Clipper', 'PawBrush', 480.00, 12, 'Safe nail clipper suitable for dogs and cats.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(24, 5, 'Pet Cleaning Wipes', 'PetCare', 350.00, 22, 'Soft cleaning wipes for quick daily pet hygiene.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12'),
(25, 5, 'Pet Toothbrush Set', 'FreshPaw', 390.00, 3, 'Toothbrush set designed for maintaining pet dental hygiene.', 'default_product.jpg', 'Available', '2026-08-28 10:34:12', '2026-08-28 10:34:12');

-- orders
INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `delivery_address`, `delivery_method`, `payment_method`, `payment_status`, `order_status`, `order_date`, `updated_at`) VALUES
(1, 2, 1650.00, 'Mirpur, Dhaka', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Confirmed', '2026-08-26 13:41:45', '2026-08-26 13:41:45'),
(2, 8, 45550.00, '243 dokhin khan dhaka', 'Pathao Fast', 'bKash', 'Pending', 'Pending', '2026-08-26 14:58:01', '2026-08-26 14:58:01'),
(3, 8, 16000.00, '243 dokhin khan dhaka', 'Jhinku BD', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-26 15:00:32', '2026-08-26 15:00:32'),
(4, 8, 32000.00, '243 dokhin khan dhaka', 'Jhinku BD', 'Credit Card', 'Pending', 'Pending', '2026-08-26 15:08:26', '2026-08-26 15:08:26'),
(5, 8, 600.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-27 09:56:15', '2026-08-27 09:56:15'),
(6, 8, 250.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-27 09:56:20', '2026-08-27 09:56:20'),
(7, 8, 450.00, '243 dokhin khan dhaka', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-27 09:56:25', '2026-08-27 09:56:25'),
(8, 8, 32000.00, 'Uttara dhaka 1230', 'Pathao Fast', 'Nagad', 'Pending', 'Pending', '2026-08-27 13:29:31', '2026-08-27 13:29:31'),
(9, 8, 450.00, 'Uttara dhaka 1230', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-27 14:39:07', '2026-08-27 14:39:07'),
(10, 8, 26800.00, 'Ashkona Uttara dhaka 1230', 'Shop Pickup', 'Rocket', 'Pending', 'Pending', '2026-08-28 02:11:48', '2026-08-28 02:11:48'),
(11, 8, 4500.00, 'Ashkona Uttara dhaka 1230', 'Pathao Fast', 'Cash on Delivery', 'Pending', 'Pending', '2026-08-28 02:20:31', '2026-08-28 02:20:31');

-- order_items
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

-- carts
INSERT INTO `carts` (`cart_id`, `customer_id`, `item_type`, `item_id`, `quantity`, `created_at`) VALUES
(1, 2, 'product', 1, 2, '2026-08-26 13:41:45'),
(2, 2, 'product', 8, 1, '2026-08-26 13:41:45');

-- deliveries
INSERT INTO `deliveries` (`delivery_id`, `order_id`, `delivery_agent_id`, `delivery_status`, `assigned_at`, `delivered_at`, `delivery_note`) VALUES
(1, 1, 6, 'Assigned', '2026-08-26 13:41:45', NULL, 'Contact customer before delivery.');

-- appointments
INSERT INTO `appointments` (`appointment_id`, `customer_id`, `doctor_id`, `pet_name`, `pet_type`, `appointment_date`, `appointment_time`, `reason`, `status`, `created_at`) VALUES
(1, 2, 1, 'Bruno', 'Dog', '2026-08-30', '10:30:00', 'Routine health examination', 'Completed', '2026-08-26 13:41:45'),
(2, 3, 2, 'Milo', 'Cat', '2026-09-02', '12:00:00', 'Low appetite and weakness', 'Confirmed', '2026-08-26 13:41:45'),
(3, 8, 3, 'Miki', 'Cat', '2026-08-25', '11:00:00', 'Vaccination and general checkup', 'Completed', '2026-08-24 15:20:00'),
(4, 2, 4, 'Rocky', 'Dog', '2026-08-27', '13:30:00', 'Diet and skin care consultation', 'Completed', '2026-08-26 09:15:00');

-- medical_records
INSERT INTO `medical_records` (`record_id`, `appointment_id`, `diagnosis`, `prescription`, `treatment_notes`, `next_visit_date`, `created_at`) VALUES
(1, 1, 'Healthy', 'No medicine required', 'Maintain proper diet, vaccination schedule and regular exercise.', '2026-09-30', '2026-08-26 13:41:45'),
(2, 3, 'Routine vaccination completed', 'Vitamin supplement if needed', 'Vaccination completed. Continue regular feeding and care.', '2026-11-25', '2026-08-25 11:30:00'),
(3, 4, 'Mild skin irritation', 'Pet-safe skin ointment', 'Keep the skin clean and monitor food sensitivity.', '2026-09-10', '2026-08-27 14:00:00');

-- reviews
INSERT INTO `reviews` (`review_id`, `customer_id`, `item_type`, `item_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 2, 'product', 1, 5, 'Good quality food and fast service.', 'Visible', '2026-08-26 13:41:45'),
(2, 3, 'pet', 3, 5, 'The Persian cat was healthy and well cared for.', 'Visible', '2026-08-26 13:41:45');

INSERT INTO `reviews` (`customer_id`, `item_type`, `item_id`, `rating`, `comment`, `status`) VALUES
(2, 'pet', 1, 4, 'Very active and healthy pet. Really satisfied with the service.', 'Visible'),
(3, 'pet', 2, 5, 'Beautiful pet and very well maintained by PawCare.', 'Visible'),
(2, 'pet', 3, 5, 'Amazing Persian cat. Healthy, fluffy and friendly.', 'Visible'),
(3, 'pet', 4, 4, 'Good health condition and exactly as described.', 'Visible'),
(2, 'pet', 5, 5, 'One of the best pets I have purchased from PawCare.', 'Visible'),
(3, 'pet', 6, 4, 'Cute and playful pet. Very happy with the purchase.', 'Visible'),
(2, 'pet', 7, 3, 'Good pet but delivery took a little longer than expected.', 'Visible'),
(3, 'product', 1, 5, 'Excellent quality food. My pet really likes it.', 'Visible'),
(2, 'product', 2, 4, 'Good quality product and reasonable price.', 'Visible'),
(3, 'product', 3, 5, 'Very useful product and fast delivery service.', 'Visible'),
(2, 'product', 4, 4, 'Good product. Packaging was also very nice.', 'Visible'),
(3, 'product', 5, 5, 'Excellent product quality. Highly recommended.', 'Visible'),
(2, 'product', 6, 3, 'Product is good but packaging could be better.', 'Visible'),
(3, 'product', 7, 4, 'Satisfied with the product and PawCare service.', 'Visible'),
(2, 'product', 8, 5, 'Very useful for my pet. I will definitely buy again.', 'Visible');

-- doctor reviews
INSERT INTO `reviews` (`customer_id`, `item_type`, `item_id`, `rating`, `comment`, `status`) VALUES
(2, 'doctor', 1, 5, 'Very caring doctor and explained the treatment clearly.', 'Visible'),
(3, 'doctor', 2, 5, 'Good consultation and friendly behavior with my pet.', 'Visible'),
(8, 'doctor', 3, 4, 'Helpful vaccination advice and good service.', 'Visible'),
(2, 'doctor', 4, 5, 'Very helpful guidance about pet diet and skin care.', 'Visible');

-- Make sample orders useful for admin dashboard and sales analytics
UPDATE `orders` SET `payment_status` = 'Paid', `order_status` = 'Delivered' WHERE `order_id` IN (1, 2, 3, 4, 5, 6);
UPDATE `orders` SET `payment_status` = 'Paid', `order_status` = 'Processing' WHERE `order_id` IN (7, 8);
UPDATE `orders` SET `payment_status` = 'Pending', `order_status` = 'Pending' WHERE `order_id` IN (9, 10);
UPDATE `orders` SET `payment_status` = 'Pending', `order_status` = 'Cancelled' WHERE `order_id` = 11;

-- Keep delivery records consistent where possible
UPDATE `deliveries` SET `delivery_status` = 'Delivered' WHERE `order_id` IN (1, 2, 3, 4, 5, 6);


UPDATE users SET profile_image = 'ashik.jpg' WHERE username = 'ashik';
UPDATE users SET profile_image = 'mustafiz.jpg' WHERE username = 'mustafiz';
UPDATE users SET profile_image = 'roni.jpg' WHERE username = 'roni';
UPDATE users SET profile_image = 'Shajia.jpg' WHERE username = 'shajia';