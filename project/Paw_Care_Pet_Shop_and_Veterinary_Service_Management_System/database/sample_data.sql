USE pawcare_db;

-- =========================================
-- SAMPLE USERS
-- Passwords are hashed using PHP password_hash()
-- =========================================

INSERT INTO users
(full_name, username, email, phone, gender, password, role, address)
VALUES

(
'PawCare Administrator',
'pawcare_admin',
'admin@pawcare.com',
'01700000001',
'Male',
'$2y$12$9RFW/EVeYno/HhIf12SbT.vS8psz1PSBQ0uSn5erMLIAxwbTu0fSa',
'admin',
'Dhaka, Bangladesh'
),

(
'Rahim Ahmed',
'rahim_ahmed',
'customer1@pawcare.com',
'01700000002',
'Male',
'$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW',
'customer',
'Mirpur, Dhaka'
),

(
'Sadia Islam',
'sadia_islam',
'customer2@pawcare.com',
'01700000003',
'Female',
'$2y$12$lge9fOoK6rs6fHNX3.2vPOQPtWiELXk5VlBFTvwumScFYc.DDvevW',
'customer',
'Uttara, Dhaka'
),

(
'Dr. Hasan Rahman',
'dr_hasan',
'doctor1@pawcare.com',
'01700000004',
'Male',
'$2y$12$oCi9cf6OD5VZQ3PSLTazseYX.VnAhKTWlTpVeqYzjNa/AzxmhRw0u',
'doctor',
'Dhanmondi, Dhaka'
),

(
'Dr. Nusrat Jahan',
'dr_nusrat',
'doctor2@pawcare.com',
'01700000005',
'Female',
'$2y$12$oCi9cf6OD5VZQ3PSLTazseYX.VnAhKTWlTpVeqYzjNa/AzxmhRw0u',
'doctor',
'Banani, Dhaka'
),

(
'Karim Uddin',
'karim_delivery',
'delivery1@pawcare.com',
'01700000006',
'Male',
'$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa',
'delivery',
'Mohammadpur, Dhaka'
),

(
'Rafi Ahmed',
'rafi_delivery',
'delivery2@pawcare.com',
'01700000007',
'Male',
'$2y$12$LrXYTJWyMWjG4L.jwHWlOOO3CrWD2Vp/0x5zjSvAy.xjQ/9HKsgaa',
'delivery',
'Badda, Dhaka'
);


-- =========================================
-- DOCTOR DETAILS
-- doctor users = user_id 4 and 5
-- =========================================

INSERT INTO doctors
(
    user_id,
    specialization,
    qualification,
    experience_years,
    consultation_fee,
    available_days,
    available_time,
    bio
)
VALUES

(
    4,
    'Small Animal Medicine',
    'DVM',
    5,
    800.00,
    'Sunday, Tuesday, Thursday',
    '10:00 AM - 4:00 PM',
    'Experienced veterinarian specializing in dogs, cats and small animals.'
),

(
    5,
    'Pet Surgery and General Care',
    'DVM, MS in Veterinary Surgery',
    7,
    1000.00,
    'Monday, Wednesday, Saturday',
    '11:00 AM - 5:00 PM',
    'Veterinary doctor experienced in general treatment and minor pet surgery.'
);

-- =========================================
-- PET CATEGORIES
-- =========================================

INSERT INTO pet_categories
(category_name, description)
VALUES

('Dog', 'Different breeds of domestic dogs'),
('Cat', 'Different breeds of domestic cats'),
('Bird', 'Pet birds'),
('Fish', 'Aquarium and decorative fish'),
('Rabbit', 'Domestic rabbits');


-- =========================================
-- PETS
-- =========================================

INSERT INTO pets
(
category_id,
pet_name,
breed,
age,
gender,
color,
weight,
price,
stock,
health_status,
vaccination_status,
description
)
VALUES

(
1,
'Golden Retriever Puppy',
'Golden Retriever',
'3 Months',
'Male',
'Golden',
'4.5 kg',
45000.00,
4,
'Healthy',
'Vaccinated',
'Friendly and playful Golden Retriever puppy.'
),

(
1,
'German Shepherd Puppy',
'German Shepherd',
'4 Months',
'Mixed',
'Black and Tan',
'6 kg',
50000.00,
3,
'Healthy',
'Vaccinated',
'Active and intelligent German Shepherd puppies.'
),

(
2,
'Persian Cat',
'Persian',
'8 Months',
'Female',
'White',
'3 kg',
25000.00,
5,
'Healthy',
'Vaccinated',
'Calm and friendly long-haired Persian cat.'
),

(
2,
'British Shorthair',
'British Shorthair',
'6 Months',
'Male',
'Grey',
'3.2 kg',
32000.00,
3,
'Healthy',
'Vaccinated',
'Friendly British Shorthair cat.'
),

(
3,
'Cockatiel',
'Cockatiel',
'5 Months',
'Mixed',
'Grey and Yellow',
'120 gm',
4500.00,
8,
'Healthy',
'Vaccinated',
'Friendly and social pet bird.'
),

(
3,
'Budgerigar',
'Budgie',
'4 Months',
'Mixed',
'Green',
'40 gm',
1800.00,
10,
'Healthy',
'Vaccinated',
'Colorful and active small pet bird.'
),

