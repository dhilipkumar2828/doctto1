<?php
$c = new mysqli('localhost', 'root', '', 'doctto');
$res = $c->query("SELECT id, name, max_doctors_allowed FROM subscription_plans WHERE id = 6");
$row = $res->fetch_assoc();
echo "Current Limit for Plan 6: " . $row['max_doctors_allowed'] . "\n";
if($row['max_doctors_allowed'] == 0) {
    echo "Updating to 10...\n";
    $c->query("UPDATE subscription_plans SET max_doctors_allowed = 10 WHERE id = 6");
    echo "Updated. New value: " . $c->query("SELECT max_doctors_allowed FROM subscription_plans WHERE id = 6")->fetch_assoc()['max_doctors_allowed'];
}
