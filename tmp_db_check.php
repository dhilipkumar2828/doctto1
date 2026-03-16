<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$tables = array('doctor_subscriptions', 'user_subscriptions', 'doctors', 'users');
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
    echo "Table: $table\n";
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "  Table not found or error: " . mysqli_error($conn) . "\n";
    }
    
    $count = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
    if ($count) {
        $row = mysqli_fetch_assoc($count);
        echo "  Count: " . $row['count'] . "\n";
    }
    echo "\n";
}

// Check first 5 records of doctor_subscriptions
echo "Top 5 records from doctor_subscriptions:\n";
$res = mysqli_query($conn, "SELECT * FROM doctor_subscriptions LIMIT 5");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}

mysqli_close($conn);
?>
