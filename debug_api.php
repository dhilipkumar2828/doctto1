<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'index.php';
$CI =& get_instance();
$CI->load->database();

// Check doctor_subscription_plans columns
echo "=== doctor_subscription_plans columns ===\n";
$fields = $CI->db->list_fields('doctor_subscription_plans');
echo implode(', ', $fields) . "\n\n";

// Check doctor_subscriptions columns
echo "=== doctor_subscriptions columns ===\n";
$fields2 = $CI->db->list_fields('doctor_subscriptions');
echo implode(', ', $fields2) . "\n\n";

// Test the get_all_subscribed_doctors query
echo "=== Testing Query ===\n";
$CI->db->select('d.id as doctor_id, d.doctor_name, sp.name as plan_name, sp.price as plan_price');
$CI->db->from('doctors d');
$CI->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id');
$CI->db->join('doctor_subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id');
$CI->db->where('ds.status', 'active');
$CI->db->where('ds.featured_status', 1);
$CI->db->where('d.doctor_show_status', 'active');
$CI->db->group_by('d.id');

$last_query_sql = $CI->db->get_compiled_select();
echo "SQL: " . $last_query_sql . "\n\n";

try {
    $result = $CI->db->query($last_query_sql)->result();
    echo "Records found: " . count($result) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
