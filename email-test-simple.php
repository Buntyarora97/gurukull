<?php
// Simple Email Test Script - No Password Required
// Use this file to test email delivery without SMTP configuration

// Test email configuration
function test_simple_email() {
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
    
    echo "<h1>Simple Email Delivery Test</h1>";
    echo "<p>Testing email delivery to: $test_email</p>";
    
    // Test PHP mail() function
    echo "<h3>Testing PHP mail() function...</h3>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Gurukull Contact Form <noreply@gurukull.com>" . "\r\n";
    $headers .= "Reply-To: test@gurukull.com" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    if (mail($test_email, $subject, $message, $headers)) {
        echo "<p style='color: green;'>✅ Email test successful! Check your inbox.</p>";
    } else {
        echo "<p style='color: red;'>❌ Email test failed. Check server configuration.</p>";
    }
    
    // Test second email
    echo "<h3>Testing second email...</h3>";
    $second_email = 'executive.gurukull@gmail.com';
    if (mail($second_email, $subject, $message, $headers)) {
        echo "<p style='color: green;'>✅ Second email test successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Second email test failed.</p>";
    }
    
    // Display server information
    echo "<h3>Server Information</h3>";
    echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
    echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
    echo "<p><strong>Mail Function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</p>";
    
    echo "<h3>Configuration Status</h3>";
    echo "<p><strong>SMTP:</strong> Not required (using PHP mail)</p>";
    echo "<p><strong>Email Recipients:</strong> gurukull.best@gmail.com, executive.gurukull@gmail.com</p>";
    echo "<p><strong>Setup:</strong> No password required - Direct email delivery</p>";
    
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>If you see green checkmarks above, your email is working!</li>";
    echo "<li>Test the contact form at <a href='contact.html'>contact.html</a></li>";
    echo "<li>All form submissions will be logged in contact_submissions.log</li>";
    echo "</ul>";
}

// Run the test
test_simple_email();
?>