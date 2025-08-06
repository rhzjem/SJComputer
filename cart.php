<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$cartItems = getCartItems($_SESSION['user_id']);
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.12;
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - SJ Computer | E-commerce</title>
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

<div class="smallContainer cartPage">
    <?php if (empty($cartItems)): ?>
        <div id="emptyCartMessage" style="text-align: center; padding: 40px;">
            <h2>Your cart is empty 🛒</h2>
            <a href="shop.php" class="goShopBtn">Go to Shopping</a>
        </div>
    <?php else: ?>
        <table class="tableCart">
            <thead>
                <tr>
                    <th>Products</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div class="cartInfo">
                                <img src="<?php echo $item['image_path'] ?: 'images/default-product.png'; ?>" alt="">
                                <div>
                                    <p><?php echo htmlspecialchars($item['name']); ?></p>
                                    <small>Price: <?php echo formatPrice($item['price']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="number" value="<?php echo $item['quantity']; ?>" 
                                   data-cart-id="<?php echo $item['id']; ?>" min="1" 
                                   onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                        </td>
                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <a href="#" class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totalPrice">
            <table>
                <tr><td>Subtotal</td><td><?php echo formatPrice($subtotal); ?></td></tr>
                <tr><td>Tax (12%)</td><td><?php echo formatPrice($tax); ?></td></tr>
                <tr><td>Total</td><td><?php echo formatPrice($total); ?></td></tr>
            </table>
        </div>

        <div class="checkout">
            <a href="checkout.php" class="checkoutBtn">Check Out</a>
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

<script>
function updateQuantity(cartId, quantity) {
    fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cart_id=' + cartId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating cart');
    });
}

function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'cart_id=' + cartId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item from cart');
        });
    }
}
</script>

</body>
</html> 