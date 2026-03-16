<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "--- Doctor Subscriptions ---\n";
$sql = "SELECT id, doctor_id, doctor_subscription_plan_id, status, autopay_agreement_id, featured_status, end_at FROM doctor_subscriptions ORDER BY id DESC LIMIT 5";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n--- Subscription Plans ---\n";
$sql = "SELECT id, name, plan_type, price, is_active FROM subscription_plans";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
