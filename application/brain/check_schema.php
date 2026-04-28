<?php
$mysqli = new mysqli("localhost", "root", "", "doctto");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$tables = ['user_subscriptions', 'user_subscription_payments', 'doctor_subscriptions', 'doctor_subscription_payments', 'subscription_renewals', 'subscription_retries'];

foreach ($tables as $table) {
    echo "=== Table: $table ===\n";
    $result = $mysqli->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "{$row['Field']} - {$row['Type']}\n";
        }
    } else {
        echo "Table $table NOT found\n";
    }
    echo "\n";
}
$mysqli->close();
