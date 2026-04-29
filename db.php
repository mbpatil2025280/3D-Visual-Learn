<?php
$conn = new mysqli("localhost", "root", "", "visual_learning");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>