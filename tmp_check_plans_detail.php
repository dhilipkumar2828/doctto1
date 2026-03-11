<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$data = [];

echo "--- subscription_plans ---\n";
$sql = "SELECT id, name, plan_type, price, is_active FROM subscription_plans";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n--- doctor_subscription_plans ---\n";
$sql = "SELECT id, name, price, is_active FROM doctor_subscription_plans";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
