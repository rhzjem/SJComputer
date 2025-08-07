<?php
require_once '../includes/functions.php';
requireAdmin();

$conn = getDBConnection();
$message = '';

// Handle status updates
if (isset($_POST['update_status'])) {
    $bookingId = (int)$_POST['booking_id'];
    $newStatus = sanitizeInput($_POST['status']);
    $adminNotes = sanitizeInput($_POST['admin_notes']);
    
    $stmt = $conn->prepare("UPDATE service_bookings SET status = ?, admin_notes = ? WHERE id = ?");
    if ($stmt->execute([$newStatus, $adminNotes, $bookingId])) {
        $message = 'Service booking status updated successfully!';
    } else {
        $message = 'Failed to update service booking status.';
    }
}

// Get all service bookings with customer information
$stmt = $conn->prepare("
    SELECT sb.*, u.username, sb.customer_name as full_name, sb.customer_email as email, sb.contact_number as phone
    FROM service_bookings sb 
    JOIN users u ON sb.user_id = u.id 
    ORDER BY sb.created_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get booking counts by status
$statusCounts = [];
$statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM service_bookings WHERE status = ?");
    $stmt->execute([$status]);
    $statusCounts[$status] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management - SJ Computer Admin</title>
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
        .status-filter.confirmed { background: #3498db; }
        .status-filter.in_progress { background: #9b59b6; }
        .status-filter.completed { background: #27ae60; }
        .status-filter.cancelled { background: #e74c3c; }
        .status-filter.all { background: #34495e; }
        
        .services-table {
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
        
        .services-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .services-table th,
        .services-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .services-table th {
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
        
        .status-confirmed {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-in_progress {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
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
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
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
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
        
        .service-name {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-tools"></i> SJ Computer - Service Management</h1>
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
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="services.php" class="active"><i class="fas fa-tools"></i> Services</a></li>
        </ul>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-tools"></i> Service Booking Management</h2>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="status-filters">
            <a href="?status=all" class="status-filter all">
                All Bookings (<?php echo array_sum($statusCounts); ?>)
            </a>
            <a href="?status=pending" class="status-filter pending">
                Pending (<?php echo $statusCounts['pending']; ?>)
            </a>
            <a href="?status=confirmed" class="status-filter confirmed">
                Confirmed (<?php echo $statusCounts['confirmed']; ?>)
            </a>
            <a href="?status=in_progress" class="status-filter in_progress">
                In Progress (<?php echo $statusCounts['in_progress']; ?>)
            </a>
            <a href="?status=completed" class="status-filter completed">
                Completed (<?php echo $statusCounts['completed']; ?>)
            </a>
            <a href="?status=cancelled" class="status-filter cancelled">
                Cancelled (<?php echo $statusCounts['cancelled']; ?>)
            </a>
        </div>

        <div class="services-table">
            <div class="table-header">
                <h3>All Service Bookings (<?php echo count($bookings); ?> total)</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Service Name</th>
                            <th>Preferred Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    <p>No service bookings found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $booking['id']; ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['full_name']); ?></strong>
                                        <br>
                                        <small style="color: #7f8c8d;">
                                            <?php echo htmlspecialchars($booking['email']); ?>
                                            <?php if ($booking['phone']): ?>
                                                <br>📞 <?php echo htmlspecialchars($booking['phone']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="service-name"><?php echo htmlspecialchars($booking['service_name']); ?></span>
                                        <br>
                                        <small style="color: #7f8c8d;">
                                            <?php echo htmlspecialchars(substr($booking['problem_description'], 0, 50)); ?>...
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($booking['preferred_date']): ?>
                                            <strong><?php echo date('M d, Y', strtotime($booking['preferred_date'])); ?></strong>
                                            
                                        <?php else: ?>
                                            <span style="color: #7f8c8d;">Not specified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $booking['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openServiceModal(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>', '<?php echo htmlspecialchars($booking['admin_notes'] ?? ''); ?>')" class="btn btn-update">
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

    <!-- Service Update Modal -->
    <div id="serviceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Service Booking Status</h3>
                <span class="close" onclick="closeServiceModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="booking_id" id="bookingId">
                <input type="hidden" name="update_status" value="1">
                
                <div class="form-group">
                    <label for="status">New Status:</label>
                    <select name="status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="admin_notes">Admin Notes:</label>
                    <textarea name="admin_notes" id="admin_notes" placeholder="Add any notes about this service booking..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeServiceModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openServiceModal(bookingId, currentStatus, adminNotes) {
            document.getElementById('bookingId').value = bookingId;
            document.getElementById('status').value = currentStatus;
            document.getElementById('admin_notes').value = adminNotes;
            document.getElementById('serviceModal').style.display = 'block';
        }
        
        function closeServiceModal() {
            document.getElementById('serviceModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('serviceModal');
            if (event.target == modal) {
                closeServiceModal();
            }
        }
    </script>
</body>
</html>