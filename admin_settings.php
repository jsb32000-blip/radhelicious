<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
  header("Location: admin_login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Settings</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="max-w-md mx-auto mt-20 bg-white shadow-lg rounded p-6">
  <h2 class="text-xl font-bold mb-4 text-center">Change Admin Password</h2>
  <form action="change_password.php" method="POST">
    <input type="password" name="current" placeholder="Current Password" class="border p-2 w-full mb-3 rounded" required>
    <input type="password" name="new" placeholder="New Password" class="border p-2 w-full mb-3 rounded" required>
    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Update Password</button>
  </form>
</div>
</body>
</html>
