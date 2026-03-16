<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$data = [];

$sql = "SELECT COUNT(*) as cnt FROM subscription_plans";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$data['subscription_plans_count'] = $row['cnt'];

$sql = "SELECT COUNT(*) as cnt FROM doctor_subscription_plans";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$data['doctor_subscription_plans_count'] = $row['cnt'];

echo json_encode($data, JSON_PRETTY_PRINT);

mysqli_close($conn);
