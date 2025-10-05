<?php
session_start();

if (!isset($_SESSION["admin_logged_in"])) {
  header("Location: admin_login.html");
  exit;
}

$stored = json_decode(file_get_contents("admin_credentials.json"), true);
$current = $_POST["current"];
$new = $_POST["new"];

if (!password_verify($current, $stored["password"])) {
  echo "<script>alert('Current password is incorrect'); window.location='admin_settings.php';</script>";
  exit;
}

$stored["password"] = password_hash($new, PASSWORD_DEFAULT);
file_put_contents("admin_credentials.json", json_encode($stored));

echo "<script>alert('Password changed successfully!'); window.location='admin.php';</script>";
?>
