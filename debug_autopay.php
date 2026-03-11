<?php
/**
 * Direct Manual Trigger for PhonePe AutoPay Notify
 * Run this in browser to see exactly why it's not working.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap CodeIgniter properly
ob_start();
require_once 'index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->database();
$CI->load->library('PhonePeOAuthService');

echo "<h2>PhonePe AutoPay Notify Debugger (v2)</h2>";

// 0. Database Info
echo "Connected to Database: " . $CI->db->database . "<br><br>";

// 1. Fetch current time and target time
$now = date('Y-m-d H:i:s');
$tomorrow_limit = date('Y-m-d H:i:s', strtotime('+24 hours'));

echo "Current Time: $now<br>";
echo "Notification Limit (Expiring before): $tomorrow_limit<br><br>";

// 2. Listing all active subs for debugging
echo "<strong>Listing last 5 subscriptions:</strong><br>";
$all_subs = $CI->db->select('id, doctor_id, doctor_subscription_plan_id as plan_id, status, end_at, autopay_enabled')
    ->from('doctor_subscriptions')
    ->order_by('id', 'DESC')
    ->limit(5)
    ->get()->result_array();

echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr><th>ID</th><th>Doctor</th><th>Plan</th><th>Status</th><th>End At</th><th>Autopay</th></tr>";
foreach ($all_subs as $s) {
    $color = ($s['autopay_enabled'] == 1 && $s['status'] == 'active') ? 'lightgreen' : 'white';
    echo "<tr style='background:$color'>";
    echo "<td>".$s['id']."</td><td>".$s['doctor_id']."</td><td>".$s['plan_id']."</td><td>".$s['status']."</td><td>".$s['end_at']."</td><td>".$s['autopay_enabled']."</td>";
    echo "</tr>";
}
echo "</table><br>";

// 3. Query due subscriptions (The original logic)
$due_subs = $CI->db->select('ds.*, dsp.price, dsp.duration_days')
    ->from('doctor_subscriptions ds')
    ->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id')
    ->where('ds.status', 'active')
    ->where('ds.autopay_enabled', 1)
    ->where('ds.end_at <=', $tomorrow_limit)
    ->get()->result();

if (empty($due_subs)) {
    echo "<span style='color:red'>FAILED: No subscriptions found needing notification.</span><br>";
    echo "Check if your record has autopay_enabled=1 and end_at < $tomorrow_limit<br>";
    die();
}

echo "Found " . count($due_subs) . " subscriptions due.<br>";

// 3. Get Token
$token_result = $CI->phonepeoauthservice->getBearerToken();
if (!$token_result['status']) {
    echo "<span style='color:red'>TOKEN ERROR: </span>" . json_encode($token_result) . "<br>";
    die();
}
echo "Auth Token Obtained Successfully.<br>";

// 4. Try Notify
foreach ($due_subs as $sub) {
    echo "Processing Sub ID: " . $sub->id . " (End At: " . $sub->end_at . ")<br>";
    
    if (empty($sub->autopay_agreement_id)) {
        echo "<span style='color:orange'>SKIP: autopay_agreement_id is empty for Sub " . $sub->id . "</span><br>";
        continue;
    }

    $cycle_id = 'DEBUG_' . $sub->id . '_' . date('Ymd_His');
    $amount_in_paise = intval($sub->price * 100);

    echo "Calling Notify API...<br>";
    $notify_result = $CI->phonepeoauthservice->notifyRedemption(
        $cycle_id,
        $sub->autopay_agreement_id,
        $amount_in_paise,
        $token_result['access_token'] ?? $token_result['accessToken'],
        true
    );

    echo "Result: " . json_encode($notify_result) . "<br>";

    if ($notify_result['status']) {
        echo "<span style='color:green'>SUCCESS: Notification sent and row will be created.</span><br>";
    } else {
        echo "<span style='color:red'>FAIL: PhonePe API error. Check credentials or Agreement ID.</span><br>";
    }
}
?>
