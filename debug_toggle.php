<?php
// debug_toggle.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'index.php'; // Load CodeIgniter
$CI =& get_instance();
$CI->load->database();
$CI->load->model('admin/Doctor_subscriptions_model', 'doctor_subscriptions_model');

$subscription_id = 1; // Testing with ID 1
$status = 0;

echo "<h1>Debugging Toggle Status</h1>";

try {
    echo "Step 1: Fetching subscription...<br>";
    $subscription = $CI->doctor_subscriptions_model->get_subscription_by_id($subscription_id);
    
    if (!$subscription) {
        die("Error: Subscription not found for ID $subscription_id");
    }
    echo "Subscription Found. Doctor ID: " . $subscription->doctor_id . "<br>";

    echo "Step 2: Changing featured status...<br>";
    $res = $CI->doctor_subscriptions_model->change_featured_status($subscription_id, $status);
    echo "Change Featured Status Result: " . ($res ? "Success" : "Failed") . "<br>";

    if ($res) {
        if ($status == 0) {
            echo "Step 3: Deleting from subscription_plan_doctors...<br>";
            $CI->db->where('doctor_id', $subscription->doctor_id);
            $CI->db->delete('subscription_plan_doctors');
            echo "Delete Result: Success<br>";
        }
    }

    echo "<h2>DONE! No errors found in logic.</h2>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>CAUGHT ERROR: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<h2 style='color:red'>CAUGHT PHP ERROR: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
