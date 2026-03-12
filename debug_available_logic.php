<?php
define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'development');
include('application/config/database.php');

$plan_id = 6;
// Get plan name
$res = mysqli_query($con, "SELECT name FROM subscription_plans WHERE id = $plan_id");
$plan = mysqli_fetch_assoc($res);
$plan_name = $plan['name'];
echo "Plan Name: $plan_name\n";

// Get IDs from doctor_subscription_plans
$ids1 = [];
$res = mysqli_query($con, "SELECT id FROM doctor_subscription_plans WHERE name = '$plan_name'");
while($row = mysqli_fetch_assoc($res)) $ids1[] = $row['id'];
echo "IDs from doctor_subscription_plans: " . implode(',', $ids1) . "\n";

// Get IDs from subscription_plans
$ids2 = [];
$res = mysqli_query($con, "SELECT id FROM subscription_plans WHERE name = '$plan_name' AND plan_type = 'doctor'");
while($row = mysqli_fetch_assoc($res)) $ids2[] = $row['id'];
echo "IDs from subscription_plans: " . implode(',', $ids2) . "\n";

$all_ids = array_unique(array_merge($ids1, $ids2));
echo "All IDs: " . implode(',', $all_ids) . "\n";

if (empty($all_ids)) {
    echo "No IDs found.\n";
    exit;
}

$all_ids_str = implode(',', $all_ids);
$query = "SELECT d.id, d.doctor_name 
          FROM doctors d 
          JOIN doctor_subscriptions ds ON d.id = ds.doctor_id 
          WHERE ds.doctor_subscription_plan_id IN ($all_ids_str) 
          AND ds.status = 'active' 
          AND d.doctor_login_status = 'active' 
          AND d.id NOT IN (SELECT doctor_id FROM subscription_plan_doctors WHERE plan_id = $plan_id)";

echo "Running Query: $query\n";
$res = mysqli_query($con, $query);
if (!$res) {
    echo "Query Error: " . mysqli_error($con) . "\n";
} else {
    while($row = mysqli_fetch_assoc($res)) {
        echo "Found Doctor: ID " . $row['id'] . " | Name: " . $row['doctor_name'] . "\n";
    }
}
