<?php
$conn = new mysqli("localhost", "root", "", "smart_kitchen");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status);
        $stmt->fetch();
        echo $status;
    } else {
        echo "deleted"; // Order was removed from DB
    }
    $stmt->close();
}

$conn->close();
?>
