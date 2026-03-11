<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT id, name, plan_type FROM subscription_plans WHERE id=4";
$res = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
} else {
    echo "ID 4 NOT FOUND";
}
mysqli_close($conn);
