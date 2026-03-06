<?php
// Direct DB connection - no CI needed
$config = include('application/config/database.php');
$db = $config['db']['default'] ?? null;

if (!$db) {
    // Try reading it manually
    $content = file_get_contents('application/config/database.php');
    preg_match_all("/'hostname'\s*=>\s*'([^']*)'/", $content, $host);
    preg_match_all("/'username'\s*=>\s*'([^']*)'/", $content, $user);
    preg_match_all("/'password'\s*=>\s*'([^']*)'/", $content, $pass);
    preg_match_all("/'database'\s*=>\s*'([^']*)'/", $content, $dbname);

    $hostname = $host[1][0] ?? 'localhost';
    $username = $user[1][0] ?? 'root';
    $password = $pass[1][0] ?? '';
    $database = $dbname[1][0] ?? '';
} else {
    $hostname = $db['hostname'];
    $username = $db['username'];
    $password = $db['password'];
    $database = $db['database'];
}

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    echo "DB Connect Error: " . $conn->connect_error;
    exit;
}

echo "Connected OK\n\n";

// Check doctor_subscription_plans columns
$r = $conn->query("SHOW COLUMNS FROM doctor_subscription_plans");
echo "=== doctor_subscription_plans columns ===\n";
while ($row = $r->fetch_assoc()) { echo $row['Field'] . "\n"; }

// Check doctor_subscriptions columns
$r2 = $conn->query("SHOW COLUMNS FROM doctor_subscriptions");
echo "\n=== doctor_subscriptions columns ===\n";
while ($row = $r2->fetch_assoc()) { echo $row['Field'] . "\n"; }

// Run the actual query
$sql = "SELECT d.id as doctor_id, d.doctor_name, sp.name as plan_name, sp.price as plan_price
        FROM doctors d
        JOIN doctor_subscriptions ds ON d.id = ds.doctor_id
        JOIN doctor_subscription_plans sp ON sp.id = ds.doctor_subscription_plan_id
        WHERE ds.status = 'active'
        AND ds.featured_status = 1
        AND d.doctor_show_status = 'active'
        GROUP BY d.id";

echo "\n=== Query Test ===\n";
$r3 = $conn->query($sql);
if (!$r3) {
    echo "SQL ERROR: " . $conn->error . "\n";
} else {
    echo "Records found: " . $r3->num_rows . "\n";
    while ($row = $r3->fetch_assoc()) {
        echo " - Doctor: " . $row['doctor_name'] . " | Plan: " . $row['plan_name'] . "\n";
    }
}
$conn->close();
