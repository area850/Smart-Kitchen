<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smart_kitchen");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create waiters table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS waiters_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    waiter_id INT,
    status ENUM('pending', 'ready', 'served') DEFAULT 'pending',
    served_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)");

// Handle actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mark_served'])) {
        $order_id = intval($_POST['order_id']);
        $waiter_id = 1; // In real app, get from session
        
        $stmt = $conn->prepare("INSERT INTO waiters_orders (order_id, waiter_id, status, served_at) 
                               VALUES (?, ?, 'served', NOW())
                               ON DUPLICATE KEY UPDATE 
                               status='served', served_at=NOW()");
        $stmt->bind_param("ii", $order_id, $waiter_id);
        $stmt->execute();
        
        $_SESSION['notification'] = "Order #$order_id marked as served!";
        
        // Return JSON response for AJAX
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
        
        header("Location: waiters.php");
        exit();
    }
}

// Fetch orders
$sql = "SELECT o.id, o.items, o.status, o.created_at, 
               fo.total_price, wo.status AS waiter_status
        FROM orders o
        LEFT JOIN finance_orders fo ON o.id = fo.order_id
        LEFT JOIN waiters_orders wo ON o.id = wo.order_id
        WHERE o.status = 'ready' OR (wo.status = 'served' AND wo.served_at > DATE_SUB(NOW(), INTERVAL 1 HOUR))
        ORDER BY 
            CASE WHEN o.status = 'ready' AND (wo.status IS NULL OR wo.status != 'served') THEN 1 ELSE 2 END,
            o.created_at DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$ready_count = count(array_filter($orders, function($order) {
    return $order['status'] === 'ready' && 
          (empty($order['waiter_status']) || $order['waiter_status'] !== 'served');
}));

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiter's Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --served: #9b59b6;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --bg-light: #f8f9fa;
            --border: #dee2e6;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--dark);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-header {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            border-left: 5px solid var(--secondary);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                var(--secondary) 0%, 
                var(--success) 50%, 
                var(--danger) 100%);
            animation: rainbow 8s linear infinite;
            background-size: 200% auto;
        }

        @keyframes rainbow {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-title h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .header-icon {
            font-size: 2rem;
            color: var(--secondary);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--primary);
            transition: var(--transition);
        }

        .notification-bell:hover {
            transform: rotate(15deg) scale(1.1);
            color: var(--danger);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
        }

        .orders-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        .orders-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr {
            transition: var(--transition);
        }

        .orders-table tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .orders-table tr.new-order {
            animation: highlightRow 2s infinite;
            position: relative;
        }

        .orders-table tr.new-order::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--success);
        }

        @keyframes highlightRow {
            0% { background-color: rgba(46, 204, 113, 0.05); }
            50% { background-color: rgba(46, 204, 113, 0.15); }
            100% { background-color: rgba(46, 204, 113, 0.05); }
        }

        .order-id {
            font-weight: 700;
            color: var(--primary);
            font-family: 'Fira Code', monospace;
        }

        .order-items {
            max-width: 300px;
        }

        .order-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .order-item-name {
            flex: 1;
        }

        .order-item-qty {
            background-color: var(--secondary);
            color: white;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-left: 8px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            gap: 5px;
        }

        .status-ready {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            animation: pulseStatus 1.5s infinite;
        }

        .status-served {
            background: linear-gradient(135deg, var(--served), #8e44ad);
            color: white;
        }

        @keyframes pulseStatus {
            0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-serve {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            box-shadow: 0 2px 5px rgba(46, 204, 113, 0.3);
        }

        .btn-serve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(46, 204, 113, 0.4);
        }

        .btn-serve:active {
            transform: translateY(0);
        }

        /* Notification Alert */
        .notification-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(46, 204, 113, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(200%);
            transition: var(--transition);
            border-left: 4px solid white;
        }

        .notification-alert.show {
            transform: translateX(0);
        }

        .notification-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }

        .notification-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }

        /* Flash Message */
        .flash-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            animation: slideIn 0.5s forwards, fadeOut 0.5s 3s forwards;
            border-left: 4px solid white;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Sound Controls */
        .sound-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .sound-btn {
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            color: var(--primary);
        }

        .sound-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .container {
                padding: 15px;
            }
            
            .orders-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .orders-table th, 
            .orders-table td {
                padding: 12px 10px;
            }
            
            .action-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Audio element -->
    <audio id="notificationSound" preload="auto">
        <source src="notification.mp3" type="audio/mpeg">
    </audio>

    <!-- Sound Controls -->
    <div class="sound-controls">
        <button class="sound-btn" id="muteBtn" title="Toggle sound">
            <i class="fas fa-volume-up"></i>
        </button>
        <button class="sound-btn" id="testSoundBtn" title="Test notification sound">
            <i class="fas fa-bell"></i>
        </button>
    </div>

    <div class="container">
        <div class="dashboard-header">
            <div class="header-title">
                <i class="fas fa-concierge-bell header-icon"></i>
                <h1>Waiter's Dashboard</h1>
            </div>
            <div class="notification-bell" id="notificationBell">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notificationBadge"><?= $ready_count ?></span>
            </div>
        </div>

        <div class="orders-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): 
                        $is_served = !empty($order['waiter_status']) && $order['waiter_status'] === 'served';
                        $is_new = !$is_served && $order['status'] === 'ready';
                    ?>
                        <tr class="<?= $is_new ? 'new-order' : '' ?>" id="order-<?= $order['id'] ?>">
                            <td class="order-id">#<?= htmlspecialchars($order['id']) ?></td>
                            <td class="order-items">
                                <?php 
                                $items = explode(", ", $order['items']);
                                foreach ($items as $item) {
                                    if (preg_match('/(.*)\s\(x(\d+)\)/', $item, $matches)) {
                                        echo '<div class="order-item">
                                                <span class="order-item-name">'.htmlspecialchars($matches[1]).'</span>
                                                <span class="order-item-qty">x'.htmlspecialchars($matches[2]).'</span>
                                              </div>';
                                    } else {
                                        echo '<div class="order-item">'.htmlspecialchars($item).'</div>';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($is_served): ?>
                                    <span class="status-badge status-served">
                                        <i class="fas fa-check-circle"></i> Served
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-ready">
                                        <i class="fas fa-clock"></i> Ready
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= number_format($order['total_price'] ?? 0, 2) ?> ETB</td>
                            <td>
                                <?php if ($is_new): ?>
                                    <button type="button" class="action-btn btn-serve" onclick="markAsServed(<?= $order['id'] ?>)">
                                        <i class="fas fa-check-circle"></i> Served
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Order Notification -->
    <div class="notification-alert" id="notificationAlert">
        <i class="fas fa-utensils"></i>
        <span id="alertMessage">New order ready to serve!</span>
        <div class="notification-controls">
            <button class="notification-btn" id="stopSoundBtn" title="Stop sound">
                <i class="fas fa-volume-mute"></i>
            </button>
            <button class="notification-btn" id="dismissAlertBtn" title="Dismiss">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['notification'])): ?>
        <div class="flash-message">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['notification'] ?></span>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

    <script>
        // DOM Elements
        const notificationSound = document.getElementById('notificationSound');
        const muteBtn = document.getElementById('muteBtn');
        const testSoundBtn = document.getElementById('testSoundBtn');
        const stopSoundBtn = document.getElementById('stopSoundBtn');
        const dismissAlertBtn = document.getElementById('dismissAlertBtn');
        const notificationAlert = document.getElementById('notificationAlert');
        
        // State
        let isMuted = false;
        let isSoundEnabled = false;
        let notificationTimeout;
        let lastReadyCount = <?= $ready_count ?>;
        
        // Initialize sound (muted until user interaction)
        notificationSound.volume = 0;
        
        // Enable sound on first interaction
        function enableSound() {
            if (!isSoundEnabled) {
                isSoundEnabled = true;
                notificationSound.volume = 0.3;
                muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                isMuted = false;
            }
        }
        
        // Toggle mute state
        function toggleMute() {
            isMuted = !isMuted;
            notificationSound.volume = isMuted ? 0 : 0.3;
            muteBtn.innerHTML = isMuted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
        }
        
        // Stop sound immediately
        function stopSound() {
            notificationSound.pause();
            notificationSound.currentTime = 0;
        }
        
        // Test notification sound
        function testSound() {
            if (isSoundEnabled && !isMuted) {
                notificationSound.currentTime = 0;
                notificationSound.play().catch(e => console.log('Test sound failed:', e));
            }
        }
        
        // Dismiss notification alert
        function dismissAlert() {
            notificationAlert.classList.remove('show');
            clearTimeout(notificationTimeout);
            stopSound();
        }
        
        // Mark order as served (AJAX)
        function markAsServed(orderId) {
            fetch('waiters.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `mark_served=1&order_id=${orderId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI immediately
                    const row = document.getElementById(`order-${orderId}`);
                    if (row) {
                        // Update status cell
                        const statusCell = row.querySelector('td:nth-child(3)');
                        statusCell.innerHTML = `
                            <span class="status-badge status-served">
                                <i class="fas fa-check-circle"></i> Served
                            </span>
                        `;
                        
                        // Remove action button
                        const actionCell = row.querySelector('td:last-child');
                        actionCell.innerHTML = '';
                        
                        // Remove new-order class
                        row.classList.remove('new-order');
                        
                        // Update notification badge
                        const badge = document.getElementById('notificationBadge');
                        if (badge) {
                            badge.textContent = parseInt(badge.textContent) - 1;
                            lastReadyCount--;
                        }
                    }
                    
                    // Show confirmation
                    showFlashMessage(`Order #${orderId} marked as served!`);
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Show flash message
        function showFlashMessage(message) {
            const flash = document.createElement('div');
            flash.className = 'flash-message';
            flash.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(flash);
            
            // Remove after animation
            setTimeout(() => {
                flash.remove();
            }, 3500);
        }
        
        // Check for new orders
        function checkForNewOrders() {
            fetch('check_new_orders.php')
                .then(response => response.json())
                .then(data => {
                    if (data.newReadyCount > lastReadyCount) {
                        const newOrders = data.newReadyCount - lastReadyCount;
                        showNewOrderNotification(newOrders);
                        lastReadyCount = data.newReadyCount;
                        document.getElementById('notificationBadge').textContent = data.newReadyCount;
                        
                        // Play sound if enabled
                        if (isSoundEnabled && !isMuted) {
                            notificationSound.currentTime = 0;
                            notificationSound.play().catch(e => console.log('Play failed:', e));
                        }
                        
                        // Refresh to show new orders
                        setTimeout(() => {
                            window.location.reload();
                        }, 20000);
                    }
                })
                .catch(error => console.error('Error checking orders:', error));
        }
        
        // Show new order notification
        function showNewOrderNotification(count) {
            const message = document.getElementById('alertMessage');
            message.textContent = count === 1 ? 
                '1 new order ready to serve!' : 
                `${count} new orders ready to serve!`;
            
            // Show alert
            notificationAlert.classList.add('show');
            
            // Auto-hide after 5 seconds
            clearTimeout(notificationTimeout);
            notificationTimeout = setTimeout(dismissAlert, 5000);
        }
        
        // Event Listeners
        document.addEventListener('click', enableSound, { once: true });
        muteBtn.addEventListener('click', toggleMute);
        testSoundBtn.addEventListener('click', testSound);
        stopSoundBtn.addEventListener('click', stopSound);
        dismissAlertBtn.addEventListener('click', dismissAlert);
        
        // Check for new orders every 5 seconds
        setInterval(checkForNewOrders, 5000);
        
        // Initial check
        checkForNewOrders();
        
        // Highlight new orders on page load
        document.addEventListener('DOMContentLoaded', () => {
            const newOrders = document.querySelectorAll('.new-order');
            if (newOrders.length > 0) {
                // Scroll to first new order
                newOrders[0].scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest',
                    inline: 'start'
                });
            }
        });
    </script>
</body>
</html>