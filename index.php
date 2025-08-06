<?php
require_once 'includes/functions.php';

// Get featured products
$featuredProducts = getFeaturedProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJ Computer | E-commerce</title>
    <script src="https://kit.fontawesome.com/cca3f4e97d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">     
</head>
<body>
    
    <div class="header">
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
                        <li><a href="services.html">Services</a></li>
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

            <div class="top">
                <div class="motto">
                    <h1>Power Up Your PC</h1>
                    <h3>Build, Repair, and Shop With Confidence</h3>
                    <p>From top-quality parts to expert repairs, we've got everything<br>
                        you need to keep your PC running at its best.</p>
                    <a href="shop.php" class="btnShop">Shop Now</a>
                </div>

                <div class="pic">
                    <img src="images/image1.jpg">
                </div>
            </div>    
        </div>
    </div>

    <div class="featuredsection">
        <h2 class="featured">Featured Products</h2>
        <div class="containerFeature swiper">
            <div class="card-wrapper">
                <ul class="cardList swiper-wrapper">
                    <?php foreach ($featuredProducts as $product): ?>
                        <li class="cardItems swiper-slide">
                            <a href="productDetails.php?id=<?php echo $product['id']; ?>" class="cardLink">
                                <img src="<?php echo $product['image_path'] ?: 'images/default-product.png'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="cardImage">
                                <p class="<?php echo strtolower($product['category_name']); ?>Badge"><?php echo $product['category_name']; ?></p>
                                <h2 class="cardTitle"><?php echo htmlspecialchars($product['name']); ?></h2>
                                <h2 class="price"><?php echo formatPrice($product['price']); ?></h2>
                                <button class="cardButton">View on Product</button>
                            </a>    
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="swiper-pagination"></div>
                <div class="swiper-slide-button swiper-button-prev"></div>
                <div class="swiper-slide-button swiper-button-next"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script.js"></script>

    <div class="limitedOffer">
        <div class="container">
        <div class="row">
            <div class="left">
                <h2>Limited-Time Offer</h2>
                <h1>Samsung 22-Inch Full HD IPS Monitor</h1>
                <p>Upgrade your setup with ultra-smooth 
                    visuals, 22-Inch Full HD IPS Monitor<br>high refresh rates 
                    for ultra-smooth experience!</p>
                <div class="price">
                    <p>Now only <b>Php 5,999.00</b></p>
                </div>
                <a href="shop.php" class="buybtn">Buy Now</a><br>
                <div class="valid">
                    <small>Valid until April 30,2025 </small>
                </div>
            </div>
            <div class="right">
                <img src="images/limiterTimeOfferPic.png" alt="asusImage">
            </div>
        </div>
        </div>
    </div>

    <div class="whyChoose">
        <div class="title">Why Choose Us?</div>
        <div class="container">
            <div class="con1">
                <img src="images/quality.png" alt="image1">
                <h2>High-Quality Products</h2>
            </div>
            <div class="con2">
                <img src="images/customer-support.png" alt="image2">
                <h2>Customer Support</h2>
            </div>
            <div class="con3">
                <img src="images/affordable.png" alt="image3">
                <h2>Affordable Prices</h2>
            </div>
        </div>
    </div>

    <div class="feedback">
        <h1>What Our Customers Say About Us</h1>
        <div class="container">
            <div class="con1">
                <div class="user">
                    <img src="images/user1.png" alt="">
                    <h3>Schenly Jamito</h3>
                    <div class="rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div> 
                    <i class="fa-solid fa-quote-left"></i> 
                </div>  
                <div class="statement">
                    <p>Lorem ipsum is the best dummy text. eththh egeheh rregerg
                    </p>
                </div>
            </div>
            <div class="con1">
                <div class="user">
                    <img src="images/user2.png" alt="">
                    <h3>Emwin Encinas</h3>
                    <div class="rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div> 
                    <i class="fa-solid fa-quote-left"></i> 
                </div>    
                <div class="statement">
                    <p>Lorem ipsum is the best dummy text. HAHHAHAHA HAHAHA HAHAH</p>
                </div>
            </div>
            <div class="con1">
                <div class="user">
                    <img src="images/user3.png" alt="">
                    <h3>Clarissa Balagtas</h3>
                    <div class="rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div> 
                    <i class="fa-solid fa-quote-left"></i> 
                </div>    
                <div class="statement">
                    <p>Lorem ipsum is the best dummy text. eththh egeheh rregerg.</p>
                </div>
            </div>
            <div class="con1">
                <div class="user">
                    <img src="images/user4.png" alt="">
                    <h3>Emwin Encinas</h3>
                    <div class="rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div> 
                    <i class="fa-solid fa-quote-left"></i> 
                </div>    
                <div class="statement">
                    <p>Lorem ipsum is the best dummy text. Mamamo Mamamo Green</p>
                </div>
            </div>
        </div>
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