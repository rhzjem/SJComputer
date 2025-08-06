<?php
require_once '../includes/functions.php';
requireAdmin();

$conn = getDBConnection();

// Get all customers (non-admin users)
$stmt = $conn->prepare("
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           SUM(o.total_amount) as total_spent,
           COUNT(DISTINCT sb.id) as total_services
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id
    LEFT JOIN service_bookings sb ON u.id = sb.user_id
    WHERE u.is_admin = 0 
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get customer statistics
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
$stmt->execute();
$totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stmt->execute();
$newCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - SJ Computer Admin</title>
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
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .customers-table {
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
        
        .customers-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .customers-table th,
        .customers-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .customers-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .customer-avatar {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .customer-details h4 {
            margin: 0;
            color: #2c3e50;
        }
        
        .customer-details small {
            color: #7f8c8d;
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
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
        
        .search-box {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #3498db;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-users"></i> SJ Computer - Customer Management</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../logout.php" class="logout-btn" style="margin-left: 1rem;">Logout</a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        </ul>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-users"></i> Customer Management</h2>
        </div>

        <div class="stats-cards">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="number"><?php echo $totalCustomers; ?></div>
            </div>
            <div class="stat-card">
                <h3>New This Month</h3>
                <div class="number"><?php echo $newCustomers; ?></div>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="customerSearch" placeholder="Search customers by name, email, or username..." onkeyup="searchCustomers()">
        </div>

        <div class="customers-table">
            <div class="table-header">
                <h3>All Customers (<?php echo count($customers); ?> total)</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact Info</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                            <th>Services</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    <p>No customers found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr class="customer-row">
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-avatar">
                                                <?php echo strtoupper(substr($customer['full_name'], 0, 1)); ?>
                                            </div>
                                            <div class="customer-details">
                                                <h4><?php echo htmlspecialchars($customer['full_name']); ?></h4>
                                                <small>@<?php echo htmlspecialchars($customer['username']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($customer['email']); ?></strong>
                                            <?php if ($customer['phone']): ?>
                                                <br><small>📞 <?php echo htmlspecialchars($customer['phone']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo $customer['total_orders']; ?> orders</span>
                                    </td>
                                    <td>
                                        <?php if ($customer['total_spent']): ?>
                                            <strong>₱<?php echo number_format($customer['total_spent'], 2); ?></strong>
                                        <?php else: ?>
                                            <span style="color: #7f8c8d;">No purchases</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success"><?php echo $customer['total_services']; ?> services</span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['total_orders'] > 0 || $customer['total_services'] > 0): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function searchCustomers() {
            const input = document.getElementById('customerSearch');
            const filter = input.value.toLowerCase();
            const tableBody = document.getElementById('customersTableBody');
            const rows = tableBody.getElementsByClassName('customer-row');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const customerName = row.querySelector('h4').textContent.toLowerCase();
                const customerEmail = row.querySelector('td:nth-child(2) strong').textContent.toLowerCase();
                const customerUsername = row.querySelector('small').textContent.toLowerCase();

                if (customerName.includes(filter) || customerEmail.includes(filter) || customerUsername.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html> 