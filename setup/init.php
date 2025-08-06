<?php
// Initialize the entire e-commerce system
echo "<h2>SJ Computer E-commerce Setup</h2>";

// Step 1: Initialize database
echo "<h3>Step 1: Initializing Database...</h3>";
require_once __DIR__ . '/../config/database.php';
initializeDatabase();
echo "<p>✅ Database initialized successfully!</p>";

// Step 2: Add sample products
echo "<h3>Step 2: Adding Sample Products...</h3>";
require_once __DIR__ . '/sample_data.php';
echo "<p>✅ Sample products added successfully!</p>";

echo "<h3>Setup Complete! 🎉</h3>";
echo "<p>Your e-commerce website is now ready to use.</p>";
echo "<p><strong>Admin Login:</strong></p>";
echo "<ul>";
echo "<li>Username: admin</li>";
echo "<li>Password: admin123</li>";
echo "</ul>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='../index.php'>Visit Homepage</a></li>";
echo "<li><a href='../admin/dashboard.php'>Access Admin Panel</a></li>";
echo "<li><a href='../login.php'>User Login</a></li>";
echo "<li><a href='../signin.php'>User Registration</a></li>";
echo "</ul>";
?> 