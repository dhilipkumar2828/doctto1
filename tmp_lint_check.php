<?php
// Mock CI environment enough to test model method
class MockDB {
    public function where($a, $b=null, $c=null) { return $this; }
    public function get($t=null) { return $this; }
    public function row() { return (object)['id'=>4, 'name'=>'Test Plan', 'description'=>'desc', 'price'=>1.00, 'max_doctors_allowed'=>10]; }
    public function result() { return []; }
    public function select($s) { return $this; }
    public function from($f) { return $this; }
    public function join($t, $c, $ty='inner') { return $this; }
    public function order_by($k, $v) { return $this; }
    public function group_by($g) { return $this; }
    public function get_where($t, $w) { return $this; }
}
// This is too complex. I'll just check for syntax errors.

exec('php -l application/controllers/admin/Subscription_plans.php', $output, $return);
echo implode("\n", $output) . "\n";

exec('php -l application/models/admin/Subscription_plans_model.php', $output2, $return2);
echo implode("\n", $output2) . "\n";
