<?php
$c = new mysqli('localhost', 'root', '', 'doctto');
$r = $c->query('SELECT id, name, plan_type, max_doctors_allowed FROM subscription_plans');
while($row = $r->fetch_assoc()){ 
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Type: " . $row['plan_type'] . " | Max: " . $row['max_doctors_allowed'] . "\n";
}
echo "\n--- doctor_subscriptions check ---\n";
$r2 = $c->query("SELECT ds.doctor_id, ds.doctor_subscription_plan_id, d.doctor_name, d.doctor_show_status FROM doctor_subscriptions ds JOIN doctors d ON d.id=ds.doctor_id WHERE ds.status='active'");
while($row = $r2->fetch_assoc()){
    print_r($row);
}
