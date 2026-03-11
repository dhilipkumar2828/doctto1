<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "subscription_plans ID 1:\n";
$res = mysqli_query($conn, "SELECT name, plan_type, max_doctors_allowed FROM subscription_plans WHERE id = 1");
print_r(mysqli_fetch_assoc($res));

mysqli_close($conn);
