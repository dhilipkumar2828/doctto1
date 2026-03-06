<?php
require 'index.php';
$CI =& get_instance();
$CI->load->database();
if (!$CI->db->field_exists('max_doctors_allowed', 'doctor_subscription_plans')) {
    $CI->db->query("ALTER TABLE doctor_subscription_plans ADD COLUMN max_doctors_allowed INT(11) DEFAULT 1 AFTER duration_days");
}
$CI->db->where('id', 1)->update('doctor_subscription_plans', array('max_doctors_allowed' => 1));
$CI->db->where('id', 2)->update('doctor_subscription_plans', array('max_doctors_allowed' => 3));
$CI->db->where('id', 3)->update('doctor_subscription_plans', array('max_doctors_allowed' => 5));
echo "Success";
