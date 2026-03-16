<?php
$mysqli = new mysqli("localhost", "root", "", "doctto");
$res = $mysqli->query("SELECT * FROM doctor_appointments WHERE id=1");
if($row = $res->fetch_assoc()) {
    echo "IN OFFLINE: ";
    print_r($row);
} else {
    echo "NOT in offline.\n";
}

$res2 = $mysqli->query("SELECT * FROM online_doctor_appointments WHERE id=1");
if($row = $res2->fetch_assoc()) {
    echo "IN ONLINE: ";
    print_r($row);
} else {
    echo "NOT in online.\n";
}
