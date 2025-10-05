<?php
// invoice.php - Amazon-style printable invoice
header("Content-Type: text/html");

$file = "orders/orders.json";
if (!file_exists($file)) {
  echo "No orders found.";
  exit;
}

$orders = json_decode(file_get_contents($file), true);
$index = isset($_GET['index']) ? intval($_GET['index']) : -1;

if ($index < 0 || $index >= count($orders)) {
  echo "Invalid order index.";
  exit;
}

$order = $orders[$index];
$total = 0;
foreach ($order['cart'] as $item) {
  $total += $item['price'] * $item['qty'];
}

// Generate unique Order ID (timestamp + index)
$orderId = "ORD-" . date("YmdHis", strtotime($order['timestamp'])) . "-$index";

// GST details (for example: 18%)
$gstRate = 18;
$subtotal = $total / (1 + $gstRate / 100);
$gstAmount = $total - $subtotal;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice - <?php echo htmlspecialchars($order['name']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
body { margin: 40px; font-family: Arial, sans-serif; background: #f8f9fa; }
.invoice {
  max-width: 800px;
  margin: auto;
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.15);
}
h1, h2, h3 { margin: 0; }
.header {
  display: flex; justify-content: space-between; align-items: center;
  border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px;
}
.logo { width: 120px; }
.info-table td { padding: 4px 0; }
.table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.table th, .table td {
  border: 1px solid #ddd; padding: 8px; text-align: left;
}
.table th { background: #f3f4f6; }
.total-section {
  margin-top: 20px; text-align: right;
}
.print-btn {
  margin-top: 30px;
  background: #2563eb;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
}
@media print {
  .print-btn { display: none; }
  body { background: white; }
}
</style>
</head>
<body>
<div class="invoice">
  <div class="header">
    <div>
      <h1 class="text-2xl font-semibold">Radhe Liciouse</h1>
      <p class="text-sm text-gray-600">dsjdskjfkdsjfk<br></p>
    </div>
    <img src="images/logo.png" alt="Shop Logo" class="logo" onerror="this.style.display='none'">
  </div>

  <h2 class="text-lg font-semibold mb-2">Invoice</h2>
  <table class="info-table text-sm mb-3">
    <tr>
      <td><strong>Order ID:</strong> <?php echo $orderId; ?></td>
      <td><strong>Date:</strong> <?php echo htmlspecialchars($order['timestamp']); ?></td>
    </tr>
    <tr>
      <td><strong>Status:</strong> <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?></td>
      <td><strong>Payment Mode:</strong> Cash on Delivery</td>
    </tr>
  </table>

  <div class="grid grid-cols-2 gap-4 mb-4">
    <div>
      <h3 class="font-semibold">Billing To:</h3>
      <p>
        <?php echo htmlspecialchars($order['name']); ?><br>
        <?php echo htmlspecialchars($order['address']); ?><br>
        <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?><br>
        <?php echo htmlspecialchars($order['mobile']); ?><br>
        <?php echo htmlspecialchars($order['email']); ?>
      </p>
    </div>
    <div>
      <h3 class="font-semibold">Seller:</h3>
      <p>
        Radhe Licious<br>
        Abohar<br>
        Email: support@radhelicious.com
      </p>
    </div>
  </div>

  <table class="table text-sm">
    <thead>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($order['cart'] as $item): ?>
        <tr>
          <td><?php echo htmlspecialchars($item['name']); ?></td>
          <td><?php echo htmlspecialchars($item['qty']); ?></td>
          <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
          <td>₹<?php echo htmlspecialchars($item['price'] * $item['qty']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="total-section">
    <p>Subtotal: ₹<?php echo number_format($subtotal, 2); ?></p>
    <p>GST (<?php echo $gstRate; ?>%): ₹<?php echo number_format($gstAmount, 2); ?></p>
    <h3 class="text-lg font-semibold mt-2">Total: ₹<?php echo number_format($total, 2); ?></h3>
  </div>

  <p class="text-xs text-gray-500 mt-4">Thank you for shopping with us! This is a system-generated invoice.</p>

  <button class="print-btn" onclick="window.print()">🖨 Print / Save as PDF</button>
</div>
</body>
</html>
