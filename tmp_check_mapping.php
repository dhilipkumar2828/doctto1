<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "SELECT id, name, plan_type FROM subscription_plans WHERE id IN (1,2,3,4,5,6,7,8)";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
