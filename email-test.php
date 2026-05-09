<?php
// Email Test Script for Hostinger
// Use this file to test email delivery configuration

require_once 'smtp-config.php';

// Test email configuration
function test_email_delivery() {
    $test_email = 'gurukull.best@gmail.com';
    $subject = 'Test Email from Gurukull Contact Form';
    $message = '
    <html>
    <body>
        <h2>Email Test Successful!</h2>
        <p>This is a test email from your Gurukull contact form.</p>
        <p>If you received this email, your email configuration is working properly.</p>
        <p>Timestamp: ' . date('Y-m-d H:i:s') . '</p>
    </body>
    </html>
    ';
    
    $test_data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '9999999999',
        'course' => 'digital-marketing-ai',
        'message' => 'This is a test message to verify email delivery.'
    ];
    
    echo "<h1>Email Delivery Test</h1>";
    echo "<p>Testing email delivery to: $test_email</p>";
    
    // Test SMTP
    echo "<h3>Testing SMTP...</h3>";
    if (function_exists('send_smtp_email')) {
        try {
            $smtp_result = send_smtp_email([$test_email], $subject, $message, 'test@example.com', 'Test User');
            if ($smtp_result) {
                echo "<p style='color: green;'>✅ SMTP test successful!</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ SMTP test failed, but this is expected if SMTP credentials are not configured.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ SMTP test error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ SMTP function not available</p>";
    }
    
    // Test PHP mail()
    echo "<h3>Testing PHP mail()...</h3>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Gurukull Test <noreply@" . $_SERVER['HTTP_HOST'] . ">" . "\r\n";
    $headers .= "Reply-To: test@example.com" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    if (mail($test_email, $subject, $message, $headers)) {
        echo "<p style='color: green;'>✅ PHP mail() test successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ PHP mail() test failed</p>";
    }
    
    // Display server information
    echo "<h3>Server Information</h3>";
    echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
    echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
    echo "<p><strong>Mail Function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</p>";
    
    // Check if sendmail is available
    if (function_exists('exec')) {
        $sendmail_path = exec('which sendmail 2>/dev/null');
        echo "<p><strong>Sendmail Path:</strong> " . ($sendmail_path ? $sendmail_path : 'Not found') . "</p>";
    }
    
    echo "<h3>Environment Variables</h3>";
    $env_vars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_SECURE'];
    foreach ($env_vars as $var) {
        $value = getenv($var);
        if ($var === 'SMTP_PASSWORD') {
            $value = $value ? '[SET]' : '[NOT SET]';
        }
        echo "<p><strong>$var:</strong> " . ($value ? $value : 'Not set') . "</p>";
    }
}

// Run the test
test_email_delivery();
?>
