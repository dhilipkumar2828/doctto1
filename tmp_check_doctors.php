<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT count(*) as count FROM doctors");
$row = mysqli_fetch_assoc($res);
echo "Doctors total count: " . $row['count'];

$res = mysqli_query($conn, "SELECT count(*) as count FROM doctors WHERE doctor_login_status = 'active'");
$row = mysqli_fetch_assoc($res);
echo "\nActive Doctors count: " . $row['count'];

mysqli_close($conn);
