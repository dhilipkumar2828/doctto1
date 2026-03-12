<?php
define('BASEPATH', 'TRUE');
define('ENVIRONMENT', 'development');
include('application/config/database.php');

$res = mysqli_query($con, "SELECT id, doctor_name, doctor_show_status, doctor_login_status FROM doctors WHERE id = 183");
$row = mysqli_fetch_assoc($res);
echo json_encode($row) . "\n";
