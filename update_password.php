<?php
session_start();

if (!isset($_SESSION["otp"]) || !isset($_SESSION["otp_email"])) {
  echo "<script>alert('Session expired. Try again.'); window.location='forgot_password.html';</script>";
  exit;
}

$otp = $_POST["otp"];
$new_pass = $_POST["new_pass"];

if ($otp != $_SESSION["otp"]) {
  echo "<script>alert('Invalid OTP'); window.location='verify_otp.php';</script>";
  exit;
}

// Save new password securely (hashed)
$new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
file_put_contents("admin_credentials.json", json_encode([
  "username" => "admin",
  "password" => $new_hash
]));

unset($_SESSION["otp"], $_SESSION["otp_email"]);

echo "<script>alert('Password updated successfully!'); window.location='admin_login.html';</script>";
?>
