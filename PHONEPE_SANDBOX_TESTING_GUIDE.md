# 🧪 **PHONEPE SANDBOX TESTING GUIDE - DOCTOR SUBSCRIPTION FLOW**

Complete guide for testing doctor subscription flow in PhonePe UAT Sandbox environment using PHP CodeIgniter.

---

## **🔧 SANDBOX SETUP**

### **1. Environment Configuration**

**Sandbox Credentials:**
- **Merchant ID:** `PGTESTPAYUAT`
- **Client ID:** `PGTESTPAYUAT`
- **Client Secret:** `099eb0cd-02cf-4e2a-8aca-3e6c6aff0399`
- **Base URL:** `https://api-preprod.phonepe.com/apis/pg-sandbox`
- **Environment:** `SANDBOX`

### **2. Install PhonePe Test App**

**Android:**
1. Download from [PhonePe Developer Portal](https://developer.phonepe.com/v1/docs/custom-uat-sandbox/)
2. Install on your test device
3. Configure test VPA (e.g., `@ybl`)

**iOS:**
1. Contact PhonePe Integration Team
2. Share your email ID
3. Receive invite via Firebase

---

## **🧪 TESTING PROCEDURES**

### **Step 1: Run Sandbox Test Script**

```bash
php test_phonepe_sandbox_subscription.php
```

**Expected Output:**
```
🧪 PHONEPE SANDBOX TESTING - DOCTOR SUBSCRIPTION FLOW
====================================================

1️⃣ Testing Sandbox Configuration...
✅ Sandbox Environment: SANDBOX
✅ Sandbox Base URL: https://api-preprod.phonepe.com/apis/pg-sandbox
✅ Sandbox Merchant ID: PGTESTPAYUAT

2️⃣ Testing Get Subscription Plans...
✅ Plans retrieved successfully
📋 Available Plans:
   - Basic Plan: ₹499.00 (30 days)
   - Premium Plan: ₹999.00 (30 days)

3️⃣ Testing Get Terms and Conditions...
✅ Terms retrieved successfully
📋 Terms ID: 1
📋 Version: 1.0

4️⃣ Testing Accept Terms...
✅ Terms accepted successfully

5️⃣ Testing Check Subscription Status...
✅ Status checked successfully
📊 Has Active Subscription: No

6️⃣ Testing Initiate PhonePe Payment (Sandbox)...
✅ Payment initiated successfully
📋 Transaction ID: SUB_123_1640995200
💰 Amount: ₹499.00
📦 Plan: Basic Plan
🔗 Sandbox Redirect URL: https://mercury.phonepe.com/transact/...
📱 Use PhonePe Test App to complete payment

7️⃣ Testing Sandbox Payment Verification...
✅ Payment verification working
📊 Payment Status: PENDING

8️⃣ Testing Sandbox Webhook...
🔗 Webhook URL: https://doctto.com/subscription_webhook/phonepe_subscription_webhook
📊 Webhook Response Code: 200
✅ Webhook endpoint accessible

9️⃣ Testing Flutter Integration...
📱 Flutter SDK Configuration:
   - Environment: SANDBOX
   - App ID: doctto_app_id
   - Merchant ID: PGTESTPAYUAT
   - App Schema: doctto://payment
✅ Flutter integration ready for sandbox testing
```

### **Step 2: Test Complete Payment Flow**

#### **2.1 Initiate Payment**
```bash
curl -X POST "https://doctto.com/api/doctors/initiate_subscription_payment" \
  -H "Content-Type: application/json" \
  -d '{
    "doctor_id": 129,
    "plan_id": 1,
    "payment_method": "phonepe"
  }'
```

**Response:**
```json
{
  "status": true,
  "order_id": 123,
  "transaction_id": "SUB_123_1640995200",
  "amount": 499.00,
  "plan_name": "Basic Plan",
  "phonepe_config": {
    "redirectUrl": "https://mercury.phonepe.com/transact/...",
    "merchantOrderId": "SUB_123_1640995200",
    "orderId": "PHONEPE_ORDER_123",
    "status": "PENDING",
    "expiresAt": "2024-01-15T11:00:00Z"
  }
}
```

#### **2.2 Complete Payment in Test App**

1. **Open PhonePe Test App**
2. **Configure Test Case Template:**
   - Select "Test Case Templates"
   - Enter Merchant ID: `PGTESTPAYUAT`
   - Click "GET CONFIGURED TEMPLATES"
   - Select "Flow: Standard Checkout"
   - Choose scenario: Success/Failure/Pending

3. **Test Payment Flow:**
   - Use the `redirectUrl` from step 2.1
   - Complete payment in test app
   - Verify response

#### **2.3 Verify Payment**
```bash
curl -X POST "https://doctto.com/api/doctors/verify_phonepe_payment" \
  -H "Content-Type: application/json" \
  -d '{
    "merchant_transaction_id": "SUB_123_1640995200"
  }'
```

---

## **📱 FLUTTER SANDBOX TESTING**

### **Flutter Configuration for Sandbox**

```dart
// Initialize PhonePe SDK for Sandbox
await PhonePePaymentSdk.init(
  environment: Environment.SANDBOX, // Important: Use SANDBOX
  appId: "doctto_app_id",
  merchantId: "PGTESTPAYUAT", // Sandbox Merchant ID
  enableLogging: true, // Enable for debugging
);

// Start payment
final response = await PhonePePaymentSdk.startTransaction(
  request: request,
  appSchema: "doctto://payment",
);
```

### **Test Scenarios in Flutter**

1. **Success Scenario:**
   - Payment completes successfully
   - Subscription gets activated
   - User sees success message

2. **Failure Scenario:**
   - Payment fails
   - Error message displayed
   - Subscription remains pending

3. **Pending Scenario:**
   - Payment pending
   - User can retry payment
   - Status shows pending

---

## **🔍 DEBUGGING & MONITORING**

### **1. Check Logs**

**CodeIgniter Logs:**
```bash
tail -f application/logs/phonepe_sandbox.log
```

**PhonePe SDK Logs:**
```bash
tail -f application/logs/log-$(date +%Y-%m-%d).php
```

### **2. Database Monitoring**

**Check Subscription Records:**
```sql
SELECT * FROM doctor_subscriptions WHERE doctor_id = 129;
SELECT * FROM doctor_subscription_payments WHERE doctor_id = 129;
SELECT * FROM webhook_response WHERE webhook_type = 'subscription_sdk_verified';
```

### **3. Webhook Testing**

**Test Webhook Manually:**
```bash
curl -X POST "https://doctto.com/subscription_webhook/phonepe_subscription_webhook" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SANDBOX_TOKEN" \
  -d '{
    "response": "eyJtZXJjaGFudElkIjoiUEdURVNUUEFZVUFUIiwibWVyY2hhbnRUcmFuc2FjdGlvbklkIjoiU1VCXzEyM18xNjQwOTk1MjAwIiwidHJhbnNhY3Rpb25JZCI6IlBIT05FUEVfU0FORkJPWF9UWE5fMTIzIiwiamFtb3VudCI6NDk5MDAsInN0YXRlIjoiQ09NUExFVEVEIiwicmVzcG9uc2VDb2RlIjoiUEFZTUVOVF9TVUNDRVNTIiwicGF5bWVudEluc3RydW1lbnQiOnsidHlwZSI6IlVQSSIsInV0ciI6IlNBTkRCT1hfVVRSXzEyMyJ9fQ=="
  }'
```

---

## **✅ TESTING CHECKLIST**

### **Backend Testing**
- [ ] Sandbox configuration loaded
- [ ] Payment initiation working
- [ ] Webhook processing working
- [ ] Database updates correct
- [ ] Error handling working
- [ ] Logging enabled

### **Frontend Testing**
- [ ] Flutter SDK initialized
- [ ] Payment flow working
- [ ] Success scenario tested
- [ ] Failure scenario tested
- [ ] Pending scenario tested
- [ ] Error handling working

### **Integration Testing**
- [ ] Complete flow tested
- [ ] Webhook verification working
- [ ] Subscription activation working
- [ ] Database consistency maintained
- [ ] Logs generated correctly

---

## **🚀 GO-LIVE CHECKLIST**

### **Before Production**
- [ ] Change environment to `PRODUCTION`
- [ ] Update credentials to production values
- [ ] Test with real PhonePe credentials
- [ ] Verify webhook URLs
- [ ] Test complete flow end-to-end
- [ ] Monitor logs and errors

### **Production Configuration**
```php
// Update in constants.php
define("PHONEPE_ENVIRONMENT", 'PRODUCTION');
define("PHONEPE_CLIENT_ID", 'YOUR_PRODUCTION_CLIENT_ID');
define("PHONEPE_CLIENT_SECRET", 'YOUR_PRODUCTION_CLIENT_SECRET');
```

---

## **📞 SUPPORT & RESOURCES**

### **PhonePe Support**
- **Documentation:** [PhonePe Developer Portal](https://developer.phonepe.com)
- **Sandbox Guide:** [UAT Sandbox Documentation](https://developer.phonepe.com/v1/docs/custom-uat-sandbox/)
- **Support:** Contact PhonePe Integration Team

### **Test App Downloads**
- **Android:** [Download Link](https://developer.phonepe.com/v1/docs/custom-uat-sandbox/)
- **iOS:** Contact PhonePe team for invite

---

## **🎉 SANDBOX TESTING COMPLETE!**

Your doctor subscription flow is now ready for comprehensive testing in PhonePe's sandbox environment. Follow this guide to test all scenarios and ensure everything works perfectly before going live! 🚀
