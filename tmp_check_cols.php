<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

function check_col($conn, $table, $col) {
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col'");
    return mysqli_num_rows($res) > 0;
}

echo "subscription_plans has 'name': " . (check_col($conn, 'subscription_plans', 'name') ? 'YES' : 'NO') . "\n";
echo "doctor_subscription_plans has 'name': " . (check_col($conn, 'doctor_subscription_plans', 'name') ? 'YES' : 'NO') . "\n";
echo "subscription_plans has 'plan_type': " . (check_col($conn, 'subscription_plans', 'plan_type') ? 'YES' : 'NO') . "\n";

mysqli_close($conn);
