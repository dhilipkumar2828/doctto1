<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$data = [];

$sql = "SELECT id, name FROM subscription_plans";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    $data['subscription_plans'][] = $row;
}

$sql = "SELECT id, name FROM doctor_subscription_plans";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    $data['doctor_subscription_plans'][] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);

mysqli_close($conn);
