<?php
// Debug script to check login issues
echo "<h2>Login Debug Information</h2>";

// Check if database connection works
echo "<h3>1. Database Connection Test</h3>";
try {
    require_once 'includes/functions.php';
    $conn = getDBConnection();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit();
}

// Check if admin user exists
echo "<h3>2. Admin User Check</h3>";
$stmt = $conn->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "✅ Admin user found<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Is Admin: " . ($admin['is_admin'] ? 'Yes' : 'No') . "<br>";
    echo "Password Hash: " . substr($admin['password'], 0, 20) . "...<br>";
} else {
    echo "❌ Admin user not found<br>";
}

// Test password verification
echo "<h3>3. Password Verification Test</h3>";
if ($admin) {
    $testPassword = 'admin123';
    if (password_verify($testPassword, $admin['password'])) {
        echo "✅ Password 'admin123' is correct<br>";
    } else {
        echo "❌ Password 'admin123' is incorrect<br>";
    }
}

// Check if tables exist
echo "<h3>4. Database Tables Check</h3>";
$tables = ['users', 'categories', 'products', 'orders', 'order_items', 'cart'];
foreach ($tables as $table) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table' exists with $count records<br>";
    } catch (Exception $e) {
        echo "❌ Table '$table' missing or error: " . $e->getMessage() . "<br>";
    }
}

// Test login function
echo "<h3>5. Login Function Test</h3>";
if ($admin) {
    $result = loginUser('admin', 'admin123');
    echo "Login result: " . ($result['success'] ? 'Success' : 'Failed') . "<br>";
    echo "Message: " . $result['message'] . "<br>";
    
    if ($result['success']) {
        echo "Session data:<br>";
        echo "- user_id: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
        echo "- username: " . ($_SESSION['username'] ?? 'Not set') . "<br>";
        echo "- is_admin: " . ($_SESSION['is_admin'] ?? 'Not set') . "<br>";
    }
}

echo "<h3>6. Manual Setup</h3>";
echo "<p>If any issues were found above, try running the setup:</p>";
echo "<a href='setup/init.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Setup</a>";
?> 