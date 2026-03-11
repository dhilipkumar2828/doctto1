<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "Structure of subscription_plans:\n";
$res = mysqli_query($conn, "DESCRIBE subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\nStructure of doctor_subscription_plans:\n";
$res = mysqli_query($conn, "DESCRIBE doctor_subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
