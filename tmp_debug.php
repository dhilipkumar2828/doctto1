<?php
require 'index.php';
$CI =& get_instance();
$CI->load->database();
$res = $CI->db->select('plan_type, count(*) as count')->group_by('plan_type')->get('subscription_plans')->result();
print_r($res);
