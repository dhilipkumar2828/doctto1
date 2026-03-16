<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

echo "Tables starting with 'doctor_subscription':\n";
$res = mysqli_query($conn, "SHOW TABLES LIKE 'doctor_subscription%'");
while($row = mysqli_fetch_row($res)) {
    echo $row[0] . "\n";
}

echo "\nTables starting with 'subscription':\n";
$res = mysqli_query($conn, "SHOW TABLES LIKE 'subscription%'");
while($row = mysqli_fetch_row($res)) {
    echo $row[0] . "\n";
}

mysqli_close($conn);
