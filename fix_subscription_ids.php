<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed");

echo "Before Update - Subscription counts per Plan ID:\n";
$res = mysqli_query($conn, "SELECT doctor_subscription_plan_id, COUNT(*) as count FROM doctor_subscriptions GROUP BY doctor_subscription_plan_id");
while($row = mysqli_fetch_assoc($res)) {
    echo "Plan ID " . $row['doctor_subscription_plan_id'] . ": " . $row['count'] . "\n";
}

// Mapping: Old ID to New ID (based on Name match)
// Classic (1 -> 6)
// Advanced (2 -> 7)
// Popular (3 -> 8)
$updates = [
    1 => 6,
    2 => 7,
    3 => 8
];

foreach ($updates as $old => $new) {
    $sql = "UPDATE doctor_subscriptions SET doctor_subscription_plan_id = $new WHERE doctor_subscription_plan_id = $old";
    if (mysqli_query($conn, $sql)) {
        echo "Updated Plan ID $old to $new successfully. Affected rows: " . mysqli_affected_rows($conn) . "\n";
    } else {
        echo "Error updating Plan ID $old: " . mysqli_error($conn) . "\n";
    }
}

echo "\nAfter Update - Subscription counts per Plan ID:\n";
$res = mysqli_query($conn, "SELECT doctor_subscription_plan_id, COUNT(*) as count FROM doctor_subscriptions GROUP BY doctor_subscription_plan_id");
while($row = mysqli_fetch_assoc($res)) {
    echo "Plan ID " . $row['doctor_subscription_plan_id'] . ": " . $row['count'] . "\n";
}

mysqli_close($conn);
?>
