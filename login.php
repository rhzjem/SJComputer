<?php
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';

// Check if we're coming from services.php and set redirect URL
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'services.php') !== false) {
    $_SESSION['redirect_url'] = 'services.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = loginUser($username, $password);
        if ($result['success']) {
            // Check if there's a redirect URL in session
            $redirectUrl = $_SESSION['redirect_url'] ?? 'index.php';
            unset($_SESSION['redirect_url']); // Clear it after use
            
            if (isAdmin()) {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: ' . $redirectUrl);
            }
            exit();
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
    <title>Login - SJ Computer | E-commerce</title>
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
                <li><a href="cart.php">Cart</a></li>
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

<div class="accountPage">
    <div class="form">
        <div class="formBtn">
            <span>Login</span>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="inputForm">
            <form method="POST" action="">
                <h3>Email/Username</h3>
                <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <h3>Password</h3>
                <input type="password" name="password" required>
                
                <div class="buttonLogin">
                    <button type="submit" class="loginBtn">Login</button>
                </div>
                <div class="forgotPass">
                    <a href="#">Forgot Password</a>
                </div>
                <div class="signInShortcut">
                    <p>Don't have an account? <a href="signin.php"><u>Sign Up</u></a></p>
                </div>
            </form>
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