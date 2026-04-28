<?php
define('BASEPATH', '1');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_config = $db['default'];

$mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Add columns to doctor_subscription_payments
$queries = [
    "ALTER TABLE doctor_subscription_payments ADD COLUMN IF NOT EXISTS error_code VARCHAR(255) DEFAULT NULL AFTER payment_status",
    "ALTER TABLE doctor_subscription_payments ADD COLUMN IF NOT EXISTS error_message TEXT DEFAULT NULL AFTER error_code",
    "ALTER TABLE doctor_subscription_payments ADD COLUMN IF NOT EXISTS failed_reason TEXT DEFAULT NULL AFTER error_message",
    "ALTER TABLE doctor_subscriptions ADD COLUMN IF NOT EXISTS failed_reason TEXT DEFAULT NULL"
];

foreach ($queries as $query) {
    if ($mysqli->query($query)) {
        echo "Success: " . substr($query, 0, 50) . "...\n";
    } else {
        echo "Error: " . $mysqli->error . " for query: " . $query . "\n";
    }
}

$mysqli->close();
