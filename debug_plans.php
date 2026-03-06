<?php
require 'index.php';
$CI =& get_instance();
$CI->load->database();

echo "--- subscription_plans ---\n";
$res1 = $CI->db->get('subscription_plans')->result();
print_r($res1);

echo "\n--- doctor_subscription_plans ---\n";
$res2 = $CI->db->get('doctor_subscription_plans')->result();
print_r($res2);

echo "\n--- doctor_subscriptions (first 5) ---\n";
$res3 = $CI->db->limit(5)->get('doctor_subscriptions')->result();
print_r($res3);
