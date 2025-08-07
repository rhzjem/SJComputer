<?php
require_once '../includes/functions.php';
requireAdmin();

$conn = getDBConnection();
$message = '';

// Handle status updates
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = sanitizeInput($_POST['status']);
    
    if (updateOrderStatus($orderId, $newStatus)) {
        $message = 'Order status updated successfully!';
    } else {
        $message = 'Failed to update order status.';
    }
}

// Get all orders with customer information
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.full_name, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get order counts by status
$statusCounts = [];
$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE status = ?");
    $stmt->execute([$status]);
    $statusCounts[$status] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - SJ Computer Admin</title>
    <script src="https://kit.fontawesome.com/cca3f4e97d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f4f6f9;
        }
        
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            font-size: 1.5rem;
        }
        
        .admin-nav {
            background: #34495e;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            display: block;
            transition: background 0.3s;
        }
        
        .admin-nav a:hover {
            background: #2c3e50;
        }
        
        .admin-nav a.active {
            background: #3498db;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-header h2 {
            color: #2c3e50;
        }
        
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .status-filter {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .status-filter:hover {
            opacity: 0.8;
        }
        
        .status-filter.pending { background: #f39c12; }
        .status-filter.processing { background: #3498db; }
        .status-filter.shipped { background: #9b59b6; }
        .status-filter.delivered { background: #27ae60; }
        .status-filter.cancelled { background: #e74c3c; }
        .status-filter.all { background: #34495e; }
        
        .orders-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .orders-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .orders-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-view {
            background: #3498db;
            color: white;
        }
        
        .btn-view:hover {
            background: #2980b9;
        }
        
        .btn-update {
            background: #f39c12;
            color: white;
        }
        
        .btn-update:hover {
            background: #e67e22;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-shopping-cart"></i> SJ Computer - Order Management</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../logout.php" class="logout-btn" style="margin-left: 1rem;">Logout</a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
        </ul>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-shopping-cart"></i> Order Management</h2>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="status-filters">
            <a href="?status=all" class="status-filter all">
                All Orders (<?php echo array_sum($statusCounts); ?>)
            </a>
            <a href="?status=pending" class="status-filter pending">
                Pending (<?php echo $statusCounts['pending']; ?>)
            </a>
            <a href="?status=processing" class="status-filter processing">
                Processing (<?php echo $statusCounts['processing']; ?>)
            </a>
            <a href="?status=shipped" class="status-filter shipped">
                Shipped (<?php echo $statusCounts['shipped']; ?>)
            </a>
            <a href="?status=delivered" class="status-filter delivered">
                Delivered (<?php echo $statusCounts['delivered']; ?>)
            </a>
            <a href="?status=cancelled" class="status-filter cancelled">
                Cancelled (<?php echo $statusCounts['cancelled']; ?>)
            </a>
        </div>

        <div class="orders-table">
            <div class="table-header">
                <h3>All Orders (<?php echo count($orders); ?> total)</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    <p>No orders found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $order['id']; ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['full_name']); ?></strong>
                                        <br>
                                        <small style="color: #7f8c8d;">
                                            <?php echo htmlspecialchars($order['email']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')" class="btn btn-update">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <span class="close" onclick="closeStatusModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="order_id" id="orderId">
                <input type="hidden" name="update_status" value="1">
                
                <div class="form-group">
                    <label for="status">New Status:</label>
                    <select name="status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(orderId, currentStatus) {
            document.getElementById('orderId').value = orderId;
            document.getElementById('status').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>
</body>
</html> 