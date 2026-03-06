<?php
require 'index.php';
$CI =& get_instance();
$CI->load->database();
$CI->load->dbforge();

echo "Running migrations...<br>";

// 1. Add featured_status to doctor_subscriptions
if (!$CI->db->field_exists('featured_status', 'doctor_subscriptions')) {
    $res = $CI->db->query("ALTER TABLE doctor_subscriptions ADD COLUMN featured_status TINYINT(1) DEFAULT 1 AFTER status");
    echo "Featured Status Column: " . ($res ? "Success" : "Failed") . "<br>";
} else {
    echo "Featured Status Column: Already exists<br>";
}

// 2. Create subscription_plan_doctors table
if (!$CI->db->table_exists('subscription_plan_doctors')) {
    $CI->dbforge->add_field(array(
        'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
        'plan_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
        'doctor_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
        'is_default' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
        'sort_order' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
        'created_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP')
    ));
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('plan_id');
    $CI->dbforge->add_key('doctor_id');
    $res = $CI->dbforge->create_table('subscription_plan_doctors');
    echo "Subscription Plan Doctors Table: " . ($res ? "Success" : "Failed") . "<br>";
} else {
    echo "Subscription Plan Doctors Table: Already exists<br>";
    
    // Check missing columns in existing table
    if (!$CI->db->field_exists('is_default', 'subscription_plan_doctors')) {
        $CI->db->query("ALTER TABLE subscription_plan_doctors ADD COLUMN is_default TINYINT(1) DEFAULT 0 AFTER doctor_id");
        echo " - is_default column added<br>";
    }
    if (!$CI->db->field_exists('sort_order', 'subscription_plan_doctors')) {
        $CI->db->query("ALTER TABLE subscription_plan_doctors ADD COLUMN sort_order INT(11) DEFAULT 0 AFTER is_default");
        echo " - sort_order column added<br>";
    }
}

// 3. Add max_doctors_allowed to doctor_subscription_plans
if (!$CI->db->field_exists('max_doctors_allowed', 'doctor_subscription_plans')) {
    $res = $CI->db->query("ALTER TABLE doctor_subscription_plans ADD COLUMN max_doctors_allowed INT(11) DEFAULT 1 AFTER duration_days");
    echo "Max Doctors Allowed Column: " . ($res ? "Success" : "Failed") . "<br>";
} else {
    echo "Max Doctors Allowed Column: Already exists<br>";
}
