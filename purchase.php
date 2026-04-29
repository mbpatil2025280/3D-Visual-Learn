<?php
include 'db.php';

if (isset($_GET['course'])) {
    $course = $conn->real_escape_string($_GET['course']);
    
    // Using a dummy user_id=1 for now since proper session login isn't maintained
    $user_id = 1; 

    $sql = "INSERT INTO purchases (user_id, course) VALUES ('$user_id', '$course')";
    
    if ($conn->query($sql) === TRUE) {
        // Convert '5th_standard' to '5th standard.html' allowing us to redirect to the actual lesson!
        $redirectFile = str_replace('_', ' ', $course) . ".html";
        
        $displayName = ucwords(str_replace('_', ' ', $course));
        echo "<script>alert('Thank you! $displayName Subscription Purchased Successfully! Enjoy your new 3D lessons.'); window.location.href='$redirectFile';</script>";
    } else {
        echo "<script>alert('Error processing purchase.'); window.location.href='dashboard.html';</script>";
    }
} else {
    header("Location: dashboard.html");
}
?>