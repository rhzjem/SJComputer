<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - SJ Computer</title>
    <script src="https://kit.fontawesome.com/cca3f4e97d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="navbar">
        <div class="logo">
            <img src="images/Logo.png" alt="Logo">
            <span class="business-name">SJ Computer</span>
        </div>
        <nav>
            <ul id="MenuItems">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart <?php if (isLoggedIn()): ?>(<?php echo getCartItemCount($_SESSION['user_id']); ?>)<?php endif; ?></a></li>
                <li><a href="services.php">Services</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signin.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<div class="smallContainer" style="text-align: center; padding: 50px 0;">
    <h1>Booking Confirmation</h1>
    <i class="fa-solid fa-circle-check" style="font-size: 100px; color: green; margin: 20px 0;"></i>
    <h2>Thank you for your booking!</h2>
    <p>We have received your service request and will contact you shortly to confirm the details.</p>
    <a href="services.php" class="btn">Back to Services</a>
</div>

<div class="footer">
    <div class="container">
            <div class="row">
                <div class="customerSupport">
                    <h3>Customer Support</h3>
                    <ul>
                        <li>FAQs</li>
                        <li>Return Policy</li>
                        <li>Warranty</li>
                    </ul>
                </div>
                <div class="sjlogo">
                    <img src="images/Logo.png" alt="">
                    <h3>SJ Computer</h3>
                    <h4>Location</h4>
                    <p>Manlapaz Bldg. F. Pimentel Ave. Daet, Camarines Norte</p>
                </div>
                <div class="socMed">
                    <h3>Follow Us</h3>
                    <ul>
                        <li>Facebook</li>
                        <li>Instagram</li>
                    </ul>
                    <h4>Contact Us</h4>
                    <p>0987654321</p>
                </div>
            </div>

            <div class="copyright">
                <hr>
                <p>&copy; 2025 SJ Computer. All rights reserved.</p>
            </div>
        </div>
</div>

</body>
</html>