<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
// define('ENVIRONMENT', 'development'); // already defined in constants usually, but let's see

$order_id = 986;

// Simulating the DB query
$con = mysqli_connect($db['default']["hostname"], $db['default']["username"], $db['default']["password"], $db['default']["database"]);
$res = $con->query("SELECT * FROM online_doctor_appointments WHERE id = $order_id");
$phonepe_data = $res->fetch_object();

if(!$phonepe_data) {
    echo "No data found for ID $order_id\n";
    exit;
}

echo "Data found for ID $order_id\n";
print_r($phonepe_data);
