<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$tables = array('doctor_subscriptions', 'user_subscriptions');
foreach ($tables as $table) {
    echo "Table: $table\n";
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
    if ($result) {
        $cols = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $cols[] = $row['Field'];
        }
        echo "  Columns: " . implode(', ', $cols) . "\n";
    }
    
    $count = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
    if ($count) {
        $row = mysqli_fetch_assoc($count);
        echo "  Count: " . $row['count'] . "\n";
    }
    echo "\n";
}

echo "First record from doctor_subscriptions:\n";
$res = mysqli_query($conn, "SELECT * FROM doctor_subscriptions LIMIT 1");
if ($res) {
    print_r(mysqli_fetch_assoc($res));
}

echo "\nFirst record from user_subscriptions:\n";
$res = mysqli_query($conn, "SELECT * FROM user_subscriptions LIMIT 1");
if ($res) {
    print_r(mysqli_fetch_assoc($res));
}

mysqli_close($conn);
?>
