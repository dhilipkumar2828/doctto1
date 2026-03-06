<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Fail");

$r = mysqli_query($conn, "SELECT id, doctor_subscription_plan_id FROM doctor_subscriptions WHERE id IN (1,2,3,4,5,6,7,8,9,10)");
while($row = mysqli_fetch_assoc($r)) {
    echo "Subscription ID: " . $row['id'] . ", Plan ID: " . $row['doctor_subscription_plan_id'] . "\n";
}

$r = mysqli_query($conn, "SELECT id, name FROM doctor_subscription_plans");
echo "\n--- doctor_subscription_plans ---\n";
while($row = mysqli_fetch_assoc($r)) {
    echo $row['id'] . " - " . $row['name'] . "\n";
}

$r = mysqli_query($conn, "SELECT id, name FROM subscription_plans WHERE plan_type='doctor'");
echo "\n--- subscription_plans (type=doctor) ---\n";
while($row = mysqli_fetch_assoc($r)) {
    echo $row['id'] . " - " . $row['name'] . "\n";
}

mysqli_close($conn);
?>
