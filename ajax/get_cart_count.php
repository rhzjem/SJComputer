<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$count = getCartItemCount($_SESSION['user_id']);
echo json_encode(['success' => true, 'count' => $count]);
?>