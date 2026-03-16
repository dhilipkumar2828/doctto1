<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$data = [];

$sql = "SELECT id, doctor_id, amount, payment_status, error_message, created_at FROM doctor_subscription_payments ORDER BY id DESC LIMIT 5";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    $data['payments'][] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);

mysqli_close($conn);
