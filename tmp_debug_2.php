<?php
require 'index.php';
$CI =& get_instance();
$CI->load->database();
$res = $CI->db->where('id', 1)->get('subscription_plans')->row();
print_r($res);
