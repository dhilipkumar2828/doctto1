<?php
/**
 * DB Fix Script for Live Server
 * Upload this to your root directory and run: https://doctto.com/fix_db_live.php
 */

// --- DATABASE CONFIGURATION ---
// Please check these credentials with your Live Server database settings
$hostname = "localhost";
$username = "root"; 
$password = "";     
$database = "doctto";
// ------------------------------

$con = mysqli_connect($hostname, $username, $password, $database);

if (!$con) {
    echo "<h3 style='color:red'>Database Connection Failed!</h3>";
    echo "Error: " . mysqli_connect_error() . "<br><br>";
    echo "<b>Note:</b> Please open <code>application/config/database.php</code> on your live server and copy the 'username', 'password', and 'database' values into this file.";
    exit;
}

echo "<h2>Starting Database Repair for Featured Status...</h2><hr>";

// 1. Check/Add featured_status in doctor_subscriptions
$sh1 = $con->query("SHOW COLUMNS FROM doctor_subscriptions LIKE 'featured_status'");
if ($sh1 && $sh1->num_rows == 0) {
    $res1 = $con->query("ALTER TABLE doctor_subscriptions ADD COLUMN featured_status TINYINT(1) DEFAULT 1 AFTER status");
    if ($res1) {
        echo "<b style='color:green'>[SUCCESS]</b> Added 'featured_status' column to 'doctor_subscriptions' table.<br>";
    } else {
        echo "<b style='color:red'>[FAILED]</b> Could not add 'featured_status' column: " . $con->error . "<br>";
    }
} else {
    echo "[SKIP] 'featured_status' column already exists in 'doctor_subscriptions'.<br>";
}

// 2. Check/Create subscription_plan_doctors table
$res2 = $con->query("CREATE TABLE IF NOT EXISTS `subscription_plan_doctors` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) UNSIGNED NOT NULL,
  `doctor_id` int(11) UNSIGNED NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `plan_id` (`plan_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

if ($res2) {
    echo "<b style='color:green'>[SUCCESS]</b> Table 'subscription_plan_doctors' is ready.<br>";
} else {
    echo "<b style='color:red'>[FAILED]</b> Could not create 'subscription_plan_doctors' table: " . $con->error . "<br>";
}

// 3. Check/Add max_doctors_allowed in doctor_subscription_plans
$sh2 = $con->query("SHOW COLUMNS FROM doctor_subscription_plans LIKE 'max_doctors_allowed'");
if ($sh2 && $sh2->num_rows == 0) {
    $res3 = $con->query("ALTER TABLE doctor_subscription_plans ADD COLUMN max_doctors_allowed INT(11) DEFAULT 1 AFTER duration_days");
    if ($res3) {
        echo "<b style='color:green'>[SUCCESS]</b> Added 'max_doctors_allowed' column to 'doctor_subscription_plans' table.<br>";
    } else {
        echo "<b style='color:red'>[FAILED]</b> Could not add 'max_doctors_allowed' column: " . $con->error . "<br>";
    }
} else {
    echo "[SKIP] 'max_doctors_allowed' column already exists in 'doctor_subscription_plans'.<br>";
}

echo "<hr><h3>Process Complete!</h3>";
echo "Try testing the toggle status now. <b>Note: Delete this file from server after successful use for security.</b>";

mysqli_close($con);
?>
