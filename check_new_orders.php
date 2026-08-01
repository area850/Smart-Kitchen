<?php
$conn = new mysqli("localhost", "root", "", "smart_kitchen2");
$count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'ready'")->fetch_assoc()['count'];
echo json_encode(['newReadyCount' => $count]);
$conn->close();
?>