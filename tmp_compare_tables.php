<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "subscription_plans ID 6:\n";
$res = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE id = 6");
print_r(mysqli_fetch_assoc($res));

echo "\ndoctor_subscription_plans ID 6:\n";
$res = mysqli_query($conn, "SELECT * FROM doctor_subscription_plans WHERE id = 6");
print_r(mysqli_fetch_assoc($res));

mysqli_close($conn);