(
4,
'Goldfish',
'Goldfish',
'3 Months',
'Mixed',
'Orange',
'50 gm',
600.00,
15,
'Healthy',
'Not Vaccinated',
'Beautiful aquarium goldfish.'
),

(
5,
'Mini Lop Rabbit',
'Mini Lop',
'5 Months',
'Female',
'White and Brown',
'1.5 kg',
8000.00,
4,
'Healthy',
'Vaccinated',
'Friendly domestic rabbit.'
);


-- =========================================
-- PRODUCT CATEGORIES
-- =========================================

INSERT INTO product_categories
(category_name, description)
VALUES

('Pet Food', 'Food products for pets'),
('Medicine', 'Pet healthcare and medicine products'),
('Toys', 'Toys for pets'),
('Accessories', 'Pet accessories and daily use products'),
('Grooming', 'Pet grooming and cleaning products');


-- =========================================
-- PRODUCTS
-- =========================================

INSERT INTO products
(
category_id,
product_name,
brand,
price,
stock,
description
)
VALUES

(1, 'Premium Dog Food 1kg', 'Pedigree', 650.00, 20, 'Nutritious dry food for adult dogs'),

(1, 'Cat Food 500g', 'Whiskas', 450.00, 25, 'Dry food for adult cats'),

(1, 'Rabbit Food 1kg', 'BunnyCare', 550.00, 12, 'Balanced food for domestic rabbits'),

(2, 'Pet Vitamin Supplement', 'VetCare', 700.00, 15, 'Vitamin supplement for pets'),

(2, 'Pet Antiseptic Spray', 'PetMed', 380.00, 18, 'Antiseptic spray for minor pet wounds'),

(3, 'Rubber Ball', 'PetPlay', 250.00, 30, 'Durable rubber toy for dogs'),

(3, 'Cat Feather Toy', 'KittyFun', 300.00, 20, 'Interactive feather toy for cats'),

(4, 'Pet Collar', 'PawStyle', 350.00, 25, 'Adjustable collar for pets'),

(4, 'Pet Feeding Bowl', 'PawHome', 450.00, 20, 'Durable pet feeding bowl'),

(4, 'Pet Carrier Bag', 'TravelPaw', 1800.00, 8, 'Comfortable carrier bag for small pets'),

(5, 'Pet Shampoo', 'PetCare', 550.00, 15, 'Gentle cleaning shampoo for pets'),

(5, 'Pet Grooming Brush', 'PawBrush', 400.00, 18, 'Soft grooming brush for dogs and cats');


-- =========================================
-- SAMPLE CART
-- Customer user_id = 2
-- =========================================

INSERT INTO carts
(customer_id, item_type, item_id, quantity)
VALUES

(2, 'product', 1, 2),
(2, 'product', 8, 1);


-- =========================================
-- SAMPLE ORDER
-- Customer user_id = 2
-- =========================================

INSERT INTO orders
(
customer_id,
total_amount,
delivery_address,
payment_method,
payment_status,
order_status
)
VALUES
(
2,
1650.00,
'Mirpur, Dhaka',
'Cash on Delivery',
'Pending',
'Confirmed'
);


-- =========================================
-- SAMPLE ORDER ITEMS
-- =========================================

INSERT INTO order_items
(
order_id,
item_type,
item_id,
item_name,
price,
quantity,
subtotal
)
VALUES

(
1,
'product',
1,
'Premium Dog Food 1kg',
650.00,
2,
1300.00
),

(
1,
'product',
8,
'Pet Collar',
350.00,
1,
350.00
);


-- =========================================
-- SAMPLE APPOINTMENTS
-- =========================================

INSERT INTO appointments
(
customer_id,
doctor_id,
pet_name,
pet_type,
appointment_date,
appointment_time,
reason,
status
)
VALUES

(
2,
1,
'Bruno',
'Dog',
'2026-08-30',
'10:30:00',
'Routine health examination',
'Completed'
),

(
3,
2,
'Milo',
'Cat',
'2026-09-02',
'12:00:00',
'Low appetite and weakness',
'Confirmed'
);


-- =========================================
-- SAMPLE MEDICAL RECORD
-- =========================================

INSERT INTO medical_records
(
appointment_id,
diagnosis,
prescription,
treatment_notes,
next_visit_date
)
VALUES
(
1,
'Healthy',
'No medicine required',
'Maintain proper diet, vaccination schedule and regular exercise.',
'2026-09-30'
);


-- =========================================
-- SAMPLE DELIVERY
-- Delivery agent user_id = 6
-- =========================================

INSERT INTO deliveries
(
order_id,
delivery_agent_id,
delivery_status,
delivery_note
)
VALUES
(
1,
6,
'Assigned',
'Contact customer before delivery.'
);


-- =========================================
-- SAMPLE REVIEWS
-- =========================================

INSERT INTO reviews
(
customer_id,
item_type,
item_id,
rating,
comment
)
VALUES

(
2,
'product',
1,
5,
'Good quality food and fast service.'
),

(
3,
'pet',
3,
5,
'The Persian cat was healthy and well cared for.'
);