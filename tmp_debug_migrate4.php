<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "INSERT INTO subscription_plans (id, name, plan_type, description, price, duration_days, max_doctors_allowed, perks, is_active, created_at, updated_at) 
        VALUES (4, 'Test Plan', 'doctor', 'Test plan description', 1.00, 3, 1, 'List of Perks...', 1, '2025-09-03 00:05:14', '2025-09-09 04:16:53')";

if (mysqli_query($conn, $sql)) {
    echo "SUCCESS\n";
} else {
    echo "ERROR: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
