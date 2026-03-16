<?php
$c = new mysqli('localhost', 'root', '', 'doctto');
$plan_id = 6;
$plan_name = "Classic Plan";

// simulation of all_ids logic
$ids1 = [];
$res = $c->query("SELECT id FROM doctor_subscription_plans WHERE name='$plan_name'");
while($r = $res->fetch_assoc()) $ids1[] = $r['id'];

$ids2 = [];
$res = $c->query("SELECT id FROM subscription_plans WHERE name='$plan_name' AND plan_type='doctor'");
while($r = $res->fetch_assoc()) $ids2[] = $r['id'];

$all_ids = array_unique(array_merge($ids1, $ids2));
$ids_str = implode(',', $all_ids);

echo "Plan IDs for 'Classic Plan': $ids_str\n";

$sql = "SELECT d.id, d.doctor_name, d.doctor_show_status, ds.status, ds.doctor_subscription_plan_id
        FROM doctors d
        JOIN doctor_subscriptions ds ON d.id = ds.doctor_id
        WHERE ds.doctor_subscription_plan_id IN ($ids_str)
        AND ds.status = 'active'
        AND d.doctor_show_status = 'active'
        AND d.id NOT IN (SELECT doctor_id FROM subscription_plan_doctors WHERE plan_id = $plan_id)";

echo "SQL: $sql\n";
$res = $c->query($sql);
echo "Results found: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    print_r($row);
}
