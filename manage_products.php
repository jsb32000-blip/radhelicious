<?php
header("Content-Type: text/plain");
$file = "products.json";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

if (!file_exists($file)) file_put_contents($file, "[]");
$products = json_decode(file_get_contents($file), true);
if (!is_array($products)) $products = [];

switch ($action) {
  case "add":
    $new = [
      "id" => time(),
      "name" => htmlspecialchars($input["name"]),
      "price" => (int)$input["price"],
      "image" => htmlspecialchars($input["image"])
    ];
    $products[] = $new;
    file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT));
    echo "Product added.";
    break;

  case "edit":
    foreach ($products as &$p) {
      if ($p["id"] == $input["id"]) {
        $p["name"] = htmlspecialchars($input["name"]);
        $p["price"] = (int)$input["price"];
        $p["image"] = htmlspecialchars($input["image"]);
        break;
      }
    }
    file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT));
    echo "Product updated.";
    break;

  case "delete":
    $products = array_filter($products, fn($p) => $p["id"] != $input["id"]);
    file_put_contents($file, json_encode(array_values($products), JSON_PRETTY_PRINT));
    echo "Product deleted.";
    break;

  default:
    echo "Invalid action.";
}
?>
