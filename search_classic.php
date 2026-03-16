<?php
define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'development');
include('application/config/database.php');

$res = mysqli_query($con, "SELECT id, name, max_doctors_allowed, plan_type FROM subscription_plans WHERE name LIKE '%Classic%'");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Max: " . $row['max_doctors_allowed'] . " | Type: " . $row['plan_type'] . "\n";
}
