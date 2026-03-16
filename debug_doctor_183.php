<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Fail");

echo "Checking Doctor 183:\n";
$r = mysqli_query($conn, "SELECT id, doctor_name FROM doctors WHERE id = 183");
print_r(mysqli_fetch_assoc($r));

echo "\nChecking Subscription for Doctor 183:\n";
$r = mysqli_query($conn, "SELECT * FROM doctor_subscriptions WHERE doctor_id = 183");
while($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}

mysqli_close($conn);
?>
