<?php
define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'development');
include('application/config/database.php');

echo "--- Doctors Table Columns ---\n";
$res = mysqli_query($con, "SHOW COLUMNS FROM doctors");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " ";
}
echo "\n\n";

echo "--- Subscription Plans (Doctor Type) ---\n";
$res = mysqli_query($con, "SELECT id, name, max_doctors_allowed FROM subscription_plans WHERE plan_type = 'doctor'");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Max: " . $row['max_doctors_allowed'] . "\n";
}
echo "\n";

echo "--- Active Doctor Subscriptions ---\n";
$res = mysqli_query($con, "SELECT ds.doctor_id, d.doctor_name, ds.doctor_subscription_plan_id, ds.status, d.doctor_show_status 
                           FROM doctor_subscriptions ds 
                           JOIN doctors d ON d.id = ds.doctor_id 
                           WHERE ds.status = 'active'");
while($row = mysqli_fetch_assoc($res)) {
    echo "DocID: " . $row['doctor_id'] . " | Name: " . $row['doctor_name'] . " | PlanID: " . $row['doctor_subscription_plan_id'] . " | SubStatus: " . $row['status'] . " | ShowStatus: " . $row['doctor_show_status'] . "\n";
}
echo "\n";
