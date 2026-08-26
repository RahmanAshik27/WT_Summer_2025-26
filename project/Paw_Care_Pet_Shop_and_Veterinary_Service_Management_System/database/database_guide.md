# PawCare Database Setup Guide

## Project
PawCare – Pet Shop and Veterinary Service Management System

## Requirements
- XAMPP
- Apache
- MySQL
- phpMyAdmin

## Database Name
pawcare_db

## Option 1: Recommended Full Database Import

This is the easiest method.

1. Open XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.
4. Open a browser.
5. Go to:
   http://localhost/phpmyadmin
6. Click the Import tab.
7. Select:
   database/pawcare_db.sql
8. Click Go.
9. The complete database structure and sample data will be imported automatically.

## Option 2: Manual Setup

If you want to create the database structure and sample data separately:

### Step 1
Import:

database/schema.sql

This will create:
- pawcare_db database
- all required tables
- relationships
- foreign keys
- constraints

### Step 2
Import:

database/sample_data.sql

This will insert the sample data used for project testing.

## Database Files

### schema.sql
Contains only the database structure.

### sample_data.sql
Contains the sample/demo data.

### pawcare_db.sql
Contains the complete database structure and sample data.

## Main Tables

1. users
2. doctors
3. pet_categories
4. pets
5. product_categories
6. products
7. carts
8. orders
9. order_items
10. appointments
11. medical_records
12. deliveries
13. reviews

## Notes

- Do not manually create the tables before importing `pawcare_db.sql`.
- If the database already exists and you want a clean installation, remove the old database first or use `schema.sql`.
- Sample accounts are provided only for testing and demonstration.