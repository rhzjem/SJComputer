<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit();
}

// Check if product exists
$product = getProductById($productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

// Check stock availability
if ($product['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit();
}

// Add to cart
if (addToCart($_SESSION['user_id'], $productId, $quantity)) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}
?> 