<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

mysqli_query($conn, "UPDATE subscription_plans SET max_doctors_allowed = 100 WHERE plan_type = 'doctor'");
echo "Affected rows: " . mysqli_affected_rows($conn);

mysqli_close($conn);
