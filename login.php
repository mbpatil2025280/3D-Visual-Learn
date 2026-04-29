<?php
session_start();
include 'db.php';

$email    = $_POST['email'];
$password = $_POST['password'];

$sql    = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();

  // Store user info in session
  $_SESSION['user_id']    = $user['id'];
  $_SESSION['user_email'] = $user['email'];

  header("Location: dashboard.php");
  exit();
} else {
  echo "<script>alert('Invalid Login! Please check your credentials and try again.'); window.location.href='index.html';</script>";
}
?>