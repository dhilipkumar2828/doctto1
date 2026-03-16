<?php
define('BASEPATH', 'TRUE');
require 'index.php';
$CI =& get_instance();
$CI->load->database();
$sub = $CI->db->select('ds.id, d.name')
    ->from('doctor_subscriptions ds')
    ->join('doctors d', 'd.id = ds.doctor_id')
    ->where('d.phone', '7659805507')
    ->order_by('ds.id', 'DESC')
    ->get()
    ->row_array();
if ($sub) {
    echo "SUCCESS: Found subscription ID " . $sub['id'] . " for " . $sub['name'] . "\n";
} else {
    echo "ERROR: Subscription not found for phone 7659805507\n";
}
