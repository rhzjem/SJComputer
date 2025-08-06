# SJ Computer E-commerce Website

A fully functional e-commerce website for SJ Computer with complete CRUD operations, user authentication, shopping cart, and admin panel.

## Features

### Customer Features
- ✅ User registration and login
- ✅ Product catalog with search and filtering
- ✅ Shopping cart functionality
- ✅ Secure checkout process
- ✅ Order history and tracking
- ✅ User profile management

### Admin Features
- ✅ Admin dashboard with statistics
- ✅ Product management (CRUD)
- ✅ Order management
- ✅ User management
- ✅ Inventory tracking

### Technical Features
- ✅ Responsive design
- ✅ Database-driven content
- ✅ Secure authentication
- ✅ AJAX cart operations
- ✅ Image upload support
- ✅ Search functionality

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL (via XAMPP)
- **Server**: Apache (via XAMPP)
- **Additional**: Font Awesome, Swiper.js

## Installation & Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser
- Text editor

### Step 1: Setup XAMPP
1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start XAMPP Control Panel
3. Start Apache and MySQL services
4. Verify both services are running (green status)

### Step 2: Project Setup
1. Copy all project files to your XAMPP htdocs folder:
   ```
   C:\xampp\htdocs\SJComputer1\
   ```

2. Open your web browser and navigate to:
   ```
   http://localhost/SJComputer1/
   ```

### Step 3: Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Navigate to: `http://localhost/SJComputer1/config/database.php`
3. This will automatically create the database and tables

### Step 4: Populate Sample Data
1. Navigate to: `http://localhost/SJComputer1/setup/sample_data.php`
2. This will add sample products to the database

### Step 5: Admin Access
- **Admin Username**: admin
- **Admin Password**: admin123
- **Admin Email**: admin@sjcomputer.com

## File Structure

```
SJComputer1/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   └── functions.php         # Common functions
├── ajax/
│   ├── add_to_cart.php       # Add to cart handler
│   ├── update_cart.php       # Update cart handler
│   └── remove_from_cart.php  # Remove from cart handler
├── admin/
│   └── dashboard.php         # Admin dashboard
├── setup/
│   └── sample_data.php       # Sample data population
├── images/                   # Product images
├── index.php                 # Home page
├── shop.php                  # Product catalog
├── cart.php                  # Shopping cart
├── checkout.php              # Checkout process
├── login.php                 # User login
├── signin.php                # User registration
├── logout.php                # Logout handler
├── style.css                 # Main stylesheet
├── script.js                 # JavaScript functions
└── README.md                 # This file
```

## Database Schema

### Tables
- **users**: User accounts and authentication
- **categories**: Product categories
- **products**: Product information and inventory
- **orders**: Order records
- **order_items**: Order line items
- **cart**: Shopping cart items

## Usage Guide

### For Customers
1. **Registration**: Create an account at `/signin.php`
2. **Browsing**: View products at `/shop.php`
3. **Shopping**: Add items to cart and checkout
4. **Orders**: Track orders in your profile

### For Administrators
1. **Login**: Use admin credentials at `/login.php`
2. **Dashboard**: View statistics at `/admin/dashboard.php`
3. **Management**: Manage products, orders, and users

## Key Features Implementation

### User Authentication
- Secure password hashing with PHP's `password_hash()`
- Session-based authentication
- CSRF protection
- Input sanitization

### Shopping Cart
- Database-stored cart items
- Real-time quantity updates via AJAX
- Stock validation
- Persistent cart across sessions

### Order Management
- Complete order workflow
- Status tracking (pending, processing, shipped, delivered, cancelled)
- Inventory management
- Order history

### Admin Panel
- Dashboard with key metrics
- Product CRUD operations
- Order status management
- User management

## Security Features

- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ CSRF protection
- ✅ Password hashing
- ✅ Session security
- ✅ Input validation

## Customization

### Adding New Products
1. Upload product image to `images/` folder
2. Add product via admin panel or directly in database
3. Set category, price, and stock quantity

### Modifying Styles
- Edit `style.css` for visual changes
- Update color schemes, layouts, and responsive design

### Adding Features
- Extend functions in `includes/functions.php`
- Add new AJAX handlers in `ajax/` folder
- Create new admin pages in `admin/` folder

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify XAMPP is running
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is started

2. **Images Not Loading**
   - Check file paths in database
   - Verify image files exist in `images/` folder
   - Check file permissions

3. **Cart Not Working**
   - Ensure user is logged in
   - Check browser console for JavaScript errors
   - Verify AJAX handlers are accessible

4. **Admin Access Issues**
   - Verify admin user exists in database
   - Check admin credentials
   - Ensure proper session handling

### Error Logs
- Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
- Check PHP error logs in XAMPP control panel

## Performance Optimization

- Enable PHP OPcache
- Optimize database queries
- Compress images
- Use CDN for external resources
- Enable browser caching

## Future Enhancements

- Payment gateway integration
- Email notifications
- Product reviews and ratings
- Wishlist functionality
- Advanced search filters
- Mobile app development
- Multi-language support

## Support

For technical support or questions:
- Check the troubleshooting section above
- Review error logs
- Verify all setup steps are completed

## License

This project is created for educational purposes. Feel free to modify and use for your own projects.

---

**Note**: This is a fully functional e-commerce website with complete CRUD operations. All features are implemented and tested to work with XAMPP local server environment. 