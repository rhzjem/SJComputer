<?php
require_once 'includes/functions.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get products based on filters
$products = getAllProducts($category, $search);
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - SJ Computer | E-commerce</title>
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

<div class="smallContainer">    
    <!-- Search Bar -->
    <div class="search-container" style="margin: 20px 0; text-align: center;">
        <form method="GET" action="" style="display: flex; justify-content: center; gap: 10px; max-width: 600px; margin: 0 auto;">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
        </form>
    </div>

    <div class="tabs">
        <a href="?category=all<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="filter-tab <?php echo $category === 'all' ? 'active-tab' : ''; ?>" data-filter="all">All Products</a>
        <?php foreach ($categories as $cat): ?>
            <a href="?category=<?php echo urlencode($cat['name']); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="filter-tab <?php echo $category === $cat['name'] ? 'active-tab' : ''; ?>" data-filter="<?php echo strtolower($cat['name']); ?>"><?php echo $cat['name']; ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 50px;">
            <h2>No products found</h2>
            <p>Try adjusting your search or filter criteria.</p>
            <a href="shop.php" style="color: #007bff;">View all products</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?php echo strtolower($product['category_name']); ?>">
                    <div class="cards">
                        <a href="productDetails.php?id=<?php echo $product['id']; ?>" class="card-link">
                            <img src="<?php echo $product['image_path'] ?: 'images/default-product.png'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <p class="<?php echo strtolower($product['category_name']); ?>Badge"><?php echo $product['category_name']; ?></p>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="rating">
                                <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                                <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                                <i class="fa-regular fa-star" style="color: #FFD43B"></i>
                            </div>
                            <p><?php echo formatPrice($product['price']); ?></p>
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['id']; ?>, event)">Add to Cart</button>
                        <?php else: ?>
                            <a href="login.php" class="login-to-buy-btn">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
function addToCart(productId, event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart!');
            // Update cart count without reloading the page
            updateCartCount();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product to cart');
    });
}

function updateCartCount() {
    fetch('ajax/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCountElement = document.querySelector('#MenuItems li:nth-child(3) a');
            if (cartCountElement) {
                cartCountElement.innerHTML = 'Cart (' + data.count + ')';
            }
        }
    });
}
</script>

</body>
</html> 