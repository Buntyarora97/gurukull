<?php
// SMTP Configuration for Hostinger
// This file contains SMTP settings for reliable email delivery

// SMTP settings - आपको बस अपना email और password यहाँ डालना है
$smtp_host = 'smtp.hostinger.com';
$smtp_port = 587;
$smtp_username = ''; // यहाँ अपना email address डालें जैसे: contact@yourdomain.com
$smtp_password = ''; // यहाँ अपना email password डालें
$smtp_secure = 'tls';

// Function to send email using SMTP
function send_smtp_email($to_emails, $subject, $message, $reply_to_email, $reply_to_name = '') {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_secure;
    
    // If SMTP credentials are not configured, return false to use fallback
    if (empty($smtp_username) || empty($smtp_password)) {
        return false;
    }
    
    // Create a socket connection
    $smtp_connection = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
    
    if (!$smtp_connection) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }
    
    // Read server response
    $response = fgets($smtp_connection, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("SMTP server not ready: $response");
        fclose($smtp_connection);
        return false;
    }
    
    // Send EHLO command
    fputs($smtp_connection, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = fgets($smtp_connection, 515);
    
    // Start TLS if required
    if ($smtp_secure === 'tls') {
        fputs($smtp_connection, "STARTTLS\r\n");
        $response = fgets($smtp_connection, 515);
        if (substr($response, 0, 3) != '220') {
            error_log("STARTTLS failed: $response");
            fclose($smtp_connection);
            return false;
        }
        
        // Enable crypto
        if (!stream_socket_enable_crypto($smtp_connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log("TLS encryption failed");
            fclose($smtp_connection);
            return false;
        }
        
        // Send EHLO again after TLS
        fputs($smtp_connection, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = fgets($smtp_connection, 515);
    }
    
    // Authenticate
    fputs($smtp_connection, "AUTH LOGIN\r\n");
    $response = fgets($smtp_connection, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("AUTH LOGIN failed: $response");
        fclose($smtp_connection);
        return false;
    }
    
    // Send username
    fputs($smtp_connection, base64_encode($smtp_username) . "\r\n");
    $response = fgets($smtp_connection, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("Username authentication failed: $response");
        fclose($smtp_connection);
        return false;
    }
    
    // Send password
    fputs($smtp_connection, base64_encode($smtp_password) . "\r\n");
    $response = fgets($smtp_connection, 515);
    if (substr($response, 0, 3) != '235') {
        error_log("Password authentication failed: $response");
        fclose($smtp_connection);
        return false;
    }
    
    // Send email to each recipient
    $success = true;
    foreach ($to_emails as $to_email) {
        // Set sender
        fputs($smtp_connection, "MAIL FROM: <$smtp_username>\r\n");
        $response = fgets($smtp_connection, 515);
        if (substr($response, 0, 3) != '250') {
            error_log("MAIL FROM failed: $response");
            $success = false;
            continue;
        }
        
        // Set recipient
        fputs($smtp_connection, "RCPT TO: <$to_email>\r\n");
        $response = fgets($smtp_connection, 515);
        if (substr($response, 0, 3) != '250') {
            error_log("RCPT TO failed for $to_email: $response");
            $success = false;
            continue;
        }
        
        // Send data command
        fputs($smtp_connection, "DATA\r\n");
        $response = fgets($smtp_connection, 515);
        if (substr($response, 0, 3) != '354') {
            error_log("DATA command failed: $response");
            $success = false;
            continue;
        }
        
        // Send headers and message
        $headers = "From: Gurukull Contact Form <$smtp_username>\r\n";
        $headers .= "Reply-To: $reply_to_name <$reply_to_email>\r\n";
        $headers .= "To: <$to_email>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "\r\n";
        
        fputs($smtp_connection, $headers . $message . "\r\n.\r\n");
        $response = fgets($smtp_connection, 515);
        if (substr($response, 0, 3) != '250') {
            error_log("Message sending failed for $to_email: $response");
            $success = false;
        }
    }
    
    // Quit
    fputs($smtp_connection, "QUIT\r\n");
    fclose($smtp_connection);
    
    return $success;
}

// Alternative PHPMailer-like function for systems with PHPMailer installed
function send_email_phpmailer($to_emails, $subject, $message, $reply_to_email, $reply_to_name = '') {
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return false;
    }
    
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_secure;
    
    if (empty($smtp_username) || empty($smtp_password)) {
        return false;
    }
    
    try {
        // Include PHPMailer classes
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port       = $smtp_port;
        
        // Recipients
        $mail->setFrom($smtp_username, 'Gurukull Contact Form');
        $mail->addReplyTo($reply_to_email, $reply_to_name);
        
        foreach ($to_emails as $to_email) {
            $mail->addAddress($to_email);
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer error: " . $e->getMessage());
        return false;
    }
}
?>
