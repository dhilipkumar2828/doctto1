<?php
define('BASEPATH', '1');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_config = $db['default'];

$mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$query = "SELECT id, phonepe_subscription_id, merchant_subscription_id, autopay_agreement_id FROM doctor_subscriptions LIMIT 10";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Query failed: " . $mysqli->error;
}

$mysqli->close();
