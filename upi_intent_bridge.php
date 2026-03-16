<?php
if (!defined('BASEPATH')) {
    // Allow direct access for this bridge page
}

/**
 * UPI Intent Bridge Page
 * 
 * This page solves ERR_UNKNOWN_URL_SCHEME for UPI deep links in Android WebView.
 * It intercepts custom URL schemes like credpay://, phonepe://, paytm://, etc.
 * and converts them to android.intent:// format that WebView can handle.
 * 
 * Usage: Redirect to this page with the payment_url as a query parameter.
 * Example: /upi_intent_bridge.php?payment_url=https://mercury-t2.phonepe.com/...
 */

$payment_url = isset($_GET['payment_url']) ? urldecode($_GET['payment_url']) : '';
$merchant_txn_id = isset($_GET['mtid']) ? htmlspecialchars($_GET['mtid']) : '';
$return_url = isset($_GET['return_url']) ? urldecode($_GET['return_url']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Opening Payment App...</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container {
            text-align: center;
            padding: 40px 30px;
            max-width: 380px;
            width: 100%;
        }
        .logo-ring {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5c35d9, #7c4dff);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 0 40px rgba(124, 77, 255, 0.4);
            animation: pulse 2s infinite;
        }
        .logo-ring svg {
            width: 45px;
            height: 45px;
            fill: white;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 40px rgba(124, 77, 255, 0.4); }
            50% { box-shadow: 0 0 60px rgba(124, 77, 255, 0.7); }
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffffff;
        }
        p {
            font-size: 14px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .app-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 32px;
        }
        .app-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 2px solid rgba(255,255,255,0.1);
        }
        .app-icon:hover, .app-icon:active { transform: scale(1.12); }
        .app-icon.phonepe { background: linear-gradient(135deg, #5f259f, #7b32c8); }
        .app-icon.gpay    { background: linear-gradient(135deg, #1a73e8, #4285f4); }
        .app-icon.paytm   { background: linear-gradient(135deg, #00b9f5, #0093cc); }
        .app-icon.cred    { background: linear-gradient(135deg, #1c1c1e, #3a3a3c); }
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255,255,255,0.15);
            border-top: 3px solid #7c4dff;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .status-text {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 24px;
        }
        .manual-btn {
            display: inline-block;
            background: linear-gradient(135deg, #7c4dff, #5c35d9);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 8px 24px rgba(124, 77, 255, 0.35);
            transition: all 0.2s;
        }
        .manual-btn:hover, .manual-btn:active {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(124, 77, 255, 0.5);
        }
        .note {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            margin-top: 20px;
            line-height: 1.5;
        }
        #status-msg { min-height: 20px; }
        .success-icon { color: #4caf50; font-size: 40px; display: none; }
        .error-msg { color: #ff5252; font-size: 13px; display: none; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo-ring">
        <!-- UPI Logo SVG -->
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
        </svg>
    </div>

    <h1>Opening Payment App</h1>
    <p>Redirecting you to your UPI app to complete the mandate setup</p>

    <div class="app-icons">
        <div class="app-icon phonepe" onclick="launchApp('phonepe')" title="PhonePe">📱</div>
        <div class="app-icon gpay"    onclick="launchApp('gpay')"    title="Google Pay">💳</div>
        <div class="app-icon paytm"   onclick="launchApp('paytm')"   title="Paytm">💰</div>
        <div class="app-icon cred"    onclick="launchApp('cred')"    title="CRED">⭐</div>
    </div>

    <div class="spinner" id="spinner"></div>
    <div class="status-text" id="status-msg">Detecting your UPI app...</div>

    <a href="#" class="manual-btn" id="open-btn" onclick="handleManualOpen()" style="display:none">
        🔗 Open Payment App
    </a>

    <div class="error-msg" id="error-msg">
        If the app didn't open automatically, tap the button above.
    </div>

    <p class="note">
        Secure UPI AutoPay Mandate Setup<br>
        Powered by PhonePe Payment Gateway
    </p>
</div>

<script>
    // =========================================================
    // UPI INTENT BRIDGE - Solves ERR_UNKNOWN_URL_SCHEME
    // =========================================================

    var PAYMENT_URL = <?php echo json_encode($payment_url); ?>;
    var RETURN_URL  = <?php echo json_encode($return_url ?: base_url('admin/doctor_subscription_plans')); ?>;
    var MTID        = <?php echo json_encode($merchant_txn_id); ?>;

    // Known UPI App deep link schemes
    var UPI_SCHEMES = {
        'phonepe' : 'phonepe://pay',
        'gpay'    : 'tez://upi/pay',
        'paytm'   : 'paytmmp://pay',
        'bhim'    : 'upi://pay',
        'cred'    : 'credpay://upi/mandate',
        'amazon'  : 'amazonPay://pay',
        'mobikwik': 'mobikwik://pay',
        'freecharge': 'freecharge://pay',
    };

    /**
     * Convert a custom UPI scheme URL to an Android Intent URL
     * so WebView can launch the native app.
     * 
     * Input:  credpay://upi/mandate?mn=Autopay&...
     * Output: intent://upi/mandate?mn=Autopay&...#Intent;scheme=credpay;package=com.dreamplug.androidapp;end
     */
    function convertToIntentUrl(url) {
        var intentPackageMap = {
            'phonepe'     : 'com.phonepe.app',
            'tez'         : 'com.google.android.apps.nbu.paisa.user',
            'gpay'        : 'com.google.android.apps.nbu.paisa.user',
            'paytmmp'     : 'net.one97.paytm',
            'paytm'       : 'net.one97.paytm',
            'credpay'     : 'com.dreamplug.androidapp',
            'cred'        : 'com.dreamplug.androidapp',
            'bhim'        : 'in.org.npci.upiapp',
            'upi'         : 'in.org.npci.upiapp',
            'amazonpay'   : 'in.amazon.mShop.android.shopping',
            'mobikwik'    : 'com.mobikwik_new',
            'freecharge'  : 'com.accenture.freecharge',
        };

        try {
            // Extract scheme from URL (e.g., "credpay" from "credpay://...")
            var schemeMatch = url.match(/^([a-zA-Z][a-zA-Z0-9+\-.]*):\/\//);
            if (!schemeMatch) return null;

            var scheme = schemeMatch[1].toLowerCase();
            var packageName = intentPackageMap[scheme] || null;

            // Build the path+query part after scheme://
            var rest = url.substring(schemeMatch[0].length);

            // Build intent:// URL
            var intentUrl = 'intent://' + rest + '#Intent;scheme=' + scheme;
            if (packageName) {
                intentUrl += ';package=' + packageName;
            }
            intentUrl += ';end';

            return intentUrl;

        } catch(e) {
            return null;
        }
    }

    /**
     * Try to launch via intent:// URL
     */
    function tryLaunchIntent(url) {
        var intentUrl = convertToIntentUrl(url);
        if (intentUrl) {
            setStatus('Launching payment app...');
            window.location.href = intentUrl;
            return true;
        }
        return false;
    }

    /**
     * Main auto-launch function called on page load
     */
    function autoLaunch() {
        if (!PAYMENT_URL) {
            setStatus('Payment URL missing. Please go back and try again.');
            showOpenButton(RETURN_URL);
            return;
        }

        var url = PAYMENT_URL;
        var isCustomScheme = /^(?!https?:\/\/).+:\/\//.test(url);

        if (isCustomScheme) {
            // It's a custom scheme like credpay://, phonepe://, etc.
            setStatus('Opening UPI app...');
            var launched = tryLaunchIntent(url);
            if (launched) {
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'block';
                    showOpenButton(url);
                    setStatus('If app didn\'t open, tap button below ↓');
                }, 2500);
            } else {
                // Fallback: try direct
                window.location.href = url;
                setTimeout(function() {
                    showOpenButton(url);
                }, 2000);
            }
        } else if (url.startsWith('https://') || url.startsWith('http://')) {
            // Normal web URL (Mercury redirect from PhonePe)
            // Load it in current window - WebView will handle https:// fine
            setStatus('Loading payment page...');
            document.getElementById('spinner').style.display = 'block';
            window.location.href = url;
        } else {
            setStatus('Unknown URL format.');
            showOpenButton(RETURN_URL);
        }
    }

    /**
     * Manual launch button handler
     */
    function handleManualOpen() {
        var btn = document.getElementById('open-btn');
        var href = btn.getAttribute('data-href') || PAYMENT_URL;
        
        if (!href) return;

        var isCustomScheme = /^(?!https?:\/\/).+:\/\//.test(href);
        if (isCustomScheme) {
            var intentUrl = convertToIntentUrl(href);
            if (intentUrl) {
                window.location.href = intentUrl;
            } else {
                window.location.href = href;
            }
        } else {
            window.location.href = href;
        }
        return false;
    }

    /**
     * Launch specific UPI app manually
     */
    function launchApp(appName) {
        if (!PAYMENT_URL) return;

        // If the payment_url is already a custom scheme, use it
        var isCustomScheme = /^(?!https?:\/\/).+:\/\//.test(PAYMENT_URL);
        if (isCustomScheme) {
            tryLaunchIntent(PAYMENT_URL);
            return;
        }

        // If it's an HTTPS URL (PhonePe hosted checkout), just navigate
        window.location.href = PAYMENT_URL;
    }

    function setStatus(msg) {
        document.getElementById('status-msg').textContent = msg;
    }

    function showOpenButton(href) {
        var btn = document.getElementById('open-btn');
        btn.style.display = 'block';
        btn.setAttribute('data-href', href);
        document.getElementById('spinner').style.display = 'none';
    }

    // =========================================================
    // WebView Deep Link Interception via window.location
    // Handles cases where shouldOverrideUrlLoading is set up
    // =========================================================
    window.addEventListener('DOMContentLoaded', function() {
        // Auto-launch on page load
        setTimeout(autoLaunch, 800);

        // Listen for messages from Android native (if JS bridge exists)
        if (window.AndroidBridge) {
            try {
                window.AndroidBridge.onPaymentPageLoaded(PAYMENT_URL);
            } catch(e) {}
        }
    });

    // Expose to Android JS Interface
    window.getPaymentUrl = function() { return PAYMENT_URL; };
    window.getMtid = function() { return MTID; };
</script>
</body>
</html>
<?php
// Helper: base_url fallback if CI not loaded
if (!function_exists('base_url')) {
    function base_url($path = '') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $base = rtrim($base, '/');
        return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
    }
}
?>
