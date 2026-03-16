<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "Structure of doctor_subscriptions:\n";
$res = mysqli_query($conn, "DESCRIBE doctor_subscriptions");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
