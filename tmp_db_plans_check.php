<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Records from subscription_plans:\n";
$res = mysqli_query($conn, "SELECT id, name, plan_type FROM subscription_plans");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}

echo "\nRecords from doctor_subscription_plans:\n";
$res = mysqli_query($conn, "SELECT id, name FROM doctor_subscription_plans");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}

mysqli_close($conn);
?>
