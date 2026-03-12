<?php
$conn = mysqli_connect("localhost", "root", "", "doctto");
if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error();
    exit;
}

echo "--- PLAN LIST ---\n";
$res = mysqli_query($conn, "SELECT id, plan_type, name, is_active FROM subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Type: {$row['plan_type']} | Name: {$row['name']} | Active: {$row['is_active']}\n";
}

echo "\n--- DOCTOR SUBSCRIPTIONS ---\n";
$res = mysqli_query($conn, "SELECT id, doctor_id, doctor_subscription_plan_id, status, end_at, featured_status FROM doctor_subscriptions");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | DocID: {$row['doctor_id']} | PlanID: {$row['doctor_subscription_plan_id']} | Status: {$row['status']} | End: {$row['end_at']} | Featured: {$row['featured_status']}\n";
}

echo "\n--- DOCTOR SHOW STATUS ---\n";
$res = mysqli_query($conn, "SELECT id, doctor_name, doctor_show_status FROM doctors WHERE id IN (SELECT doctor_id FROM doctor_subscriptions)");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Name: {$row['doctor_name']} | Status: {$row['doctor_show_status']}\n";
}

echo "\n--- PLAN DOCTORS (Featured Table) ---\n";
$res = mysqli_query($conn, "SELECT spd.plan_id, spd.doctor_id, d.doctor_name FROM subscription_plan_doctors spd JOIN doctors d ON spd.doctor_id = d.id");
while($row = mysqli_fetch_assoc($res)) {
    echo "PlanID: {$row['plan_id']} | DocID: {$row['doctor_id']} | Name: {$row['doctor_name']}\n";
}

mysqli_close($conn);
