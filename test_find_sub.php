<?php
define('BASEPATH', 'TRUE');
require_once 'application/config/database.php';
$db = $db['default'];
$conn = mysqli_connect($db['hostname'], $db['username'], $db['password'], $db['database']);

$sql = "SELECT ds.*, d.name, d.phone 
        FROM doctor_subscriptions ds 
        JOIN doctors d ON d.id = ds.doctor_id 
        WHERE d.phone = '7659805507' 
        ORDER BY ds.id DESC LIMIT 1";

$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if ($row) {
    echo "ID: " . $row['id'] . "\n";
    echo "Doctor: " . $row['name'] . "\n";
    echo "Plan ID: " . $row['doctor_subscription_plan_id'] . "\n";
    echo "Status: " . $row['status'] . "\n";
    echo "Auto Renew: " . $row['auto_renew'] . "\n";
    echo "Autopay Enabled: " . $row['autopay_enabled'] . "\n";
    echo "Agreement ID: " . $row['autopay_agreement_id'] . "\n";
} else {
    echo "NOT FOUND";
}
