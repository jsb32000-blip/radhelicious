<?php
header("Content-Type: text/plain");
$file = "orders/orders.json";

if (!file_exists($file)) { echo "No orders found."; exit; }

$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input["index"])) { echo "Invalid input."; exit; }

$orders = json_decode(file_get_contents($file), true);
$index = (int)$input["index"];

if (!isset($orders[$index])) { echo "Order not found."; exit; }

if (($input["status"] ?? "") === "delete") {
  array_splice($orders, $index, 1);
  file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT));
  echo "Order deleted.";
  exit;
}

$orders[$index]["status"] = $input["status"];
file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT));
echo "Order updated.";
?>
