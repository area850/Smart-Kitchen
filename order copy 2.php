<?php
session_start();

// Database connection for status validation
$conn_status = new mysqli("localhost", "root", "", "smart_kitchen");
if ($conn_status->connect_error) {
    die("Database connection failed: " . $conn_status->connect_error);
}

$valid_order = false;

// Clear order if 10 seconds have passed since it was marked ready
if (isset($_SESSION['order_ready_time'])) {
    $time_elapsed = time() - $_SESSION['order_ready_time'];
    if ($time_elapsed > 10) {
        unset($_SESSION['last_order_id']);
        unset($_SESSION['last_status']);
        unset($_SESSION['order_ready_time']);
        unset($_SESSION['total_price']);
        unset($_SESSION['order_start_time']);
        unset($_SESSION['estimated_cooking_time']);
    }
}

if (isset($_SESSION['last_order_id'])) {
    $check_order = $conn_status->prepare("SELECT id, status, cooking_time FROM orders WHERE id = ?");
    $check_order->bind_param("i", $_SESSION['last_order_id']);
    $check_order->execute();
    $result = $check_order->get_result();
    
    if ($result->num_rows > 0) {
        $valid_order = true;
        $order_data = $result->fetch_assoc();
        $_SESSION['last_status'] = $order_data['status'];
        
        // Set cooking time if not already set
        if (!isset($_SESSION['estimated_cooking_time']) && $order_data['cooking_time'] > 0) {
            $_SESSION['estimated_cooking_time'] = $order_data['cooking_time'];
            $_SESSION['order_start_time'] = time();
        }
        
        // Set ready time if order is ready
        if ($order_data['status'] === 'ready' && !isset($_SESSION['order_ready_time'])) {
            $_SESSION['order_ready_time'] = time();
        }
    } else {
        unset($_SESSION['last_order_id']);
        unset($_SESSION['last_status']);
        unset($_SESSION['order_ready_time']);
        unset($_SESSION['order_start_time']);
        unset($_SESSION['estimated_cooking_time']);
    }
}
$conn_status->close();

