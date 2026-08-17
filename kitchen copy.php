<?php
$conn = new mysqli("localhost", "root", "", "smart_kitchen");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $sql = "UPDATE orders SET status='ready' WHERE id=$order_id";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
    exit;
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "status='pending'";
if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $where_clause .= " AND (items LIKE '%$search_term%' OR ing_1 LIKE '%$search_term%' OR ing_2 LIKE '%$search_term%')";
}

// Fetch pending orders
$sql = "SELECT * FROM orders WHERE $where_clause ORDER BY id ASC";
$result = $conn->query($sql);
?>
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "smart_kitchen");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("UPDATE orders SET status = 'ready' WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        // Optional: update session or notify
        $_SESSION['last_status'] = 'ready';
        echo "<script>alert('Order #$order_id marked as ready.'); window.location='chef_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update status.'); window.location='chef_dashboard.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard | NeuovaCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-color: #1a2a3a;
            --secondary-color: #ff6b6b;
            --accent-color: #ff9e2c;
            --success-color: #4cd964;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #333333;
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --gradient-primary: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            --gradient-accent: linear-gradient(135deg, #ff6b6b 0%, #ff9e2c 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
            padding: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.05) 1px, transparent 0);
            background-size: 20px 20px;
        }

        .header {
            background: var(--gradient-primary);
            color: var(--text-light);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.03)" d="M0,0 L100,0 L100,100 L0,100 Z"></path></svg>');
            background-size: cover;
            pointer-events: none;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .header h1 i {
            color: var(--accent-color);
            text-shadow: 0 0 8px rgba(255,158,44,0.4);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            position: relative;
        }

        .search-container {
            position: relative;
            min-width: 250px;
        }

        .search-input {
            padding: 0.6rem 1rem 0.6rem 2.8rem;
            border-radius: 30px;
            border: none;
            width: 100%;
            font-family: inherit;
            transition: var(--transition);
            background-color: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-input:focus {
            outline: none;
            background-color: rgba(255,255,255,0.25);
            box-shadow: 0 0 0 3px rgba(255,255,255,0.2);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.8);
        }

        .refresh-btn {
            background: var(--gradient-accent);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            white-space: nowrap;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3);
        }

        .refresh-btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 20px rgba(255,107,107,0.4);
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 2rem;
        }

        .order-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            border-left: 5px solid var(--secondary-color);
            position: relative;
            animation: fadeInUp 0.5s ease-out;
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            pointer-events: none;
        }

        .order-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .order-header {
            background: var(--gradient-primary);
            color: var(--text-light);
            padding: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .order-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-accent);
        }

        .order-id {
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--accent-color);
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .order-time {
            font-size: 0.9rem;
            opacity: 0.9;
            background: rgba(0,0,0,0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
        }

        .order-body {
            padding: 1.8rem;
        }

        .order-items {
            margin-bottom: 1.2rem;
        }

        .order-item {
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px dashed #eee;
            position: relative;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-ingredients {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 0.8rem;
            font-style: italic;
            background: rgba(0,0,0,0.02);
            padding: 0.5rem;
            border-radius: 6px;
            display: inline-block;
        }

        .order-quantity {
            display: inline-block;
            background: var(--gradient-accent);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 8px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .order-actions {
            display: flex;
            justify-content: flex-end;
            padding: 1.2rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            background: rgba(0,0,0,0.02);
        }

        .ready-btn {
            background: var(--gradient-accent);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .ready-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(255,107,107,0.4);
        }

        .ready-btn i {
            transition: transform 0.3s ease;
        }

        .ready-btn:hover i {
            transform: rotate(15deg);
        }

        .no-orders {
            text-align: center;
            padding: 4rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            grid-column: 1 / -1;
            animation: fadeIn 0.8s ease-out;
            border: 1px dashed rgba(0,0,0,0.1);
        }

        .no-orders i {
            font-size: 4rem;
            color: #dfe6e9;
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .no-orders p {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .search-results-info {
            grid-column: 1 / -1;
            text-align: center;
            padding: 1.5rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
            animation: fadeInDown 0.5s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .clear-search {
            color: var(--secondary-color);
            text-decoration: none;
            margin-left: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: inline-block;
        }

        .clear-search:hover {
            text-decoration: underline;
            transform: translateX(3px);
        }

        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Floating animation for header icon */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }

        .header h1 i {
            animation: float 3s ease-in-out infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .orders-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem 1rem;
            }
            
            .header-actions {
                width: 100%;
                flex-direction: column;
            }
            
            .search-container {
                width: 100%;
                margin-bottom: 1rem;
            }

            .order-card {
                animation: fadeIn 0.5s ease-out;
            }
        }

        /* Dark mode toggle (optional) */
        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 100;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(30deg);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-utensils pulse"></i> Kitchen Dashboard</h1>
        <div class="header-actions">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <form method="GET" action="">
                    <input type="text" name="search" class="search-input" placeholder="Search orders..." value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>
            <button class="refresh-btn" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <div class="container">
        <div class="orders-grid">
            <?php if (!empty($search)): ?>
                <div class="search-results-info">
                    Showing results for: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                    <a class="clear-search" href="?">Clear search</a>
                </div>
            <?php endif; ?>
            
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="order-card" id="order-<?= $row['id'] ?>">
                        <div class="order-header">
                            <span class="order-id">Order #<?= $row['id'] ?></span>
                            <span class="order-time"><?= date('l, j, Y') ?></span>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <div class="order-item">
                                    <?php
                                    // Parse items to display quantities
                                    $items = explode(", ", $row['items']);
                                    foreach ($items as $item) {
                                        // Check for quantity notation (x3)
                                        if (preg_match('/(.*)\s\(x(\d+)\)$/', $item, $matches)) {
                                            echo htmlspecialchars($matches[1]) . 
                                                 '<span class="order-quantity">x' . 
                                                 htmlspecialchars($matches[2]) . '</span>';
                                        } else {
                                            echo htmlspecialchars($item);
                                        }
                                        echo "<br>";
                                    }
                                    ?>
                                    <div class="order-ingredients">
                                        <?= htmlspecialchars($row['ing_1']) ?>, <?= htmlspecialchars($row['ing_2']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="order-actions">
                    <button class="ready-btn" onclick="markAsReady(<?= $row['id'] ?>)">
                        <i class="fas fa-check"></i> Mark as Ready
                    </button>
                </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-search"></i>
                    <p><?= empty($search) ? 'No pending orders at the moment' : 'No orders found matching your search' ?></p>
                    <?php if (!empty($search)): ?>
                        <a class="clear-search" href="?">Show all orders</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Optional theme toggle button -->
    <div class="theme-toggle" title="Toggle theme">
        <i class="fas fa-moon"></i>
    </div>

    <script>
        function markAsReady(orderId) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&order_id=' + orderId
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    const orderElement = document.getElementById('order-' + orderId);
                    orderElement.style.opacity = '0.5';
                    orderElement.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        orderElement.classList.add('animate__animated', 'animate__fadeOut');
                        setTimeout(() => {
                            orderElement.remove();
                            checkEmptyOrders();
                        }, 500);
                    }, 300);
                } else {
                    alert('Error: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function checkEmptyOrders() {
            const ordersGrid = document.querySelector('.orders-grid');
            if (ordersGrid.children.length === 0 || 
                (ordersGrid.children.length === 1 && ordersGrid.querySelector('.no-orders'))) {
                ordersGrid.innerHTML = `
                    <div class="no-orders animate__animated animate__fadeIn">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending orders at the moment</p>
                    </div>
                `;
            }
        }

        // Auto-submit search form when typing stops
        const searchInput = document.querySelector('.search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        // Optional: Theme toggle functionality 
        const themeToggle = document.querySelector('.theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const icon = themeToggle.querySelector('i');
            if (document.body.classList.contains('dark-theme')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    </script>
</body>
</html>
