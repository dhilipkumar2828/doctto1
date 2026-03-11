<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT id, name, plan_type FROM subscription_plans");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($conn);
