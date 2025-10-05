<?php
header("Content-Type: text/plain");

// Read JSON input from JS
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data["name"]) || !isset($data["cart"])) {
    http_response_code(400);
    echo "Invalid order data.";
    exit;
}

// Create a directory for orders if it doesn’t exist
if (!file_exists("orders")) {
    mkdir("orders", 0777, true);
}

$order = [
    "name" => htmlspecialchars($data["name"]),
    "mobile" => htmlspecialchars($data["mobile"]),
    "email" => htmlspecialchars($data["email"]),
    "address" => htmlspecialchars($data["address"]),
    "pincode" => htmlspecialchars($data["pincode"]),
    "city" => htmlspecialchars($data["city"]),
    "state" => htmlspecialchars($data["state"]),
    "cart" => $data["cart"],
    "timestamp" => date("Y-m-d H:i:s")
];

// Save to a single JSON file (append mode)
$file = "orders/orders.json";
$existing = [];

if (file_exists($file)) {
    $existing = json_decode(file_get_contents($file), true);
    if (!is_array($existing)) $existing = [];
}

$existing[] = $order;
file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));

echo "Order saved successfully!";
?>
