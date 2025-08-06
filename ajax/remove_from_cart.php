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

if ($cartId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit();
}

// Verify cart item belongs to user
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
$stmt->execute([$cartId, $_SESSION['user_id']]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit();
}

// Remove from cart
if (removeFromCart($cartId)) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
}
?> 