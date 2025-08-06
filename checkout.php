<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$cartItems = getCartItems($_SESSION['user_id']);
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.12;
$total = $subtotal + $tax;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = sanitizeInput($_POST['shipping_address']);
    $paymentMethod = sanitizeInput($_POST['payment_method']);
    
    if (empty($shippingAddress)) {
        $error = 'Please provide shipping address';
    } else {
        $result = createOrder($_SESSION['user_id'], $total, $shippingAddress, $paymentMethod);
        if ($result['success']) {
            $success = 'Order placed successfully! Order ID: ' . $result['order_id'];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SJ Computer | E-commerce</title>
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
                <li><a href="cart.php">Cart (<?php echo getCartItemCount($_SESSION['user_id']); ?>)</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</div>

<div class="smallContainer">
    <h1>Checkout</h1>
    
    <?php if ($error): ?>
        <div class="error-message" style="color: red; text-align: center; margin: 10px 0;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message" style="color: green; text-align: center; margin: 10px 0;">
            <?php echo $success; ?>
            <br><a href="orders.php" style="color: blue;">View My Orders</a>
        </div>
    <?php else: ?>
        <div class="checkout-container" style="display: flex; gap: 30px;">
            <!-- Order Summary -->
            <div class="order-summary" style="flex: 1;">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="order-item" style="display: flex; justify-content: space-between; margin: 10px 0; padding: 10px; border: 1px solid #ddd;">
                            <div>
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div>
                                <p><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total" style="margin-top: 20px; padding: 20px; background: #f9f9f9;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Tax (12%):</span>
                        <span><?php echo formatPrice($tax); ?></span>
                    </div>
                    <hr>
                    <div style="display: flex; justify-content: space-between; font-weight: bold;">
                        <span>Total:</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Checkout Form -->
            <div class="checkout-form" style="flex: 1;">
                <h2>Shipping Information</h2>
                <form method="POST" action="">
                    <div style="margin-bottom: 20px;">
                        <label for="shipping_address">Shipping Address *</label>
                        <textarea name="shipping_address" id="shipping_address" rows="4" required 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?></textarea>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="payment_method">Payment Method *</label>
                        <select name="payment_method" id="payment_method" required 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select payment method</option>
                            <option value="cash_on_delivery" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cash_on_delivery') ? 'selected' : ''; ?>>Cash on Delivery</option>
                            <option value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="gcash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'gcash') ? 'selected' : ''; ?>>GCash</option>
                        </select>
                    </div>
                    
                    <button type="submit" style="width: 100%; padding: 15px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
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