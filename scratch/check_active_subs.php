<?php
define('BASEPATH', '1');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_config = $db['default'];

$mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
$res = $mysqli->query("SELECT id, autopay_agreement_id FROM doctor_subscriptions WHERE autopay_agreement_id IS NOT NULL AND status='active'");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " Agreement: " . $row['autopay_agreement_id'] . "\n";
}
$mysqli->close();
