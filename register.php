<?php
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='register.html';</script>";
    exit();
}

// Check if user already exists
$check_sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo "<script>alert('Email is already registered! Please login.'); window.location.href='index.html';</script>";
} else {
    $insert_sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "<script>alert('Registration Successful! Please login to continue.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error registering user.'); window.location.href='register.html';</script>";
    }
}
?>
