<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "doctor_subscription_plan_id values in doctor_subscriptions table:\n";
$res = mysqli_query($conn, "SELECT DISTINCT doctor_subscription_plan_id FROM doctor_subscriptions");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['doctor_subscription_plan_id'] . "\n";
    }
}

echo "\nIDs in doctor_subscription_plans table:\n";
$res = mysqli_query($conn, "SELECT id, name FROM doctor_subscription_plans");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['id'] . " - " . $row['name'] . "\n";
    }
}

echo "\nIDs in subscription_plans (type=doctor) table:\n";
$res = mysqli_query($conn, "SELECT id, name FROM subscription_plans WHERE plan_type='doctor'");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['id'] . " - " . $row['name'] . "\n";
    }
}

mysqli_close($conn);
?>
