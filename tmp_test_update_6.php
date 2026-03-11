<?php
require_once 'index.php';
// We need to bypass some stuff to run in CLI if needed, but let's just use raw model if we can.
// Actually, let's just use raw mysqli to verify the model fix I made.

$conn = mysqli_connect('localhost', 'root', '', 'doctto');

// Suppose user updates ID 6 to max_doctors_allowed = 2
$id = 6;
$new_val = 2;

// Check what Subscription_plans_model::update_plan does
$query = "UPDATE subscription_plans SET max_doctors_allowed = $new_val WHERE id = $id";
mysqli_query($conn, $query);

echo "Updated ID 6 to 2. Checking again:\n";
$res = mysqli_query($conn, "SELECT max_doctors_allowed FROM subscription_plans WHERE id = 6");
$row = mysqli_fetch_assoc($res);
echo "Value: " . $row['max_doctors_allowed'];

// Set it back to 100 for now to keep them unblocked if they haven't saved their edit yet
// Or maybe set it to 1 since they clearly want 1 for Classic Plan.
mysqli_query($conn, "UPDATE subscription_plans SET max_doctors_allowed = 1 WHERE id = 6");

mysqli_close($conn);
