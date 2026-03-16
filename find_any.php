<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'doctto';
$conn = mysqli_connect($hostname, $username, $password, $database);

$sql = "SELECT ds.* FROM doctor_subscriptions ds WHERE ds.autopay_enabled = 1 LIMIT 5";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
