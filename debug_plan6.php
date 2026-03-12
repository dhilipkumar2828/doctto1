<?php
$con = new mysqli('localhost', 'root', '', 'doctto');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

echo "=== subscription_plans (ID 6) ===\n";
$res = $con->query("SELECT * FROM subscription_plans WHERE id=6");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n=== doctor_subscription_plans (Classic) ===\n";
$res = $con->query("SELECT * FROM doctor_subscription_plans WHERE name LIKE '%Classic%'");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n=== doctor_subscriptions (active) ===\n";
$res = $con->query("SELECT ds.doctor_id, ds.doctor_subscription_plan_id, ds.status, d.doctor_name, d.doctor_show_status FROM doctor_subscriptions ds JOIN doctors d ON d.id=ds.doctor_id WHERE ds.status='active'");
while($row = $res->fetch_assoc()) print_r($row);

$con->close();
