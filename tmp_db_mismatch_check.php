<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_subscriptions");
$row = mysqli_fetch_assoc($res);
echo "Total records in doctor_subscriptions: " . $row['count'] . "\n";

$sql = "SELECT COUNT(*) as count FROM doctor_subscriptions ds
        JOIN doctors d ON ds.doctor_id = d.id
        JOIN doctor_subscription_plans dsp ON ds.doctor_subscription_plan_id = dsp.id";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
echo "Records showing in Admin (with INNER JOIN doctor_subscription_plans): " . $row['count'] . "\n";

$sql2 = "SELECT COUNT(*) as count FROM doctor_subscriptions ds
        JOIN doctors d ON ds.doctor_id = d.id
        JOIN subscription_plans sp ON ds.doctor_subscription_plan_id = sp.id AND sp.plan_type = 'doctor'";
$res = mysqli_query($conn, $sql2);
$row = mysqli_fetch_assoc($res);
echo "Records that WOULD show if joining with subscription_plans (type=doctor): " . $row['count'] . "\n";

mysqli_close($conn);
?>