// Prevent duplicate submission on page refresh
if (isset($_SESSION['order_submitted']) && $_SESSION['order_submitted'] === true) {
    unset($_SESSION['order_submitted']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Database connection for menu and orders
$conn = new mysqli("localhost", "root", "", "smart_kitchen");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load menu items with cooking time estimates
$sql = "SELECT id, name, category, price, ing_1, ing_2, image, cooking_time FROM food_drink ORDER BY category, name";
$result = $conn->query($sql);

$menu = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu[$row['category']][] = $row;
    }
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order'])) {
    if (!empty($_POST['food_drink']) && is_array($_POST['food_drink'])) {
        $selected_items = $_POST['food_drink'];
        $quantities = $_POST['quantity'];
        $items = [];
        $ingredients1 = [];
        $ingredients2 = [];
        $total_price = 0;
        $item_details = [];
        $total_cooking_time = 0;

        // Prepare the items for query
        $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
        $types = str_repeat('s', count($selected_items));
        $stmt = $conn->prepare("SELECT name, price, ing_1, ing_2, image, cooking_time FROM food_drink WHERE name IN ($placeholders)");
        $stmt->bind_param($types, ...$selected_items);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($item = $result->fetch_assoc()) {
            $index = array_search($item['name'], $selected_items);
            $quantity = isset($quantities[$index]) ? max(1, intval($quantities[$index])) : 1;
            
            for ($i = 0; $i < $quantity; $i++) {
                $items[] = $item['name'];
                $ingredients1[] = $item['ing_1'];
                $ingredients2[] = $item['ing_2'];
                $total_price += $item['price'];
            }
            
            // Calculate total cooking time based on quantity
            $total_cooking_time += $item['cooking_time'] * $quantity;
            
            $item_details[] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity,
                'total' => $item['price'] * $quantity,
                'cooking_time' => $item['cooking_time']
            ];
        }

        if (!empty($items)) {
            $items_str = implode(", ", $items);
            $ingredients1_str = implode(", ", $ingredients1);
            $ingredients2_str = implode(", ", $ingredients2);

            $stmt_order = $conn->prepare("INSERT INTO orders (items, status, ing_1, ing_2, cooking_time) VALUES (?, 'pending', ?, ?, ?)");
            $stmt_order->bind_param("sssi", $items_str, $ingredients1_str, $ingredients2_str, $total_cooking_time);

            if ($stmt_order->execute()) {
                $order_id = $stmt_order->insert_id;
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['last_status'] = "pending";
                $_SESSION['total_price'] = $total_price;
                $_SESSION['estimated_cooking_time'] = $total_cooking_time;
                $_SESSION['order_start_time'] = time();
                $_SESSION['order_submitted'] = true;

                // Prepare items string for finance table including quantities
                $finance_items = [];
                foreach ($item_details as $detail) {
                    $finance_items[] = "{$detail['name']} (x{$detail['quantity']}) - {$detail['cooking_time']} min";
                }
                $finance_items_str = implode(", ", $finance_items);

                $stmt_finance = $conn->prepare("INSERT INTO finance_orders (order_id, items, total_price) VALUES (?, ?, ?)");
                $stmt_finance->bind_param("isd", $order_id, $finance_items_str, $total_price);
                $stmt_finance->execute();

                // Set game URL with cooking time parameter
                $game_url = "1-player.html?cookingTime=" . ($total_cooking_time * 60);
                
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error_msg = $stmt_order->error;
                echo "<script>alert('Database error: $error_msg');</script>";
            }
        }
    } else {
        echo "<script>alert('Please select at least one item before placing your order.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuovaCafe - Gourmet Experience</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #5a2c0c;
            --secondary: #c49b63;
            --accent: #e8c07d;
            --light: #fff8f0;
            --dark: #333;
            --gray: #666;
            --white: #fff;
            --error: #d32f2f;
            --success: #4caf50;
            --warning: #ffc107;
            --info: #2196f3;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4') center/cover no-repeat;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
            padding: 0 20px;
            margin-bottom: 40px;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin-bottom: 15px;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .header p {
            font-size: 1.2rem;
            max-width: 700px;
            opacity: 0.9;
        }

        /* Menu Container */
        .menu-container {
            max-width: 1200px;
            margin: 0 auto 40px;
            background-color: var(--white);
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .menu-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
        }

        /* Category Styles */
        .category-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 2.2rem;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px dashed var(--secondary);
            position: relative;
        }

        /* Menu Items Grid */
        .menu-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        /* Menu Item Card */
        .menu-item {
            display: flex;
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            background-color: var(--white);
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .item-image {
            width: 150px;
            height: 150px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .menu-item:hover .item-image img {
            transform: scale(1.1);
        }

        .item-details {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .item-name {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-price {
            color: var(--secondary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .item-ingredients {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 10px;
            font-style: italic;
        }

        .cooking-time {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 10px;
        }

        /* Item Selection Controls */
        .item-selection {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .item-checkbox {
            display: flex;
            align-items: center;
        }

        .item-checkbox input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid var(--secondary);
            border-radius: 4px;
            margin-right: 10px;
            cursor: pointer;
            position: relative;
            transition: var(--transition);
        }

        .item-checkbox input[type="checkbox"]:checked {
            background-color: var(--secondary);
        }

        .item-checkbox input[type="checkbox"]:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .item-quantity {
            display: flex;
            align-items: center;
        }

        .item-quantity input {
            width: 50px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 10px;
        }

        /* Category Actions */
        .category-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-clear {
            background-color: #f8f8f8;
            color: var(--error);
            border: 1px solid var(--error);
        }

        .btn-clear:hover {
            background-color: var(--error);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 47, 47, 0.3);
        }

        .btn-order {
            background-color: var(--secondary);
            color: var(--primary);
        }

        .btn-order:hover {
            background-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(226, 146, 26, 0.55);
        }

        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Order Summary */
        .order-summary {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--primary);
            color: white;
            padding: 15px 25px;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            z-index: 100;
            transition: var(--transition);
            cursor: pointer;
        }

        .order-summary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .order-summary i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Status Indicator */
        .status-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            z-index: 100;
            font-weight: 500;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-pending {
            background-color: var(--warning);
            animation: pulse 1.5s infinite;
        }

        .status-preparing {
            background-color: var(--info);
        }

        .status-ready {
            background-color: var(--success);
        }

        /* Enhanced Timer Styles with Animations */
        .timer-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 100;
            background-color: rgba(0,0,0,0.85);
            color: white;
            padding: 20px;
            border-radius: var(--radius-lg);
            font-family: 'Poppins', sans-serif;
            box-shadow: var(--shadow-lg);
            border-left: 5px solid var(--accent);
            transform: translateX(-120%);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .timer-container.visible {
            transform: translateX(0);
        }

        .timer-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .timer-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: var(--accent);
            animation: pulse 2s infinite;
        }

        .timer-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--accent);
        }

        .timer-display {
            font-weight: bold;
            font-size: 1.8rem;
            margin: 5px 0;
            text-align: center;
            font-family: 'Courier New', monospace;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .timer-label {
            font-size: 0.8rem;
            text-align: center;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .progress-container {
            position: relative;
            margin-top: 15px;
        }

        .progress-bar {
            height: 10px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .progress {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            width: 100%;
            transition: width 1s linear;
            position: relative;
            overflow: hidden;
        }

        .progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shine 2s infinite;
        }

        .progress-percent {
            position: absolute;
            right: 0;
            top: -20px;
            font-size: 0.8rem;
            color: var(--accent);
        }

        .time-remaining {
            font-size: 0.9rem;
            text-align: center;
            margin-top: 5px;
            opacity: 0.8;
        }

        /* Animation for timer warning state */
        .timer-warning {
            animation: blink 0.5s infinite alternate;
            color: #ff6b6b !important;
        }

        .progress-warning {
            background: linear-gradient(90deg, #ff6b6b, #ff0000) !important;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0;
            background: rgba(0,0,0,0.85);
            color: white;
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
            overflow: auto;
        }

        .modal-content {
            max-width: 500px;
            padding: 30px;
            background: rgba(255,255,255,0.1);
            border-radius: var(--radius-md);
            backdrop-filter: blur(10px);
        }

        .modal-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .modal-btn {
            background: var(--primary);
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            margin: 10px;
        }

        .modal-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .mode-btn {
            background: var(--secondary);
            border: none;
            color: var(--primary);
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            margin: 10px;
        }

        .mode-btn:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        /* Game Container */
        .game-container {
            display: none;
            margin-top: 20px;
            background: var(--white);
            color: var(--dark);
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10000;
        }

        /* Footer Styles */
        .site-footer {
            background-color: var(--primary);
            color: var(--light);
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
            border-top: 3px solid var(--secondary);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .site-footer p {
            margin: 10px 0;
            font-size: 1rem;
        }

        .copyright {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .copyright a {
            color: var(--accent);
            text-decoration: none;
            transition: var(--transition);
        }

        .copyright a:hover {
            color: var(--light);
            text-decoration: underline;
        }

        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes blink {
            0% { color: var(--accent); }
            50% { color: var(--error); }
            100% { color: var(--accent); }
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .menu-container {
                padding: 25px;
            }
            
            .menu-items {
                grid-template-columns: 1fr;
            }
            
            .item-image {
                width: 120px;
                height: 120px;
            }
            
            .category-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .item-selection {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .item-quantity {
                width: 100%;
                justify-content: space-between;
            }
            
            .site-footer {
                padding: 20px 0;
            }
            
            .site-footer p {
                font-size: 0.9rem;
            }
            
            .copyright {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Status Indicator -->
    <?php if ($valid_order && isset($_SESSION['last_status'])): ?>
        <div class="status-indicator" id="statusIndicator">
            <div class="status-dot status-<?= htmlspecialchars($_SESSION['last_status']) ?>" id="statusDot"></div>
            <span id="statusText">
                <?php if ($_SESSION['last_status'] === 'ready'): ?>
                    Your order is on its way 🚚 
                <?php elseif ($_SESSION['last_status'] === 'pending'): ?>
                    Order #<?= $_SESSION['last_order_id'] ?>: Being prepared
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Enhanced Timer Container -->
    <?php if (isset($_SESSION['estimated_cooking_time'])): ?>
    <div class="timer-container" id="timerContainer">
        <div class="timer-header">
            <div class="timer-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="timer-title">ESTIMATED COOKING TIME</div>
        </div>
        <div class="timer-display" id="timerDisplay"></div>
        <div class="timer-label">Time remaining until your order is ready</div>
        <div class="progress-container">
            <div class="progress-percent" id="progressPercent">100%</div>
            <div class="progress-bar">
                <div class="progress" id="progressBar"></div>
            </div>
        </div>
        <div class="time-remaining" id="timeRemaining"></div>
    </div>
    <?php endif; ?>

    <!-- Game Modal -->
    <div class="modal" id="gameModal">
        <div class="modal-content">
            <div id="pendingScreen">
                <h2 class="modal-title">Your order is being prepared</h2>
                <p style="margin-bottom: 20px;">Please enjoy our game while you wait</p>
                <button class="modal-btn" id="playBtn">Play Game</button>
            </div>

            <div id="modeSelectScreen">
                <h2 class="modal-title">Choose game mode</h2>
                <button class="mode-btn" data-mode="1p">1 Player</button>
                <button class="mode-btn" data-mode="2p">2 Players</button>
            </div>
        </div>
    </div>

    <!-- Game Container -->
    <div class="game-container" id="gameContainer"></div>

    <!-- Main Content -->
    <div class="header">
        <h1>NeuovaCafe</h1>
        <p>Experience artisanal flavors crafted with passion and precision</p>
    </div>

    <form method="post" id="orderForm">
        <?php foreach ($menu as $category => $items): ?>
            <div class="menu-container">
                <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>

                <div class="menu-items">
                    <?php foreach ($items as $item): ?>
                        <div class="menu-item">
                            <div class="item-image">
                                <img src="./uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="item-details">
                                <div class="item-name">
                                    <span><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="item-price"><?= htmlspecialchars($item['price']) ?> ETB</span>
                                </div>
                                <p class="item-ingredients">
                                    <?= htmlspecialchars($item['ing_1']) ?>, <?= htmlspecialchars($item['ing_2']) ?>
                                </p>
                                <div class="cooking-time">
                                    Cooking time: <?= htmlspecialchars($item['cooking_time']) ?> minutes
                                </div>
                                <div class="item-selection">
                                    <div class="item-checkbox">
                                        <input type="checkbox" name="food_drink[]" value="<?= htmlspecialchars($item['name']) ?>" id="item_<?= $item['id'] ?>">
                                        <label for="item_<?= $item['id'] ?>">Add to order</label>
                                    </div>
                                    <div class="item-quantity">
                                        <label for="quantity_<?= $item['id'] ?>">Qty:</label>
                                        <input type="number" name="quantity[]" id="quantity_<?= $item['id'] ?>" min="1" value="1" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="category-actions">
                    <button type="button" class="btn btn-clear" onclick="clearAllSelections()">
                        <i class="fas fa-trash-alt"></i> Clear Selections
                    </button>
                    <button type="button" class="btn btn-order place-order-btn" disabled>
                        <i class="fas fa-paper-plane"></i> Place Your Order
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="order-summary" id="orderSummary" style="display: none;">
            <i class="fas fa-shopping-bag"></i>
            <span id="selectedCount">0</span> items selected
        </div>
    </form>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> NeuovaCafe. All Rights Reserved.</p>
            <p class="copyright">Developed with  by <a href="https://neuovacoders.com" target="_blank">Neuova Coders</a></p>
        </div>
    </footer>

    <script>
        // Initialize variables
        const orderId = <?= isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 'null' ?>;
        const gameModal = document.getElementById('gameModal');
        const pendingScreen = document.getElementById('pendingScreen');
        const modeSelectScreen = document.getElementById('modeSelectScreen');
        const gameContainer = document.getElementById('gameContainer');
        const playBtn = document.getElementById('playBtn');
        const modeButtons = document.querySelectorAll('.mode-btn');
        const orderForm = document.getElementById('orderForm');
        let currentStatus = null;
        let readyNotificationShown = false;
        let timerInterval;
        let cookingTimerInterval;

        // Enhanced Timer functionality with hours support
        function startTimer() {
            const cookingTime = <?= isset($_SESSION['estimated_cooking_time']) ? $_SESSION['estimated_cooking_time'] * 60 : 0 ?>;
            const startTime = <?= isset($_SESSION['order_start_time']) ? $_SESSION['order_start_time'] : 'null' ?>;
            
            if (!cookingTime || !startTime) return;
            
            // Show the timer container with animation
            const timerContainer = document.getElementById('timerContainer');
            timerContainer.classList.add('visible');
            
            function formatTime(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = seconds % 60;
                
                if (hours > 0) {
                    return `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                } else {
                    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                }
            }
            
            function formatTimeRemaining(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = seconds % 60;
                
                let timeString = '';
                if (hours > 0) {
                    timeString += `${hours} hour${hours !== 1 ? 's' : ''} `;
                }
                if (minutes > 0) {
                    timeString += `${minutes} minute${minutes !== 1 ? 's' : ''} `;
                }
                if (remainingSeconds > 0 || (hours === 0 && minutes === 0)) {
                    timeString += `${remainingSeconds} second${remainingSeconds !== 1 ? 's' : ''}`;
                }
                
                return timeString + ' remaining';
            }
            
            function updateTimer() {
                const now = Math.floor(Date.now() / 1000);
                const elapsed = now - startTime;
                const remaining = Math.max(0, cookingTime - elapsed);
                const progress = (elapsed / cookingTime) * 100;
                
                // Update progress bar
                const progressBar = document.getElementById('progressBar');
                progressBar.style.width = `${100 - progress}%`;
                document.getElementById('progressPercent').textContent = `${Math.round(100 - progress)}%`;
                
                // Update timer display
                const timerDisplay = document.getElementById('timerDisplay');
                timerDisplay.textContent = formatTime(remaining);
                
                // Update time remaining text
                document.getElementById('timeRemaining').textContent = formatTimeRemaining(remaining);
                
                // Add warning styles when time is running out
                if (remaining <= 60) { // Last minute
                    timerDisplay.classList.add('timer-warning');
                    progressBar.classList.add('progress-warning');
                    
                    // Add bounce animation to icon
                    const timerIcon = document.querySelector('.timer-icon');
                    timerIcon.style.animation = 'bounce 0.5s infinite';
                } else {
                    timerDisplay.classList.remove('timer-warning');
                    progressBar.classList.remove('progress-warning');
                }
                
                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = "00:00";
                    timerDisplay.classList.add('timer-warning');
                    progressBar.style.width = '0%';
                    document.getElementById('progressPercent').textContent = '0%';
                    document.getElementById('timeRemaining').textContent = 'Your order should be ready now!';
                    
                    // Change icon to check mark
                    const timerIcon = document.querySelector('.timer-icon');
                    timerIcon.innerHTML = '<i class="fas fa-check"></i>';
                    timerIcon.style.animation = 'none';
                    timerIcon.style.color = 'var(--success)';
                    
                    setTimeout(() => {
                        timerContainer.classList.remove('visible');
                    }, 10000);
                }
            }
            
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }

        // Modal functions
        function showModal() {
            gameModal.style.display = 'flex';
            pendingScreen.style.display = 'block';
            modeSelectScreen.style.display = 'none';
            gameContainer.style.display = 'none';
            startTimer();
        }

        function hideModal() {
            gameModal.style.display = 'none';
            gameContainer.innerHTML = '';
            clearInterval(timerInterval);
        }

        // Game loading functions
        function loadGame(mode) {
            gameContainer.innerHTML = '';
            const iframe = document.createElement('iframe');
            iframe.src = mode === '1p' ? '1-player.html?cookingTime=<?= isset($_SESSION["estimated_cooking_time"]) ? $_SESSION["estimated_cooking_time"] * 60 : 900 ?>' : 
                                       '2-player.html?cookingTime=<?= isset($_SESSION["estimated_cooking_time"]) ? $_SESSION["estimated_cooking_time"] * 60 : 900 ?>';
            iframe.style.border = 'none';
            iframe.style.width = '100vw';
            iframe.style.height = '100vh';
            iframe.style.position = 'fixed';
            iframe.style.top = '0';
            iframe.style.left = '0';
            iframe.style.zIndex = '10000';

            gameContainer.appendChild(iframe);
            pendingScreen.style.display = 'none';
            modeSelectScreen.style.display = 'none';
            gameContainer.style.display = 'block';
        }

        // Event listeners for game buttons
        playBtn.addEventListener('click', () => {
            pendingScreen.style.display = 'none';
            modeSelectScreen.style.display = 'flex';
        });

        modeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const mode = btn.getAttribute('data-mode');
                loadGame(mode);
            });
        });

        // Order confirmation dialog for all place order buttons
        document.querySelectorAll('.place-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const checkedItems = document.querySelectorAll('.item-checkbox input[type="checkbox"]:checked');
                
                if (checkedItems.length === 0) {
                    Swal.fire({
                        title: 'No Items Selected',
                        text: 'Please select at least one item before placing your order.',
                        icon: 'warning',
                        confirmButtonColor: 'var(--primary)'
                    });
                    return;
                }
                
                let totalItems = 0;
                let totalPrice = 0;
                let totalCookingTime = 0;
                let itemsSummary = [];
                
                checkedItems.forEach(checkbox => {
                    const itemId = checkbox.id.replace('item_', '');
                    const quantityInput = document.getElementById(`quantity_${itemId}`);
                    const quantity = parseInt(quantityInput.value) || 1;
                    const priceText = checkbox.closest('.item-details').querySelector('.item-price').textContent;
                    const price = parseFloat(priceText);
                    const cookingTime = parseInt(checkbox.closest('.item-details').querySelector('.cooking-time').textContent.replace('Cooking time: ', '').replace(' minutes', ''));
                    
                    totalItems += quantity;
                    totalPrice += price * quantity;
                    totalCookingTime += cookingTime * quantity;
                    itemsSummary.push(`${checkbox.value} (x${quantity}) - ${(price * quantity).toFixed(2)} ETB (${cookingTime * quantity} min)`);
                });
                
                Swal.fire({
                    title: 'Confirm Your Order',
                    html: `
                        <div style="text-align: left;">
                            <p>You are about to place an order for <b>${totalItems} items</b> with a total of <b>${totalPrice.toFixed(2)} ETB</b>.</p>
                            <p>Total estimated cooking time: <b>${formatCookingTime(totalCookingTime)}</b></p>
                            <p><b>Order Details:</b></p>
                            <ul style="padding-left: 20px; margin-top: 5px;">
                                ${itemsSummary.map(item => `<li>${item}</li>`).join('')}
                            </ul>
                            <p style="margin-top: 10px;">Do you agree to proceed?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'I Agree',
                    cancelButtonText: 'Reject',
                    confirmButtonColor: 'var(--primary)',
                    cancelButtonColor: 'var(--error)',
                    width: '600px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a hidden submit button and click it to submit the form
                        const submitBtn = document.createElement('button');
                        submitBtn.type = 'submit';
                        submitBtn.name = 'order';
                        submitBtn.style.display = 'none';
                        orderForm.appendChild(submitBtn);
                        submitBtn.click();
                        orderForm.removeChild(submitBtn);
                    }
                });
            });
        });

        // Helper function to format cooking time (minutes to hours if needed)
        function formatCookingTime(minutes) {
            if (minutes >= 60) {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                return `${hours} hour${hours !== 1 ? 's' : ''}${remainingMinutes > 0 ? ` ${remainingMinutes} minute${remainingMinutes !== 1 ? 's' : ''}` : ''}`;
            }
            return `${minutes} minute${minutes !== 1 ? 's' : ''}`;
        }

        // Order status checking
        function checkOrderStatus() {
            if (!orderId) return;

            fetch(`check_status.php?order_id=${orderId}`)
                .then(res => res.text())
                .then(status => {
                    if (status !== currentStatus) {
                        currentStatus = status;

                        if (status === 'pending') {
                            showModal();
                            readyNotificationShown = false;
                        } else if (status === 'ready' && !readyNotificationShown) {
                            readyNotificationShown = true;
                            
                            // Show the ready notification
                            const statusIndicator = document.getElementById('statusIndicator');
                            if (statusIndicator) {
                                statusIndicator.classList.add('ready-notification');
                                statusIndicator.innerHTML = `
                                    <div class="status-dot status-ready"></div>
                                    <span>Your order is on its way 🚚!</span>
                                `;
                            }
                            
                            // Show SweetAlert notification
                            Swal.fire({
                                title: 'Your Order Is Ready To Eat!',
                                text: 'Bon Appétit',
                                icon: 'success',
                                timer: 10000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                willClose: () => {
                                    // Refresh after notification closes
                                    window.location.href = 'order.php';
                                }
                            });
                            
                            hideModal();
                        } else if (status === 'deleted') {
                            hideModal();
                            document.getElementById('timerContainer').style.display = 'none';
                        }
                    }
                })
                .catch(err => console.error('Error checking order status:', err));
        }

        // Check status every second
        setInterval(checkOrderStatus, 1000);
        checkOrderStatus();

        // Function to clear all selections across all categories
        function clearAllSelections() {
            const checkboxes = document.querySelectorAll('.item-checkbox input[type="checkbox"]:checked');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                const itemId = checkbox.id.replace('item_', '');
                const quantityInput = document.getElementById(`quantity_${itemId}`);
                quantityInput.disabled = true;
                quantityInput.value = 1;
            });
            
            updateOrderSummary();
            
            Swal.fire({
                icon: 'success',
                title: 'All Selections Cleared',
                text: 'All selected items have been removed',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Menu selection functions
        document.querySelectorAll('.item-checkbox input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const itemId = this.id.replace('item_', '');
                const quantityInput = document.getElementById(`quantity_${itemId}`);
                quantityInput.disabled = !this.checked;
                
                if (!this.checked) {
                    quantityInput.value = 1;
                }
                
                updateOrderSummary();
            });
        });

        function updateOrderSummary() {
            const checkedItems = document.querySelectorAll('.item-checkbox input[type="checkbox"]:checked');
            let totalItems = 0;

            checkedItems.forEach(checkbox => {
                const itemId = checkbox.id.replace('item_', '');
                const quantityInput = document.getElementById(`quantity_${itemId}`);
                totalItems += parseInt(quantityInput.value) || 1;
            });

            document.getElementById('selectedCount').textContent = totalItems;

            if (checkedItems.length > 0) {
                document.getElementById('orderSummary').style.display = 'flex';
                document.querySelectorAll('.place-order-btn').forEach(btn => {
                    btn.disabled = false;
                });
            } else {
                document.getElementById('orderSummary').style.display = 'none';
                document.querySelectorAll('.place-order-btn').forEach(btn => {
                    btn.disabled = true;
                });
            }
        }

        // Order summary click handler
        document.getElementById('orderSummary').addEventListener('click', () => {
            const checkedItems = Array.from(document.querySelectorAll('.item-checkbox input[type="checkbox"]:checked'))
                .map(checkbox => {
                    const itemId = checkbox.id.replace('item_', '');
                    const quantity = document.getElementById(`quantity_${itemId}`).value;
                    const itemName = checkbox.closest('.item-details').querySelector('.item-name span').textContent;
                    const itemPrice = checkbox.closest('.item-details').querySelector('.item-price').textContent;
                    const cookingTime = checkbox.closest('.item-details').querySelector('.cooking-time').textContent;
                    const total = (parseFloat(itemPrice) * parseInt(quantity)).toFixed(2);
                    return `${itemName} (x${quantity}) - ${total} ETB (${cookingTime})`;
                })
                .join('\n• ');

            Swal.fire({
                title: 'Your Order Summary',
                html: checkedItems ? `<p>• ${checkedItems}</p>` : '<p>No items selected</p>',
                icon: 'info',
                confirmButtonText: 'Continue Ordering',
                footer: checkedItems ? '<b>Click "Place Order" when ready</b>' : ''
            });
        });

        // Quantity input validation
        document.querySelectorAll('.item-quantity input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                updateOrderSummary();
            });
        });

        // Initialize the timer if there's a pending order
        if (orderId && <?= isset($_SESSION['last_status']) && $_SESSION['last_status'] === 'pending' ? 'true' : 'false' ?>) {
            startTimer();
        }
    </script>
</body>
</html>