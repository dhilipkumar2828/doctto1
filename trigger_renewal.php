<?php
/**
 * Manual Subscription Renewal Trigger for Testing
 * This script triggers the PhonePe recurring debit for the LATEST subscription
 */

// 1. Hardcoded connection for testing on this specific server
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'doctto';
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 1. Find the latest subscription for testing (Dr T NAGARJUN)
$sql = "SELECT ds.*, d.doctor_name, d.mobile_number as doctor_phone, dsp.name as plan_name, dsp.price as plan_price
        FROM doctor_subscriptions ds 
        JOIN doctors d ON d.id = ds.doctor_id 
        JOIN subscription_plans dsp ON dsp.id = ds.doctor_subscription_plan_id
        WHERE d.mobile_number = '7659805507' 
        ORDER BY ds.id DESC LIMIT 1";

$res = mysqli_query($conn, $sql);
$sub = mysqli_fetch_assoc($res);

if (!$sub) {
    die("ERROR: No subscription found for doctor 7659805507\n");
}

echo "🔔 Found Subscription ID: " . $sub['id'] . " for " . $sub['doctor_name'] . "\n";
echo "📦 Plan: " . $sub['plan_name'] . " (₹" . $sub['plan_price'] . ")\n";
echo "🔑 Agreement ID: " . $sub['autopay_agreement_id'] . "\n";
echo "--------------------------------------------------\n";

// 2. Trigger the renewal via URL to the fixed controller
$trigger_url = "https://www.doctto.com/Subscription_renewal_cron/process_manual_renewal/" . $sub['id'];

echo "🚀 Triggering Manual Renewal via URL: $trigger_url\n";

$ch = curl_init($trigger_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Use SSL check if needed, but on localhost or dev it might be skipped
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📡 HTTP Response Code: $http_code\n";
echo "📄 Response Output:\n" . $response . "\n";
echo "--------------------------------------------------\n";
echo "✅ Done. Please check 'doctor_subscription_payments' table for details.\n";
