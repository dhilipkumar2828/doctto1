<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require 'application/config/database.php';
$db_config = $db['default'];

$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT id FROM online_doctor_appointments ORDER BY id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'];
} else {
    echo "No records found";
}

$conn->close();
?>
