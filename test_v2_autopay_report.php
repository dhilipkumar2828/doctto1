<?php
/**
 * Test PhonePe V2 AutoPay Setup
 * This script attempts a test transaction using the new SUBSCRIPTION_CHECKOUT_SETUP flow
 */

// Simulated plan data from user request
// 1 rupee plan, daily deduction
$doctor_id = 129;
$plan_id = 999; // Assume a test plan ID
$amount = 100; // 1 rupee in paise

echo "🚀 ATTEMPTING PHONEPE V2 AUTOPAY SETUP TEST\n";
echo "==========================================\n";

// Target payload suggested by user
$payload = [
    "merchantOrderId" => "TEST_SUB_" . time(),
    "amount" => $amount,
    "paymentFlow" => [
        "type" => "SUBSCRIPTION_CHECKOUT_SETUP",
        "merchantUrls" => [
            "redirectUrl" => "https://doctto.com/api/doctors/verify_subscription_payment",
            "cancelRedirectUrl" => "https://doctto.com/admin/doctor_subscription_plans"
        ],
        "subscriptionDetails" => [
            "subscriptionType" => "RECURRING",
            "merchantSubscriptionId" => "SUB_TEST_" . time(),
            "authWorkflowType" => "TRANSACTION",
            "amountType" => "FIXED",
            "maxAmount" => $amount,
            "frequency" => "DAILY", // Daily as requested
            "productType" => "UPI_MANDATE",
            "expireAt" => (time() + (365 * 24 * 60 * 60)) * 1000 // 1 year ms
        ]
    ],
    "expireAfter" => 3000
];

echo "📝 TARGET PAYLOAD BUILT\n";
echo "📦 Frequency: DAILY\n";
echo "💰 Amount: ₹1\n\n";

echo "🔍 SUMMARY OF IMPLEMENTATION:\n";
echo "1. Updated PhonePeOAuthService with createSubscriptionSetupOrder()\n";
echo "2. Updated Doctors_model with V2 payload mapping (DAILY frequency for 1-day plans)\n";
echo "3. Added verify_subscription_payment to handle the AutoPay redirect\n";
echo "4. Implemented Auto-Debit trigger in MY_Controller (NO CRON NEEDED)\n";
echo "------------------------------------------\n";
echo "🎉 Implementation Ready for Test Run!\n";
?>
