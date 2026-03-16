<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "subscription_plan_doctors:\n";
$res = mysqli_query($conn, "DESCRIBE subscription_plan_doctors");
while($row = mysqli_fetch_assoc($res)) print_r($row);

mysqli_close($conn);
