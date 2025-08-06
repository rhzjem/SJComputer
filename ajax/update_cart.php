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

$cartId = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($cartId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit();
}

// Verify cart item belongs to user
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT c.*, p.stock_quantity FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.id = ? AND c.user_id = ?");
$stmt->execute([$cartId, $_SESSION['user_id']]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cartItem) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit();
}

// Check stock availability
if ($cartItem['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit();
}

// Update quantity
if (updateCartQuantity($cartId, $quantity)) {
    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
}
?> 