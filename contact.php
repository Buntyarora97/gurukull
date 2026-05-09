<?php
// No SMTP configuration needed - using direct PHP mail()

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors and messages
function log_message($message, $type = 'INFO') {
    $log_entry = date('Y-m-d H:i:s') . " [$type] - " . $message . "\n";
    file_put_contents('contact_debug.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone (10 digits)
function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to validate name (only letters and spaces)
function validate_name($name) {
    return preg_match('/^[a-zA-Z\s]+$/', $name);
}

// Function to check for spam keywords
function check_spam($message) {
    $spam_keywords = ['viagra', 'casino', 'lottery', 'winner', 'prize', 'congratulations', 'million', 'bitcoin'];
    $message_lower = strtolower($message);
    
    foreach ($spam_keywords as $keyword) {
        if (strpos($message_lower, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

// Function to send email directly using PHP mail()
function send_email_direct($data) {
    log_message("Attempting to send email via PHP mail()", "INFO");
    
    $to_emails = ['gurukull.best@gmail.com', 'executive.gurukull@gmail.com'];
    
    $course_names = [
        'digital-marketing-ai' => 'Digital Marketing with AI',
        'web-designing' => 'Web Designing',
        'multimedia' => 'Multimedia',
        'diploma' => '1 Year Diploma'
    ];
    
    $course_name = $course_names[$data['course']] ?? $data['course'];
    
    $subject = "New Contact Form Submission - " . $course_name;
    
    $message = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #2c3e50, #3498db); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .info-table { width: 100%; border-collapse: collapse; background: white; }
            .info-table td { padding: 10px; border-bottom: 1px solid #eee; }
            .info-table .label { font-weight: bold; color: #2c3e50; }
            .message-box { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #3498db; margin-top: 20px; }
            .footer { color: #666; font-size: 14px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Contact Form Submission</h1>
                <p>Gurukull Infosystems</p>
            </div>
            
            <div class='content'>
                <h2 style='color: #2c3e50;'>Contact Details</h2>
                
                <table class='info-table'>
                    <tr>
                        <td class='label'>Name:</td>
                        <td>" . htmlspecialchars($data['name']) . "</td>
                    </tr>
                    <tr>
                        <td class='label'>Email:</td>
                        <td>" . htmlspecialchars($data['email']) . "</td>
                    </tr>
                    <tr>
                        <td class='label'>Phone:</td>
                        <td>" . htmlspecialchars($data['phone']) . "</td>
                    </tr>
                    <tr>
                        <td class='label'>Course:</td>
                        <td>" . htmlspecialchars($course_name) . "</td>
                    </tr>
                </table>
                
                <div class='message-box'>
                    <h3 style='color: #2c3e50; margin-bottom: 10px;'>Message:</h3>
                    <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                </div>
                
                <div class='footer'>
                    <p>Submitted on: " . date('Y-m-d H:i:s') . "</p>
                    <p>Please respond within 24 hours as promised.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Use PHP mail() directly with proper headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Gurukull Contact Form <noreply@gurukull.com>" . "\r\n";
    $headers .= "Reply-To: " . $data['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Return-Path: noreply@gurukull.com" . "\r\n";
    
    $success = true;
    foreach ($to_emails as $to_email) {
        log_message("Attempting to send email to: " . $to_email, "INFO");
        if (!mail($to_email, $subject, $message, $headers)) {
            $success = false;
            log_message("Failed to send email to: " . $to_email, "ERROR");
        } else {
            log_message("Email sent successfully to: " . $to_email, "SUCCESS");
        }
    }
    
    return $success;
}

// Main processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        log_message("Form submission received", "INFO");
        
        // Get form data
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $course = sanitize_input($_POST['course'] ?? '');
        $message = sanitize_input($_POST['message'] ?? '');
        $terms = isset($_POST['terms']) ? $_POST['terms'] : '';
        $honeypot = sanitize_input($_POST['honeypot'] ?? '');
        
        log_message("Processing form data for: " . $name . " (" . $email . ")", "INFO");
        
        // Check honeypot for spam
        if (!empty($honeypot)) {
            log_message("Spam detected via honeypot", "WARNING");
            echo json_encode(['status' => 'error', 'message' => 'Spam detected']);
            exit;
        }
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($course) || empty($message) || empty($terms)) {
            log_message("Missing required fields", "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields']);
            exit;
        }
        
        // Validate name
        if (!validate_name($name)) {
            log_message("Invalid name format: " . $name, "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Name must contain only letters and spaces']);
            exit;
        }
        
        // Validate email
        if (!validate_email($email)) {
            log_message("Invalid email format: " . $email, "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address']);
            exit;
        }
        
        // Validate phone
        if (!validate_phone($phone)) {
            log_message("Invalid phone format: " . $phone, "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Phone number must be exactly 10 digits']);
            exit;
        }
        
        // Validate course
        $valid_courses = ['digital-marketing-ai', 'web-designing', 'multimedia', 'diploma'];
        if (!in_array($course, $valid_courses)) {
            log_message("Invalid course selection: " . $course, "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Please select a valid course']);
            exit;
        }
        
        // Check message length
        if (strlen($message) < 10) {
            log_message("Message too short", "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Message must be at least 10 characters']);
            exit;
        }
        
        // Check for spam
        if (check_spam($message)) {
            log_message("Spam keywords detected in message", "WARNING");
            echo json_encode(['status' => 'error', 'message' => 'Message contains inappropriate content']);
            exit;
        }
        
        // Prepare data for email
        $form_data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'course' => $course,
            'message' => $message
        ];
        
        // Save to file (for backup)
        $log_entry = date('Y-m-d H:i:s') . " - " . json_encode($form_data) . "\n";
        file_put_contents('contact_submissions.log', $log_entry, FILE_APPEND | LOCK_EX);
        
        // Send email
        if (send_email_direct($form_data)) {
            log_message("Email sent successfully", "SUCCESS");
            echo json_encode(['status' => 'success', 'message' => 'Thank you for your message! We will get back to you within 24 hours.']);
        } else {
            log_message("Failed to send email", "ERROR");
            echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again or contact us directly at gurukull.best@gmail.com']);
        }
        
    } catch (Exception $e) {
        log_message("Exception occurred: " . $e->getMessage(), "ERROR");
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again later.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed']);
}
?>
