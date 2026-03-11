<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "--- subscription_plans ---\n";
$res = mysqli_query($conn, "DESC subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n--- doctor_subscription_plans ---\n";
$res = mysqli_query($conn, "DESC doctor_subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

mysqli_close($conn);
