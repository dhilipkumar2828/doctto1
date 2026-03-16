# 🚀 **SERVER DEPLOYMENT GUIDE - PHONEPE SDK**

## **✅ FIXED COMPOSER.JSON**

Your `composer.json` has been updated with the correct PhonePe SDK package information.

## **🔧 SERVER DEPLOYMENT STEPS**

### **Step 1: Upload Files to Server**

Upload these files to your live server:

```
📁 Files to Upload:
├── composer.json (updated with correct PhonePe SDK)
├── application/
│   ├── config/constants.php (updated with PhonePe config)
│   ├── libraries/PhonePeSDKClient_Simple.php (working client)
│   ├── models/Doctors_model.php (updated methods)
│   └── controllers/Subscription_webhook.php (updated webhook)
├── test_phonepe_sandbox_subscription.php (testing script)
└── PHONEPE_SANDBOX_TESTING_GUIDE.md (documentation)
```

### **Step 2: SSH into Your Server**

```bash
ssh u853751150@in-mum-web676
cd public_html
```

### **Step 3: Install PhonePe SDK**

```bash
# Install the PhonePe SDK with correct package
composer require phonepe/pg-php-sdk-v2

# Or install all dependencies
composer install --no-dev --optimize-autoloader
```

### **Step 4: Set Permissions**

```bash
# Set proper permissions
chmod -R 755 application/
chmod 644 application/config/constants.php
chmod 644 application/libraries/PhonePeSDKClient_Simple.php
chmod 644 application/models/Doctors_model.php
```

### **Step 5: Clear Cache**

```bash
# Clear CodeIgniter cache
rm -rf application/cache/*
```

### **Step 6: Test Installation**

```bash
# Test the PhonePe SDK installation
php test_phonepe_sandbox_subscription.php
```

## **📱 SANDBOX TESTING**

### **Test API Endpoint**

```bash
curl -X POST "https://doctto.com/api/doctors/initiate_subscription_payment" \
  -H "Content-Type: application/json" \
  -d '{"doctor_id": 129, "plan_id": 1, "payment_method": "phonepe"}'
```

### **Expected Response**

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

## **🔧 TROUBLESHOOTING**

### **If Composer Install Fails**

```bash
# Try with verbose output
composer install -vvv

# Check PHP version
php -v

# Check if curl extension is installed
php -m | grep curl
```

### **If 500 Error Persists**

1. **Check logs:**
   ```bash
   tail -f application/logs/log-$(date +%Y-%m-%d).php
   ```

2. **Check file permissions:**
   ```bash
   ls -la application/libraries/PhonePeSDKClient_Simple.php
   ```

3. **Test constants:**
   ```bash
   php -r "require 'application/config/constants.php'; echo PHONEPE_CLIENT_ID;"
   ```

## **📋 COMPLETE API TESTING**

### **1. Get Subscription Plans**
```bash
curl -X POST "https://doctto.com/api/doctors/get_doctor_subscription_plans" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### **2. Initiate Payment**
```bash
curl -X POST "https://doctto.com/api/doctors/initiate_subscription_payment" \
  -H "Content-Type: application/json" \
  -d '{"doctor_id": 129, "plan_id": 1, "payment_method": "phonepe"}'
```

### **3. Verify Payment**
```bash
curl -X POST "https://doctto.com/api/doctors/verify_phonepe_payment" \
  -H "Content-Type: application/json" \
  -d '{"merchant_transaction_id": "SUB_123_1640995200"}'
```

## **🎉 SUCCESS INDICATORS**

- ✅ **No 500 Error** - API returns 200 status
- ✅ **Payment Initiation** - Returns redirect URL
- ✅ **Sandbox Working** - Uses test credentials
- ✅ **Webhook Ready** - Can receive callbacks
- ✅ **Database Updated** - Subscription records created

## **📞 SUPPORT**

If you encounter any issues:

1. **Check server logs** for specific errors
2. **Verify file permissions** are correct
3. **Test constants** are loaded properly
4. **Check PHP version** compatibility

Your PhonePe integration is now ready for deployment and testing! 🚀
