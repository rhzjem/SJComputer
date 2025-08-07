<?php
require_once '../includes/functions.php';
requireAdmin();

// Get statistics
$conn = getDBConnection();
$stats = [];

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders");
$stmt->execute();
$stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total products
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM products");
$stmt->execute();
$stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total users (customers only)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
$stmt->execute();
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total revenue
$stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
$stmt->execute();
$stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;

// Recent orders
$recentOrders = getAllOrders();
$recentOrders = array_slice($recentOrders, 0, 5);

// Low stock products (less than 10 items)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 10");
$stmt->execute();
$stats['low_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending orders
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$stmt->execute();
$stats['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SJ Computer</title>
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
        
        .admin-nav li {
            position: relative;
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
        
        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-card h3 {
            margin: 0;
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .stat-card.revenue .number {
            color: #27ae60;
        }
        
        .stat-card.orders .number {
            color: #3498db;
        }
        
        .stat-card.products .number {
            color: #e74c3c;
        }
        
        .stat-card.users .number {
            color: #f39c12;
        }
        
        .stat-card.warning .number {
            color: #e67e22;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .recent-orders {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .recent-orders h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-table th,
        .order-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .order-table th {
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
        
        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .quick-actions h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        
        .action-btn:hover {
            background: #2980b9;
        }
        
        .action-btn.danger {
            background: #e74c3c;
        }
        
        .action-btn.danger:hover {
            background: #c0392b;
        }
        
        .action-btn.success {
            background: #27ae60;
        }
        
        .action-btn.success:hover {
            background: #229954;
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
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-tachometer-alt"></i> SJ Computer - Admin Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../logout.php" class="logout-btn" style="margin-left: 1rem;">Logout</a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>   

        </ul>
    </nav>

    <div class="admin-container">
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
            <p>Here's what's happening with your business today.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card revenue">
                <h3>Total Revenue</h3>
                <div class="number">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <p>All time earnings</p>
            </div>
            
            <div class="stat-card orders">
                <h3>Total Orders</h3>
                <div class="number"><?php echo $stats['total_orders']; ?></div>
                <p><?php echo $stats['pending_orders']; ?> pending</p>
            </div>
            
            <div class="stat-card products">
                <h3>Total Products</h3>
                <div class="number"><?php echo $stats['total_products']; ?></div>
                <p><?php echo $stats['low_stock']; ?> low stock</p>
            </div>
            
            <div class="stat-card users">
                <h3>Total Customers</h3>
                <div class="number"><?php echo $stats['total_users']; ?></div>
                <p>Registered users</p>
            </div>
        </div>

        <div class="content-grid">
            <div class="recent-orders">
                <h2><i class="fas fa-clock"></i> Recent Orders</h2>
                <?php if (empty($recentOrders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div class="action-buttons">
                    <a href="add_product.php" class="action-btn success">
                        <i class="fas fa-plus"></i>
                        <span>Add New Product</span>
                    </a>
                    
                    <a href="products.php" class="action-btn">
                        <i class="fas fa-box"></i>
                        <span>Manage Products</span>
                    </a>
                    
                    <a href="orders.php" class="action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>View All Orders</span>
                    </a>
                    
                    <a href="customers.php" class="action-btn">
                        <i class="fas fa-users"></i>
                        <span>Manage Customers</span>
                    </a>
                    
                    <a href="services.php" class="action-btn">
                        <i class="fas fa-tools"></i>
                        <span>Service Bookings</span>
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html> 