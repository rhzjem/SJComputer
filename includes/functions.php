<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Session management
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

// User functions
function registerUser($username, $email, $password, $fullName, $phone = '', $address = '') {
    $conn = getDBConnection();
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $hashedPassword, $fullName, $phone, $address])) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function loginUser($username, $password) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        return ['success' => true, 'message' => 'Login successful'];
    } else {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
}

function logoutUser() {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Product functions
function getAllProducts($category = null, $search = null) {
    $conn = getDBConnection();
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = [];
    
    if ($category && $category !== 'all') {
        $sql .= " AND c.name = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFeaturedProducts() {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 6");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllCategories() {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cart functions
function addToCart($userId, $productId, $quantity = 1) {
    $conn = getDBConnection();
    
    // Check if item already exists in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        return $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Add new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $productId, $quantity]);
    }
}

function getCartItems($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image_path FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateCartQuantity($cartId, $quantity) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    return $stmt->execute([$quantity, $cartId]);
}

function removeFromCart($cartId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    return $stmt->execute([$cartId]);
}

function clearCart($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

// Order functions
function createOrder($userId, $totalAmount, $shippingAddress, $paymentMethod) {
    $conn = getDBConnection();
    
    try {
        $conn->beginTransaction();
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $totalAmount, $shippingAddress, $paymentMethod]);
        $orderId = $conn->lastInsertId();
        
        // Get cart items
        $cartItems = getCartItems($userId);
        
        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            
            // Update product stock
            $updateStmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $updateStmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Clear cart
        clearCart($userId);
        
        $conn->commit();
        return ['success' => true, 'order_id' => $orderId];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getUserOrders($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetails($orderId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT o.*, oi.*, p.name, p.image_path FROM orders o 
                           JOIN order_items oi ON o.id = oi.order_id 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE o.id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Admin functions
function getAllOrders() {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT o.*, u.username, u.full_name FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           ORDER BY o.created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($orderId, $status) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}

function addProduct($name, $description, $price, $categoryId, $imagePath, $stockQuantity, $isFeatured = false) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_path, stock_quantity, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $description, $price, $categoryId, $imagePath, $stockQuantity, $isFeatured]);
}

function updateProduct($id, $name, $description, $price, $categoryId, $imagePath, $stockQuantity, $isFeatured = false) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image_path = ?, stock_quantity = ?, is_featured = ? WHERE id = ?");
    return $stmt->execute([$name, $description, $price, $categoryId, $imagePath, $stockQuantity, $isFeatured, $id]);
}

function deleteProduct($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

// Utility functions
function formatPrice($price) {
    return '₱ ' . number_format($price, 2);
}

function getCartItemCount($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?: 0;
}

function uploadImage($file, $targetDir = 'uploads/') {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $targetDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    
    return false;
}
?> 