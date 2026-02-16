<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PhonePe Sandbox Configuration
 * For testing doctor subscription flow in UAT environment
 */

// PhonePe Sandbox Credentials
$config['phonepe_sandbox'] = array(
    // Sandbox API Configuration
    'base_url' => 'https://api-preprod.phonepe.com/apis/pg-sandbox',
    'merchant_id' => 'PGTESTPAYUAT',
    'client_id' => 'PGTESTPAYUAT',
    'client_secret' => '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399',
    'client_version' => '1.0',
    'environment' => 'SANDBOX',
    
    // Sandbox Test Configuration
    'test_vpa' => '@ybl', // Test VPA for UPI Collect
    'test_phone' => '9999999999', // Test phone number
    'test_email' => 'test@doctto.com', // Test email
    
    // Sandbox URLs
    'redirect_url' => 'https://doctto.com/api/doctors/subscription_payment_callback',
    'callback_url' => 'https://doctto.com/subscription_webhook/phonepe_subscription_webhook',
    
    // Sandbox Test Scenarios
    'test_scenarios' => array(
        'success' => array(
            'response_code' => 'PAYMENT_SUCCESS',
            'state' => 'COMPLETED',
            'description' => 'Payment completed successfully'
        ),
        'failure' => array(
            'response_code' => 'PAYMENT_FAILED',
            'state' => 'FAILED',
            'description' => 'Payment failed'
        ),
        'pending' => array(
            'response_code' => 'PAYMENT_PENDING',
            'state' => 'PENDING',
            'description' => 'Payment pending'
        )
    ),
    
    // Sandbox Logging
    'enable_logging' => true,
    'log_level' => 'debug',
    'log_file' => 'phonepe_sandbox.log'
);

// Sandbox Test Data
$config['sandbox_test_data'] = array(
    'test_doctors' => array(
        array(
            'id' => 129,
            'name' => 'Test Doctor 1',
            'email' => 'testdoctor1@doctto.com',
            'phone' => '9999999999'
        ),
        array(
            'id' => 130,
            'name' => 'Test Doctor 2',
            'email' => 'testdoctor2@doctto.com',
            'phone' => '9999999998'
        )
    ),
    'test_plans' => array(
        array(
            'id' => 1,
            'name' => 'Basic Plan',
            'price' => 499.00,
            'duration_days' => 30
        ),
        array(
            'id' => 2,
            'name' => 'Premium Plan',
            'price' => 999.00,
            'duration_days' => 30
        )
    )
);
?>
