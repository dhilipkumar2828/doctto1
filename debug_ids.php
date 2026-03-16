<?php
$c = new mysqli('localhost', 'root', '', 'doctto');
$res = $c->query("SELECT id, doctor_name, doctor_show_status FROM doctors WHERE doctor_name LIKE '%NAGARJUN%'");
echo "--- Doctor Status ---\n";
while($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "\n--- Subscription detail ---\n";
$res2 = $c->query("SELECT * FROM doctor_subscriptions WHERE doctor_id=183 AND status='active'");
while($row = $res2->fetch_assoc()) {
    print_r($row);
}
