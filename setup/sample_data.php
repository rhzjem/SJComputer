<?php
require_once '../includes/functions.php';

// Sample products data based on existing images
$sampleProducts = [
    [
        'name' => 'Acer Aspire 5 15.6" Laptop Ryzen 5-5500U',
        'description' => 'Acer Aspire 5 15.6" Laptop Ryzen 5-5500U, 8GB RAM 512GB SSD - Silver',
        'price' => 30495.00,
        'category' => 'Laptop',
        'image_path' => 'images/Acer Aspire 5.png',
        'stock_quantity' => 10,
        'is_featured' => true
    ],
    [
        'name' => 'HP 2024 15-fd0000 Notebook',
        'description' => 'HP 2024 15-fd0000 Notebook, 15.6 HD Bright view display, Intel N200 Quad-core, 4GB DRR4 RAM, 12GB Flash localStorage',
        'price' => 21900.00,
        'category' => 'Laptop',
        'image_path' => 'images/HP 2024 Laptop.png',
        'stock_quantity' => 8,
        'is_featured' => false
    ],
    [
        'name' => 'Lenovo IdeaPad Slim 15.6 FHD',
        'description' => 'Lenovo IdeaPad Slim 15.6 FHD AMD Ryzen 3-7320U 8GB RAM 512GB SSD Windows 11',
        'price' => 29495.00,
        'category' => 'Laptop',
        'image_path' => 'images/Lenovo Ideapad.png',
        'stock_quantity' => 12,
        'is_featured' => true
    ],
    [
        'name' => 'Acer Desktop PC with Acer Monitor',
        'description' => 'Acer X2660g-I3-8100 Desktop PC with Acer S200GHQL Monitor',
        'price' => 29999.00,
        'category' => 'Desktop',
        'image_path' => 'images/Acer Desktop PC with Acer Monitor.png',
        'stock_quantity' => 5,
        'is_featured' => true
    ],
    [
        'name' => 'HP Desktop PC Intel Core i3',
        'description' => 'HP Desktop PC Intel Core i3 with 1TB HDD, 8GB RAM, 23.8 Monitor, Keyboard and Mouse Bundle',
        'price' => 35828.00,
        'category' => 'Desktop',
        'image_path' => 'images/HP Desktop PC Intel Core i3.png',
        'stock_quantity' => 7,
        'is_featured' => false
    ],
    [
        'name' => 'Lenovo IdeaCentre 5 Desktop Computer',
        'description' => 'Lenovo IdeaCentre 5 14IAB7 Desktop Computer, Intel Core I5-2400. 8GB DDR4, 500GB SSD, Integrated Intel UHD Graphics 730',
        'price' => 28729.00,
        'category' => 'Desktop',
        'image_path' => 'images/Lenovo IdeaCentre 5 14IAB7 Desktop Computer.png',
        'stock_quantity' => 6,
        'is_featured' => false
    ],
    [
        'name' => 'ACER SA222Q EBI LED Monitor',
        'description' => 'ACER SA222Q EBI, LED, 1920 X 1080, 5MS (GTG) 21.5", 100Hz, HDMI 1.4*1, VGA *1, High Refresh Rate',
        'price' => 4997.00,
        'category' => 'Monitor',
        'image_path' => 'images/ACER SA222Q EBI  LED  1920 X 1080  5MS (GTG)  .png',
        'stock_quantity' => 15,
        'is_featured' => false
    ],
    [
        'name' => 'Samsung 22-Inch Full HD IPS Monitor',
        'description' => 'Samsung 22-Inch Full HD IPS Monitor, 3-Sided Bordless Design, LS22C310',
        'price' => 5999.00,
        'category' => 'Monitor',
        'image_path' => 'images/Samsung 22-Inch Full HD.png',
        'stock_quantity' => 20,
        'is_featured' => true
    ],
    [
        'name' => 'Kingston NV2 1TB NVMe PCIe Internal SSD',
        'description' => 'Kingston NV2 1TB NVMe PCIe Internal Solid State Drive',
        'price' => 4200.00,
        'category' => 'SSD',
        'image_path' => 'images/Kingston NV2 1TB NVMe PCIe Internal SSD.png',
        'stock_quantity' => 25,
        'is_featured' => false
    ],
    [
        'name' => 'Kingston SKC600/512G 512GB SATA3 2.5" SSD',
        'description' => 'Kingston SKC600/512G 512GB SATA3 2.5" Solid State Drive',
        'price' => 3200.00,
        'category' => 'SSD',
        'image_path' => 'images/Kingston SKC600512G 512GB SATA3.png',
        'stock_quantity' => 30,
        'is_featured' => false
    ],
    [
        'name' => 'Kingston 480GB SSD',
        'description' => 'Kingston 480GB Solid State Drive',
        'price' => 2800.00,
        'category' => 'SSD',
        'image_path' => 'images/Kingston SA400S37480G SSDNOW A400 480GB.png',
        'stock_quantity' => 35,
        'is_featured' => true
    ],
    [
        'name' => 'Kingston 240GB SSD',
        'description' => 'Kingston 240GB Solid State Drive',
        'price' => 2100.00,
        'category' => 'SSD',
        'image_path' => 'images/Kingston 240 GB SSD.png',
        'stock_quantity' => 40,
        'is_featured' => false
    ],
    [
        'name' => 'APC UPS 650VA',
        'description' => 'APC UPC 650VA',
        'price' => 3400.00,
        'category' => 'UPS',
        'image_path' => 'images/APC UPS 650VA.png',
        'stock_quantity' => 12,
        'is_featured' => false
    ],
    [
        'name' => 'APC UPS 1200VA',
        'description' => 'APC UPC 1200VA',
        'price' => 7799.00,
        'category' => 'UPS',
        'image_path' => 'images/APC UPS  1200VA.png',
        'stock_quantity' => 8,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 003 Black',
        'description' => 'Epson Ink 003 Black',
        'price' => 260.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 003 Black.png',
        'stock_quantity' => 100,
        'is_featured' => true
    ],
    [
        'name' => 'Epson Ink 664 Black',
        'description' => 'Epson Ink 664 Black',
        'price' => 260.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 664 Black.png',
        'stock_quantity' => 95,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 003 Cyan',
        'description' => 'Epson Ink 003 Cyan',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 003 Cyan.png',
        'stock_quantity' => 90,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 003 Magenta',
        'description' => 'Epson Ink 003 Magenta',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 003 Magenta.png',
        'stock_quantity' => 85,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 003 Yellow',
        'description' => 'Epson Ink 003 Yellow',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 003 Yellow.png',
        'stock_quantity' => 80,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 664 Cyan',
        'description' => 'Epson Ink 664 Cyan',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 664 Cyan.png',
        'stock_quantity' => 75,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 664 Magenta',
        'description' => 'Epson Ink 664 Magenta',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 664 Magenta.png',
        'stock_quantity' => 70,
        'is_featured' => false
    ],
    [
        'name' => 'Epson Ink 664 Yellow',
        'description' => 'Epson Ink 664 Yellow',
        'price' => 275.00,
        'category' => 'Ink',
        'image_path' => 'images/Epson Ink 664 Yellow.png',
        'stock_quantity' => 65,
        'is_featured' => false
    ],
    [
        'name' => 'Epson EcoTank L1210 A4 Ink Tank Printer',
        'description' => 'Epson EcoTank L1210 A4 Ink Tank Printer',
        'price' => 5995.00,
        'category' => 'Printer',
        'image_path' => 'images/Epson EcoTank L1210 A4 Ink Tank Printer.png',
        'stock_quantity' => 15,
        'is_featured' => true
    ],
    [
        'name' => 'Epson L3210 A4 All-in-One Ink Tank Printer',
        'description' => 'Epson L3210 A4 All-in-One Ink Tank Printer',
        'price' => 8975.00,
        'category' => 'Printer',
        'image_path' => 'images/Epson L3210 A4 All-in-One Ink Tank Printer.png',
        'stock_quantity' => 10,
        'is_featured' => false
    ],
    [
        'name' => 'Epson L5290 Wi-Fi All-in-One Ink Tank Printer with ADF',
        'description' => 'Epson L5290 Wi-Fi All-in-One Ink Tank Printer with ADF',
        'price' => 13995.00,
        'category' => 'Printer',
        'image_path' => 'images/Epson L5290 Wi-Fi All-in-One Ink Tank Printer with ADF.png',
        'stock_quantity' => 8,
        'is_featured' => false
    ]
];

// Get category IDs
$categories = getAllCategories();
$categoryMap = [];
foreach ($categories as $category) {
    $categoryMap[$category['name']] = $category['id'];
}

// Insert products
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_path, stock_quantity, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");

$inserted = 0;
foreach ($sampleProducts as $product) {
    if (isset($categoryMap[$product['category']])) {
        try {
            $stmt->execute([
                $product['name'],
                $product['description'],
                $product['price'],
                $categoryMap[$product['category']],
                $product['image_path'],
                $product['stock_quantity'],
                $product['is_featured']
            ]);
            $inserted++;
        } catch (PDOException $e) {
            // Product might already exist, skip
            continue;
        }
    }
}

echo "Successfully inserted $inserted sample products!";
?> 