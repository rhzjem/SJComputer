<?php
require_once 'includes/functions.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product = getProductById($productId);

// Fetch related products (same category)
$relatedProducts = [];
if ($product) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.category_id = ? AND p.id != ? LIMIT 4");
    $stmt->execute([$product['category_id'], $productId]);
    $relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($quantity > 0 && $product) {
        addToCart($_SESSION['user_id'], $productId, $quantity);
        header('Location: cart.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) . ' - SJ Computer' : 'Product Not Found - SJ Computer'; ?></title>
    <script src="https://kit.fontawesome.com/cca3f4e97d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">     
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

<div class="smallContainer productDetails">
    <?php if ($product): ?>
    <div class="row">
        <div class="col2">
            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col2">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <h4><?php echo formatPrice($product['price']); ?></h4>
            
            <form method="post">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
            
            <h3>Product Details</h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> available</p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col2">
            <h1>Product Not Found</h1>
            <p>The requested product could not be found.</p>
            <a href="shop.php" class="btn">Continue Shopping</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($relatedProducts)): ?>
<div class="smallContainer">
    <div class="row2">
        <h2>Related Products</h2>
        <a href="shop.php?category=<?php echo $product['category_id']; ?>">View More</a>
    </div>
</div>

<div class="smallContainer">    
    <div class="row">
        <?php foreach ($relatedProducts as $related): ?>
        <div class="cards">
            <a href="productDetails.php?id=<?php echo $related['id']; ?>">
                <img src="<?php echo htmlspecialchars($related['image_path']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                <div class="rating">
                    <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                    <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                    <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                    <i class="fa-solid fa-star" style="color: #FFD43B"></i>
                    <i class="fa-regular fa-star" style="color: #FFD43B"></i>
                </div>
                <p><?php echo formatPrice($related['price']); ?></p>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

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

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>
</html>