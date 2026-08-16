<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'smart_kitchen'
];

// Constants
define('VAT_PERCENT', 15); // 15% VAT
define('SERVICE_TAX_PERCENT', 5); // 5% Service Tax

// Establish database connection
$conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// =============================================
// SYSTEM INITIALIZATION
// =============================================
$system_config = [
    'upload' => [
        'dir' => 'uploads/',
        'max_size' => 2 * 1024 * 1024, // 2MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif']
    ],
    'categories' => ['softdrink', 'yetsom', 'dessert', 'yefisik', 'hotdrink', 'juice']
];

// Create upload directory if it doesn't exist
if (!file_exists($system_config['upload']['dir'])) {
    mkdir($system_config['upload']['dir'], 0755, true);
}

// Initialize form data
$formData = [
    'name' => '',
    'category' => '',
    'price' => '',
    'ing_1' => '',
    'ing_2' => '',
    'image' => '',
    'frozen' => 0
];

// =============================================
// DATABASE SCHEMA SETUP
// =============================================
$conn->query("CREATE TABLE IF NOT EXISTS food_drink (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    ing_1 VARCHAR(100) NOT NULL,
    ing_2 VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    frozen BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    items TEXT NOT NULL,
    status ENUM('pending', 'preparing', 'ready', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS finance_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    vat DECIMAL(10,2) DEFAULT 0,
    service_tax DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// =============================================
// REQUEST HANDLING
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle menu item updates
    if (isset($_POST['update_id'])) {
        $itemId = (int)$_POST['update_id'];
        $stmt = $conn->prepare("SELECT * FROM food_drink WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $formData = $result->fetch_assoc();
            $formData['price'] = number_format($formData['price'], 2);
        }
        $stmt->close();
    }
    
    // Handle menu item creation/update
    if (isset($_POST['name']) && !isset($_POST['delete_id'])) {
        $isUpdate = isset($_POST['existing_id']);
        $itemId = $isUpdate ? (int)$_POST['existing_id'] : null;
        
        // Validate and sanitize input
        $formData = [
            'name' => trim($_POST['name']),
            'category' => trim($_POST['category']),
            'price' => trim($_POST['price']),
            'ing_1' => trim($_POST['ing_1']),
            'ing_2' => trim($_POST['ing_2']),
            'frozen' => isset($_POST['frozen']) ? 1 : 0
        ];
        
        $errors = [];
        if (empty($formData['name'])) $errors[] = "Name is required";
        if (empty($formData['category'])) $errors[] = "Category is required";
        if (empty($formData['price']) || !is_numeric($formData['price']) || $formData['price'] <= 0) {
            $errors[] = "Valid price is required";
        }
        if (empty($formData['ing_1'])) $errors[] = "Ingredient 1 is required";
        if (empty($formData['ing_2'])) $errors[] = "Ingredient 2 is required";
        
        // Handle file upload
        $imageName = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            
            if ($file['size'] > $system_config['upload']['max_size']) {
                $errors[] = "Image must be less than 2MB";
            }
            
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, $system_config['upload']['allowed_types'])) {
                $errors[] = "Only JPG, PNG, and GIF files are allowed";
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('img_', true) . '.' . $ext;
            $targetPath = $system_config['upload']['dir'] . $imageName;
            
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $errors[] = "Failed to upload image";
            }
        } elseif (!$isUpdate) {
            $errors[] = "Please upload an image";
        }
        
        if (empty($errors)) {
            $conn->begin_transaction();
            try {
                if ($isUpdate) {
                    // Get old image path if updating
                    $oldImage = '';
                    if (!empty($imageName)) {
                        $stmt = $conn->prepare("SELECT image FROM food_drink WHERE id = ?");
                        $stmt->bind_param("i", $itemId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $oldImage = $result->fetch_assoc()['image'];
                        $stmt->close();
                    }
                    
                    if (!empty($imageName)) {
                        $stmt = $conn->prepare("UPDATE food_drink SET name=?, category=?, price=?, ing_1=?, ing_2=?, image=?, frozen=? WHERE id=?");
                        $stmt->bind_param("ssdsssii", $formData['name'], $formData['category'], $formData['price'], 
                                          $formData['ing_1'], $formData['ing_2'], $imageName, $formData['frozen'], $itemId);
                    } else {
                        $stmt = $conn->prepare("UPDATE food_drink SET name=?, category=?, price=?, ing_1=?, ing_2=?, frozen=? WHERE id=?");
                        $stmt->bind_param("ssdssii", $formData['name'], $formData['category'], $formData['price'], 
                                          $formData['ing_1'], $formData['ing_2'], $formData['frozen'], $itemId);
                    }
                } else {
                    $stmt = $conn->prepare("INSERT INTO food_drink (name, category, price, ing_1, ing_2, image, frozen) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssdsssi", $formData['name'], $formData['category'], $formData['price'], 
                                  $formData['ing_1'], $formData['ing_2'], $imageName, $formData['frozen']);
                }
                
                if ($stmt->execute()) {
                    if ($isUpdate && !empty($imageName) && !empty($oldImage)) {
                        $oldPath = $system_config['upload']['dir'] . $oldImage;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    
                    $conn->commit();
                    $_SESSION['success'] = $isUpdate ? "Item updated successfully!" : "Item added successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } catch (Exception $e) {
                $conn->rollback();
                if (!empty($imageName) && file_exists($targetPath)) {
                    unlink($targetPath);
                }
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = implode("<br>", $errors);
        }
    }
    
    // Handle menu item deletion
    if (isset($_POST['delete_id'])) {
        $itemId = (int)$_POST['delete_id'];
        $conn->begin_transaction();
        
        try {
            // Get image path first
            $stmt = $conn->prepare("SELECT image FROM food_drink WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            $imagePath = $result->num_rows > 0 ? $system_config['upload']['dir'] . $result->fetch_assoc()['image'] : null;
            $stmt->close();
            
            // Delete record
            $stmt = $conn->prepare("DELETE FROM food_drink WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            
            $conn->commit();
            
            // Delete image file
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Handle freeze toggle
    if (isset($_POST['toggle_freeze'])) {
        $itemId = (int)$_POST['toggle_freeze'];
        
        $conn->begin_transaction();
        try {
            // Toggle the frozen status
            $stmt = $conn->prepare("UPDATE food_drink SET frozen = NOT frozen WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            
            // Get the new frozen status
            $stmt = $conn->prepare("SELECT frozen FROM food_drink WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            $isFrozen = $result->fetch_assoc()['frozen'];
            $stmt->close();
            
            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Item status updated',
                'isFrozen' => $isFrozen,
                'newText' => $isFrozen ? 'Unfreeze' : 'Freeze',
                'newIcon' => $isFrozen ? 'fa-fire' : 'fa-snowflake'
            ]);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update item: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Handle receipt generation
    if (isset($_POST['generate_receipt'])) {
        $order_id = intval($_POST['order_id']);
        
        $stmt = $conn->prepare("SELECT o.*, f.total_price FROM orders o LEFT JOIN finance_orders f ON o.id = f.order_id WHERE o.id = ? AND o.status = 'ready' AND f.status = 'pending'");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            
            $subtotal = floatval($order['total_price']);
            $vat = $subtotal * (VAT_PERCENT / 100);
            $service_tax = $subtotal * (SERVICE_TAX_PERCENT / 100);
            $total = $subtotal + $vat + $service_tax;
            
            $items = !empty($order['items']) ? explode(", ", $order['items']) : [];
            
            $_SESSION['receipt_data'] = [
                'order_id' => $order_id,
                'items' => $items,
                'subtotal' => $subtotal,
                'vat' => $vat,
                'service_tax' => $service_tax,
                'total' => $total,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } else {
            $_SESSION['error'] = "Order not found or not ready!";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Handle order completion
    if (isset($_POST['complete_order'])) {
        $order_id = intval($_POST['order_id']);
        
        $conn->begin_transaction();
        try {
            if (!isset($_SESSION['receipt_data']) || $_SESSION['receipt_data']['order_id'] != $order_id) {
                throw new Exception("Receipt data not found for this order!");
            }
            
            $receipt_data = $_SESSION['receipt_data'];
            
            // Update orders table
            $stmt1 = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND status = 'ready'");
            $stmt1->bind_param("i", $order_id);
            $stmt1->execute();
            
            // Update finance_orders table
            $stmt2 = $conn->prepare("UPDATE finance_orders SET status = 'completed', vat = ?, service_tax = ?, total = ? WHERE order_id = ? AND status = 'pending'");
            $stmt2->bind_param("dddi", $receipt_data['vat'], $receipt_data['service_tax'], $receipt_data['total'], $order_id);
            $stmt2->execute();
            
            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                $conn->commit();
                $_SESSION['success'] = "Order #$order_id marked as completed with financial records updated!";
                unset($_SESSION['receipt_data']);
            } else {
                $conn->rollback();
                $_SESSION['error'] = "Order not found or already completed!";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Failed to complete order: " . $e->getMessage();
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// =============================================
// DATA FETCHING
// =============================================
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Menu Items
$menuItems = $conn->query("SELECT * FROM food_drink ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Ready Orders - Only show items that aren't frozen
$ready_orders = $conn->query("
    SELECT o.*, f.total_price 
    FROM orders o 
    LEFT JOIN finance_orders f ON o.id = f.order_id 
    WHERE o.status = 'ready' 
    AND f.status = 'pending' 
    ORDER BY o.created_at DESC
");

// Today's Summary
$today_summary = $conn->query("SELECT COUNT(*) as today_orders, SUM(total) as today_revenue, AVG(total) as today_avg, SUM(vat) as today_vat, SUM(service_tax) as today_service_tax FROM finance_orders WHERE DATE(created_at) = '$today' AND status = 'completed'")->fetch_assoc() ?? ['today_orders' => 0, 'today_revenue' => 0, 'today_avg' => 0, 'today_vat' => 0, 'today_service_tax' => 0];

// Total Revenue
$total_revenue = $conn->query("SELECT SUM(total) as all_time_revenue, SUM(vat) as all_time_vat, SUM(service_tax) as all_time_service_tax FROM finance_orders WHERE status = 'completed'")->fetch_assoc() ?? ['all_time_revenue' => 0, 'all_time_vat' => 0, 'all_time_service_tax' => 0];

// Today's Orders
$today_orders = $conn->query("SELECT f.order_id as id, o.items, f.total as total_price, f.vat, f.service_tax, f.created_at FROM finance_orders f LEFT JOIN orders o ON f.order_id = o.id WHERE DATE(f.created_at) = '$today' AND f.status = 'completed' ORDER BY f.created_at DESC");

// Yesterday's Orders
$yesterday_orders = $conn->query("SELECT f.order_id as id, o.items, f.total as total_price, f.vat, f.service_tax, f.created_at FROM finance_orders f LEFT JOIN orders o ON f.order_id = o.id WHERE DATE(f.created_at) = '$yesterday' AND f.status = 'completed' ORDER BY f.created_at DESC");

// Historical Data
$past_dates = $conn->query("SELECT DATE(created_at) as order_date, SUM(total) as daily_total, SUM(vat) as daily_vat, SUM(service_tax) as daily_service_tax FROM finance_orders WHERE DATE(created_at) < '$yesterday' AND status = 'completed' GROUP BY DATE(created_at) ORDER BY order_date DESC");

// Clear session messages
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Kitchen Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3f37c9;
            --secondary: #f72585;
            --success: #4BB543;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --primary-color: #5a2c0c;
            --secondary-color: #c49b63;
            --accent-color: #e8c07d;
            --light-bg: #fff8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark);
        }

        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4') center/cover no-repeat;
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .nav-pills .nav-link {
            color: var(--dark);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            margin-right: 0.5rem;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary);
            color: white;
        }

        .tab-content {
            background: white;
            border-radius: 0 0 10px 10px;
            padding: 20px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .menu-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
            transition: all 0.3s;
            position: relative;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .menu-card.frozen {
            opacity: 0.7;
        }

        .menu-card.frozen::after {
            content: "FINISHED";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            background-color: rgba(244, 67, 54, 0.8);
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            padding: 5px 15px;
            border-radius: 5px;
            z-index: 2;
        }

        .card-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .card-category {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--light-gray);
            color: var(--dark);
            border-radius: 0.25rem;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .card-price {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0.5rem 0;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
        }

        .freeze-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.8rem;
        }

        .freeze-btn:hover {
            background: #f8f9fa;
        }

        .freeze-btn .fa-snowflake {
            color: #0dcaf0;
        }

        .freeze-btn .fa-fire {
            color: #fd7e14;
        }

        .frozen-checkbox {
            margin-top: 10px;
        }

        .order-card {
            transition: all 0.3s;
            cursor: pointer;
            border-left: 4px solid var(--secondary-color);
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .receipt-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed var(--secondary-color);
        }

        .receipt-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }

        .receipt-totals {
            margin-top: 20px;
            border-top: 2px solid var(--primary-color);
            padding-top: 15px;
        }

        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .receipt-grand-total {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .time-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 20px;
            margin: 30px 0 15px;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 100;
            cursor: pointer;
            transition: all 0.3s;
        }

        .refresh-btn:hover {
            background-color: var(--primary-color);
            transform: rotate(360deg) scale(1.1);
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(90, 44, 12, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(90, 44, 12, 0); }
            100% { box-shadow: 0 0 0 0 rgba(90, 44, 12, 0); }
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }

        .order-time {
            font-size: 0.8rem;
            color: #6c757d;
            white-space: nowrap;
        }

        .date-group {
            margin-bottom: 40px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none;
            }
        }

        /* New styles for quantity display */
        .order-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #eee;
        }

        .order-item-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .item-name {
            flex-grow: 1;
        }

        .order-quantity {
            background: var(--gradient-accent);
            color: black;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            min-width: 30px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header text-center">
        <div class="container">
            <h1><i class="fas fa-utensils me-2"></i> Smart Kitchen Dashboard</h1>
            <p class="lead">Complete management system for your kitchen operations</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-3" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cashier-tab" data-bs-toggle="pill" data-bs-target="#cashier" type="button" role="tab" aria-controls="cashier" aria-selected="true">
                    <i class="fas fa-cash-register me-2"></i> Cashier
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="menu-tab" data-bs-toggle="pill" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="false">
                    <i class="fas fa-utensils me-2"></i> Menu
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </button>
            </li>
        </ul> 

        <!-- Tab Content -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Cashier Tab -->
            <div class="tab-pane fade show active" id="cashier" role="tabpanel" aria-labelledby="cashier-tab">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="mb-3">Ready Orders</h3>
                        
                        <?php if ($ready_orders->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Order #</th>
                                            <th>Items</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $ready_orders->fetch_assoc()): 
                                            $items = explode(", ", $order['items']);
                                            $itemCounts = [];
                                            
                                            // Count quantities for each item
                                            foreach ($items as $item) {
                                                if (preg_match('/(.*)\s\(x(\d+)\)$/', $item, $matches)) {
                                                    $itemName = trim($matches[1]);
                                                    $quantity = (int)$matches[2];
                                                    if (isset($itemCounts[$itemName])) {
                                                        $itemCounts[$itemName] += $quantity;
                                                    } else {
                                                        $itemCounts[$itemName] = $quantity;
                                                    }
                                                } else {
                                                    $itemName = trim($item);
                                                    if (isset($itemCounts[$itemName])) {
                                                        $itemCounts[$itemName]++;
                                                    } else {
                                                        $itemCounts[$itemName] = 1;
                                                    }
                                                }
                                            }
                                            
                                            // Prepare items display with combined quantities
                                            $itemsDisplay = [];
                                            foreach ($itemCounts as $itemName => $totalQuantity) {
                                                $itemsDisplay[] = htmlspecialchars($itemName) . ' <span class="order-quantity">x(' . $totalQuantity . ')</span>';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($order['id']) ?></td>
                                            <td><?= implode('<br>', $itemsDisplay) ?></td>
                                            <td><?= number_format($order['total_price'], 2) ?> ETB</td>
                                            <td>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                                    <button type="submit" name="generate_receipt" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-receipt me-1"></i> Process
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-concierge-bell me-2"></i> No orders ready for payment
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <h3 class="mb-3">Payment Processing</h3>
                        
                        <?php if (isset($_SESSION['receipt_data'])): 
                            $receipt = $_SESSION['receipt_data'];
                        ?>
                        <div class="receipt-container">
                            <div class="receipt-header">
                                <h5 class="receipt-title">z Boss pastry</h5>
                                <p class="text-muted">Order #<?= htmlspecialchars($receipt['order_id']) ?></p>
                            </div>
                            
                            <div class="receipt-totals">
                                <div class="receipt-total-row">
                                    <span>Subtotal:</span>
                                    <span><?= number_format($receipt['subtotal'], 2) ?> ETB</span>
                                </div>
                                <div class="receipt-total-row">
                                    <span>VAT (<?= VAT_PERCENT ?>%):</span>
                                    <span><?= number_format($receipt['vat'], 2) ?> ETB</span>
                                </div>
                                <div class="receipt-total-row">
                                    <span>Service Tax (<?= SERVICE_TAX_PERCENT ?>%):</span>
                                    <span><?= number_format($receipt['service_tax'], 2) ?> ETB</span>
                                </div>
                                <div class="receipt-total-row receipt-grand-total">
                                    <span>Total:</span>
                                    <span><?= number_format($receipt['total'], 2) ?> ETB</span>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <form method="post">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($receipt['order_id']) ?>">
                                    <button type="submit" name="complete_order" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle me-1"></i> Complete Payment
                                    </button>
                                    <button onclick="window.print()" class="btn btn-outline-primary w-100 mt-2">
                                        <i class="fas fa-print me-1"></i> Print Receipt
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5>No Order Selected</h5>
                                <p class="text-muted">Select an order to process payment</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Menu Tab -->
            <div class="tab-pane fade" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= isset($_POST['update_id']) ? 'Update Menu Item' : 'Add Menu Item' ?></h5>
                                
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger">
                                        <?= htmlspecialchars($_SESSION['error']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success">
                                        <?= htmlspecialchars($_SESSION['success']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <?php if (isset($_POST['update_id'])): ?>
                                        <input type="hidden" name="existing_id" value="<?= (int)$_POST['update_id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Item Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category" required>
                                            <option value="" disabled <?= empty($formData['category']) ? 'selected' : '' ?>>Select category</option>
                                            <?php foreach ($system_config['categories'] as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat) ?>" <?= $formData['category'] === $cat ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars(ucfirst($cat)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control" name="price" step="0.01" min="0.01" value="<?= htmlspecialchars($formData['price']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ingredient 1</label>
                                        <input type="text" class="form-control" name="ing_1" value="<?= htmlspecialchars($formData['ing_1']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ingredient 2</label>
                                        <input type="text" class="form-control" name="ing_2" value="<?= htmlspecialchars($formData['ing_2']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Item Image</label>
                                        <input type="file" class="form-control" name="image" <?= isset($_POST['update_id']) ? '' : 'required' ?>>
                                        <?php if (isset($_POST['update_id']) && !empty($formData['image'])): ?>
                                            <img src="<?= htmlspecialchars($system_config['upload']['dir'] . $formData['image']) ?>" class="img-thumbnail mt-2" style="max-height: 150px;">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3 form-check frozen-checkbox">
                                        <input type="checkbox" class="form-check-input" name="frozen" id="frozen" <?= $formData['frozen'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="frozen">Mark as finished (unavailable for ordering)</label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <?= isset($_POST['update_id']) ? 'Update Item' : 'Add Item' ?>
                                    </button>
                                    
                                    <?php if (isset($_POST['update_id'])): ?>
                                        <button type="button" id="cancelUpdate" class="btn btn-outline-secondary w-100 mt-2">
                                            Cancel Update
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <h5 class="mb-3">Menu Items</h5>
                        
                        <div class="menu-grid">
                            <?php if (!empty($menuItems)): ?>
                                <?php foreach ($menuItems as $item): ?>
                                    <div class="menu-card <?= $item['frozen'] ? 'frozen' : '' ?>">
                                        <img src="<?= htmlspecialchars($system_config['upload']['dir'] . $item['image']) ?>" 
                                             class="card-image" onerror="this.src='https://via.placeholder.com/300x180?text=No+Image'">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($item['name']) ?></h6>
                                            <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($item['category'])) ?></span>
                                            <div class="card-price mt-2"><?= number_format($item['price'], 2) ?> ETB</div>
                                            <div class="card-actions mt-3">
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="update_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-warning freeze-btn" data-id="<?= $item['id'] ?>">
                                                    <i class="fas <?= $item['frozen'] ? 'fa-fire' : 'fa-snowflake' ?>"></i>
                                                    <?= $item['frozen'] ? 'Unfreeze' : 'Freeze' ?>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $item['id'] ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No menu items found. Add your first item.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Tab -->
            <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                <!-- Summary Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value"><?= $today_summary['today_orders'] ?? 0 ?></div>
                            <h3>Today's completed Orders</h3>
                            <div class="text-success mt-2">
                                <i class="fas fa-check-circle me-2"></i>
                                Completed
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value"><?= number_format($total_revenue['all_time_revenue'] ?? 0, 2) ?> ETB</div>
                            <h3>Total Revenue</h3>
                            <div class="text-primary mt-2">
                                <i class="fas fa-coins me-2"></i>
                                All-Time
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value"><?= number_format($today_summary['today_avg'] ?? 0, 2) ?> ETB</div>
                            <h3>Today's Average</h3>
                            <div class="text-info mt-2">
                                <i class="fas fa-chart-line me-2"></i>
                                Per Order
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's completed Orders -->
                <div class="date-group">
                    <div class="time-header">
                        <i class="fas fa-calendar-day me-2"></i>
                        Today's completed Orders - <?= date('F j, Y') ?>
                    </div>
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($today_orders->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-hashtag"></i> Order ID</th>
                                                <th><i class="fas fa-list"></i> Items</th>
                                                <th><i class="fas fa-money-bill-wave"></i> Total</th>
                                                <th><i class="fas fa-clock"></i> Time Completed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($order = $today_orders->fetch_assoc()): 
                                                $items = explode(", ", $order['items']);
                                                $itemCounts = [];
                                                
                                                // Count quantities for each item
                                                foreach ($items as $item) {
                                                    if (preg_match('/(.*)\s\(x(\d+)\)$/', $item, $matches)) {
                                                        $itemName = trim($matches[1]);
                                                        $quantity = (int)$matches[2];
                                                        if (isset($itemCounts[$itemName])) {
                                                            $itemCounts[$itemName] += $quantity;
                                                        } else {
                                                            $itemCounts[$itemName] = $quantity;
                                                        }
                                                    } else {
                                                        $itemName = trim($item);
                                                        if (isset($itemCounts[$itemName])) {
                                                            $itemCounts[$itemName]++;
                                                        } else {
                                                            $itemCounts[$itemName] = 1;
                                                        }
                                                    }
                                                }
                                                
                                                // Prepare items display with combined quantities
                                                $itemsDisplay = [];
                                                foreach ($itemCounts as $itemName => $totalQuantity) {
                                                    $itemsDisplay[] = htmlspecialchars($itemName) . ' <span class="order-quantity">x(' . $totalQuantity . ')</span>';
                                                }
                                            ?>
                                            <tr>
                                                <td><span class="badge bg-secondary">#<?= $order['id'] ?></span></td>
                                                <td><?= implode('<br>', $itemsDisplay) ?></td>
                                                <td class="fw-bold"><?= number_format($order['total_price'], 2) ?> ETB</td>
                                                <td class="order-time"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-concierge-bell"></i>
                                    <h4>No completed Orders Today</h4>
                                    <p>All pending orders will appear here when completed</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Yesterday's completed Orders -->
                <div class="date-group">
                    <div class="time-header">
                        <i class="fas fa-calendar-minus me-2"></i>
                        Yesterday's completed Orders - <?= date('F j, Y', strtotime('-1 day')) ?>
                    </div>
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($yesterday_orders->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-hashtag"></i> Order ID</th>
                                                <th><i class="fas fa-list"></i> Items</th>
                                                <th><i class="fas fa-money-bill-wave"></i> Total</th>
                                                <th><i class="fas fa-clock"></i> Time Completed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($order = $yesterday_orders->fetch_assoc()): 
                                                $items = explode(", ", $order['items']);
                                                $itemCounts = [];
                                                
                                                // Count quantities for each item
                                                foreach ($items as $item) {
                                                    if (preg_match('/(.*)\s\(x(\d+)\)$/', $item, $matches)) {
                                                        $itemName = trim($matches[1]);
                                                        $quantity = (int)$matches[2];
                                                        if (isset($itemCounts[$itemName])) {
                                                            $itemCounts[$itemName] += $quantity;
                                                        } else {
                                                            $itemCounts[$itemName] = $quantity;
                                                        }
                                                    } else {
                                                        $itemName = trim($item);
                                                        if (isset($itemCounts[$itemName])) {
                                                            $itemCounts[$itemName]++;
                                                        } else {
                                                            $itemCounts[$itemName] = 1;
                                                        }
                                                    }
                                                }
                                                
                                                // Prepare items display with combined quantities
                                                $itemsDisplay = [];
                                                foreach ($itemCounts as $itemName => $totalQuantity) {
                                                    $itemsDisplay[] = htmlspecialchars($itemName) . ' <span class="order-quantity">x(' . $totalQuantity . ')</span>';
                                                }
                                            ?>
                                            <tr>
                                                <td><span class="badge bg-secondary">#<?= $order['id'] ?></span></td>
                                                <td><?= implode('<br>', $itemsDisplay) ?></td>
                                                <td class="fw-bold"><?= number_format($order['total_price'], 2) ?> ETB</td>
                                                <td class="order-time"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-concierge-bell"></i>
                                    <h4>No completed Orders Yesterday</h4>
                                    <p>No orders were marked completed on this date</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Past completed Orders (Grouped by Date) -->
                <div class="date-group">
                    <div class="time-header">
                        <i class="fas fa-history me-2"></i>
                        Historical completed Orders
                    </div>
                    <?php if ($past_dates->num_rows > 0): ?>
                        <?php while($date_row = $past_dates->fetch_assoc()): 
                            $current_date = $date_row['order_date'];
                            $date_orders = $conn->query("SELECT f.order_id as id, o.items, f.total as total_price, f.vat, f.service_tax, f.created_at 
                                                       FROM finance_orders f 
                                                       LEFT JOIN orders o ON f.order_id = o.id 
                                                       WHERE DATE(f.created_at) = '$current_date' AND f.status = 'completed'
                                                       ORDER BY f.created_at DESC");
                        ?>
                        <div class="card mb-4 border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="far fa-calendar me-2"></i>
                                    <?= date('F j, Y', strtotime($current_date)) ?>
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($date_orders->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-hashtag"></i> Order ID</th>
                                                    <th><i class="fas fa-list"></i> Items</th>
                                                    <th><i class="fas fa-money-bill-wave"></i> Total</th>
                                                    <th><i class="fas fa-clock"></i> Time Completed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($order = $date_orders->fetch_assoc()): 
                                                    $items = explode(", ", $order['items']);
                                                    $itemCounts = [];
                                                    
                                                    // Count quantities for each item
                                                    foreach ($items as $item) {
                                                        if (preg_match('/(.*)\s\(x(\d+)\)$/', $item, $matches)) {
                                                            $itemName = trim($matches[1]);
                                                            $quantity = (int)$matches[2];
                                                            if (isset($itemCounts[$itemName])) {
                                                                $itemCounts[$itemName] += $quantity;
                                                            } else {
                                                                $itemCounts[$itemName] = $quantity;
                                                            }
                                                        } else {
                                                            $itemName = trim($item);
                                                            if (isset($itemCounts[$itemName])) {
                                                                $itemCounts[$itemName]++;
                                                            } else {
                                                                $itemCounts[$itemName] = 1;
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Prepare items display with combined quantities
                                                    $itemsDisplay = [];
                                                    foreach ($itemCounts as $itemName => $totalQuantity) {
                                                        $itemsDisplay[] = htmlspecialchars($itemName) . ' <span class="order-quantity">x(' . $totalQuantity . ')</span>';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary">#<?= $order['id'] ?></span></td>
                                                    <td><?= implode('<br>', $itemsDisplay) ?></td>
                                                    <td class="fw-bold"><?= number_format($order['total_price'], 2) ?> ETB</td>
                                                    <td class="order-time"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($order['created_at'])) ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-concierge-bell"></i>
                                        <h4>No completed Orders</h4>
                                        <p>No orders were marked completed on this date</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-concierge-bell"></i>
                            <h4>No Historical Orders Found</h4>
                            <p>No completed orders found before yesterday</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="refresh-btn pulse" id="refreshButton">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Refresh button functionality
        document.getElementById('refreshButton').addEventListener('click', function() {
            this.querySelector('i').classList.add('fa-spin');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });

        // Freeze button handler
        document.querySelectorAll('.freeze-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const card = this.closest('.menu-card');
                const icon = this.querySelector('i');
                
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'toggle_freeze=' + itemId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update button text and icon
                        this.innerHTML = `<i class="fas ${data.newIcon}"></i> ${data.newText}`;
                        
                        // Toggle frozen class on card
                        if (data.isFrozen) {
                            card.classList.add('frozen');
                        } else {
                            card.classList.remove('frozen');
                        }
                        
                        // Show success message
                        const toast = document.createElement('div');
                        toast.className = 'position-fixed bottom-0 end-0 p-3';
                        toast.style.zIndex = '11';
                        toast.innerHTML = `
                            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header bg-success text-white">
                                    <strong class="me-auto">Success</strong>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    ${data.message}
                                </div>
                            </div>
                        `;
                        document.body.appendChild(toast);
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    } else {
                        throw new Error(data.message || 'Failed to update item status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    this.innerHTML = originalText;
                })
                .finally(() => {
                    this.disabled = false;
                });
            });
        });

        // Delete button handler
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                
                document.getElementById('confirmDelete').onclick = function() {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'delete_id=' + itemId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
                };
                
                modal.show();
            });
        });

        // Cancel update button
        document.getElementById('cancelUpdate')?.addEventListener('click', function() {
            window.location.href = window.location.href.split('?')[0];
        });

        // Auto-refresh every 60 seconds
        
    </script>
</body>
</html>
