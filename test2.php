<?php
$mysqli = new mysqli("localhost", "root", "", "doctto");
if ($mysqli->connect_error) { die("Connection failed: " . $mysqli->connect_error); }
$res1 = $mysqli->query("SELECT doctor_id, doctor_status FROM doctor_appointments WHERE id=1");
if ($res1 && $row1 = $res1->fetch_assoc()) {
    echo "TABLE: doctor_appointments\n";
    print_r($row1);
} else {
    echo "Not found in doctor_appointments\n";
}

$res2 = $mysqli->query("SELECT doctor_id, doctor_status FROM online_doctor_appointments WHERE id=1");
if ($res2 && $row2 = $res2->fetch_assoc()) {
    echo "TABLE: online_doctor_appointments\n";
    print_r($row2);
} else {
    echo "Not found in online_doctor_appointments\n";
}
$mysqli->close();
