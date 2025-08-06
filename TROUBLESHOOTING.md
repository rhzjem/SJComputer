# Troubleshooting Guide

## Common Issues and Solutions

### 1. Database Connection Error
**Error:** `Connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`
**Solution:** 
- Make sure XAMPP is running (Apache and MySQL)
- Check if MySQL is started in XAMPP Control Panel
- Verify the database credentials in `config/database.php`

### 2. File Path Issues
**Error:** `Failed to open stream: No such file or directory`
**Solution:**
- Make sure all files are in the correct directory structure
- Check that the `config/`, `includes/`, `ajax/`, `admin/`, and `setup/` folders exist
- Verify file permissions

### 3. Sample Data Not Loading
**Error:** No products appear on the website
**Solution:**
- Run the setup script: `http://localhost/SJComputer1/setup/init.php`
- Check if the database tables were created properly
- Verify that categories and products exist in the database

### 4. Admin Panel Access Denied
**Error:** Cannot access admin dashboard
**Solution:**
- Login with admin credentials: username `admin`, password `admin123`
- Make sure you're logged in as an admin user
- Check if the `is_admin` field is set to `true` in the database

### 5. Cart Not Working
**Error:** Cannot add items to cart
**Solution:**
- Make sure you're logged in as a regular user
- Check if the AJAX files are accessible
- Verify that the database connection is working

### 6. Images Not Displaying
**Error:** Product images don't show up
**Solution:**
- Check if the image files exist in the `images/` folder
- Verify the image paths in the database
- Make sure the image file names match exactly (case-sensitive)

## Setup Checklist

- [ ] XAMPP is installed and running
- [ ] Apache and MySQL services are started
- [ ] Project files are in `C:\xampp\htdocs\SJComputer1\`
- [ ] Database is initialized via `setup/init.php`
- [ ] Sample data is loaded
- [ ] Admin user is created
- [ ] All pages are accessible

## Testing Your Setup

1. **Homepage:** `http://localhost/SJComputer1/`
2. **Shop:** `http://localhost/SJComputer1/shop.php`
3. **Admin Panel:** `http://localhost/SJComputer1/admin/dashboard.php`
4. **User Login:** `http://localhost/SJComputer1/login.php`
5. **User Registration:** `http://localhost/SJComputer1/signin.php`

## Database Verification

You can check if your database is set up correctly by:
1. Opening phpMyAdmin: `http://localhost/phpmyadmin`
2. Looking for the `sjcomputer_db` database
3. Checking that all tables exist: `users`, `categories`, `products`, `orders`, `order_items`, `cart`
4. Verifying that sample data is present

## Getting Help

If you're still experiencing issues:
1. Check the XAMPP error logs
2. Verify all file paths and permissions
3. Make sure all required PHP extensions are enabled
4. Test with a simple PHP file to verify the environment 