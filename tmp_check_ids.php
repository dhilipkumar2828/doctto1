<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "SELECT DISTINCT doctor_subscription_plan_id FROM doctor_subscriptions";
$res = mysqli_query($conn, $sql);
$ids = [];
while($row = mysqli_fetch_row($res)) {
    $ids[] = $row[0];
}
echo "IDs in doctor_subscriptions: " . implode(', ', $ids) . "\n";

mysqli_close($conn);
