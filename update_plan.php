<?php
define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'development');
include('application/config/database.php');
$query = "UPDATE subscription_plans SET max_doctors_allowed = 10 WHERE id = 6";
if (mysqli_query($con, $query)) {
    echo "Plan 6 updated successfully. Max doctors set to 10.\n";
} else {
    echo "Error updating plan 6: " . mysqli_error($con) . "\n";
}
