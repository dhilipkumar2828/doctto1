<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "SELECT id FROM doctor_subscription_plans WHERE id IN (6,7,8)";
$res = mysqli_query($conn, $sql);
$ids = [];
while($row = mysqli_fetch_row($res)) {
    $ids[] = $row[0];
}
echo "IDs 6,7,8 in doctor_subscription_plans: " . implode(', ', $ids) . "\n";

mysqli_close($conn);
