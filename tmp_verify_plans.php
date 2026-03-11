<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT id, name, plan_type, is_active FROM subscription_plans WHERE plan_type='doctor'";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
mysqli_close($conn);
