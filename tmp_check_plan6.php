<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE id = 6");
$row = mysqli_fetch_assoc($res);
print_r($row);

mysqli_close($conn);
