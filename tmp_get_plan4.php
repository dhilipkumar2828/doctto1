<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "SELECT * FROM doctor_subscription_plans WHERE id=4";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
print_r($row);

mysqli_close($conn);
