<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "SELECT DISTINCT plan_id FROM subscription_plan_doctors";
$res = mysqli_query($conn, $sql);
$ids = [];
while($row = mysqli_fetch_row($res)) {
    $ids[] = $row[0];
}
echo "IDs in subscription_plan_doctors: " . implode(', ', $ids) . "\n";

mysqli_close($conn);
