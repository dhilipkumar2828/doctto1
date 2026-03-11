<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$sql = "INSERT INTO subscription_plans (id, name, plan_type, description, price, duration_days, max_doctors_allowed, perks, is_active, created_at, updated_at) 
        VALUES (4, 'Test Plan', 'doctor', 'Test plan description', 1.00, 3, 1, 'Premium Profile Visibility\nHigh Rating Priority\nHighlighted Profile\nPR Articles\nMore Patient Reach\nHigh Viewer Boost', 1, '2025-09-03 00:05:14', '2025-09-09 04:16:53')";

if (mysqli_query($conn, $sql)) {
    echo "Test Plan inserted into subscription_plans successfully.\n";
} else {
    echo "Error inserting Test Plan: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
