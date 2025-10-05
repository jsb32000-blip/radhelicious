<?php
header("Content-Type: application/json");
$file = "orders/orders.json";

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$orders = json_decode(file_get_contents($file), true);
if (!$orders) $orders = [];

echo json_encode($orders);
?>
