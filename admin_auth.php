<?php
session_start();

$stored = json_decode(@file_get_contents("admin_credentials.json"), true);

if (!$stored) {
  // default fallback
  $stored = ["username" => "admin", "password" => password_hash("12345", PASSWORD_DEFAULT)];
  file_put_contents("admin_credentials.json", json_encode($stored));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user = $_POST["username"];
  $pass = $_POST["password"];

  if ($user === $stored["username"] && password_verify($pass, $stored["password"])) {
    $_SESSION["admin_logged_in"] = true;
    header("Location: admin.html");
  } else {
    echo "<script>alert('Invalid username or password'); window.location='admin_login.html';</script>";
  }
}
?>

