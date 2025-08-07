<?php
require_once '../includes/functions.php';
requireAdmin();

$conn = getDBConnection();

// Get product data if ID is provided
$product = null;
if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $product = getProductById($productId);
    if (!$product) {
        header('Location: products.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data and update product
    $id = (int)$_POST['id'];
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $categoryId = (int)$_POST['category_id'];
    $stockQuantity = (int)$_POST['stock_quantity'];
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle image upload
    $imagePath = $product['image_path']; // Keep existing by default
    
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = uploadImage($_FILES['image'], '../uploads/');
        if ($uploadResult) {
            $imagePath = 'uploads/' . basename($uploadResult);
            // Optionally delete the old image file if it exists
            if (!empty($product['image_path']) && file_exists('../' . $product['image_path'])) {
                unlink('../' . $product['image_path']);
            }
        }
    }
    
    if (updateProduct($id, $name, $description, $price, $categoryId, $imagePath, $stockQuantity, $isFeatured)) {
        header('Location: products.php?success=1');
        exit();
    } else {
        $error = "Failed to update product";
    }
}

$categories = getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - SJ Computer Admin</title>
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
        
        .add-btn, .back-btn {
            background: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s;
        }
        
        .back-btn {
            background: #7f8c8d;
        }
        
        .add-btn:hover {
            background: #2980b9;
        }
        
        .back-btn:hover {
            background: #6c757d;
        }
        
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .product-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        input[type="checkbox"] {
            width: auto;
        }
        
        .current-image {
            margin-top: 1rem;
        }
        
        .current-image img {
            max-width: 200px;
            height: auto;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        .btn-save {
            background: #27ae60;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s;
        }
        
        .btn-save:hover {
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
        <h1><i class="fas fa-edit"></i> SJ Computer - Edit Product</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../logout.php" class="logout-btn" style="margin-left: 1rem;">Logout</a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
        </ul>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-edit"></i> Edit Product</h2>
            <a href="products.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Products
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="edit_product.php" method="post" class="product-form" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <div class="form-group">
                <label>Product Image</label>
                <?php if ($product['image_path']): ?>
                    <div class="current-image">
                        <img src="../<?php echo $product['image_path']; ?>" alt="Current product image">
                        <p>Current: <?php echo basename($product['image_path']); ?></p>
                    </div>
                <?php else: ?>
                    <p>No image currently set</p>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                </div>

            <div class="form-group">
                <label>Featured Product</label>
                <div class="checkbox-container">
                    <input type="checkbox" id="is_featured" name="is_featured" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                    <label for="is_featured">Mark as featured</label>
                </div>
            </div>

            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</body>
</html>