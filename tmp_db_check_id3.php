<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Record ID 3 from doctor_subscriptions:\n";
$res = mysqli_query($conn, "SELECT * FROM doctor_subscriptions WHERE id = 3");
if ($res) {
    print_r(mysqli_fetch_assoc($res));
}

echo "\nRecord ID 3 from user_subscriptions:\n";
$res = mysqli_query($conn, "SELECT * FROM user_subscriptions WHERE id = 3");
if ($res) {
    print_r(mysqli_fetch_assoc($res));
}

mysqli_close($conn);
?>
