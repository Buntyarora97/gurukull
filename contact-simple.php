<?php
// === contact-simple.php ===
// Direct form to email script using mail() with validation, anti-spam

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

error_reporting(0);
ini_set('display_errors', 0);

// Log function
function log_entry($msg, $type = 'INFO') {
    file_put_contents('contact_debug.log', date('Y-m-d H:i:s') . " [$type] - $msg\n", FILE_APPEND | LOCK_EX);
}

// Validators
function valid_name($name) {
    return preg_match("/^[a-zA-Z\s]+$/", $name);
}
function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function valid_phone($phone) {
    return preg_match("/^[0-9]{10}$/", $phone);
}

// Input values
$name     = htmlspecialchars(trim($_POST['name'] ?? ''));
$email    = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone    = htmlspecialchars(trim($_POST['phone'] ?? ''));
$course   = htmlspecialchars(trim($_POST['course'] ?? ''));
$message  = htmlspecialchars(trim($_POST['message'] ?? ''));
$terms    = $_POST['terms'] ?? '';
$honeypot = trim($_POST['honeypot'] ?? '');

// SPAM check
if (!empty($honeypot)) {
    log_entry("Spam blocked via honeypot", "SPAM");
    echo json_encode(['status' => 'error', 'message' => 'Spam detected.']); exit;
}

// Empty field validation
if (!$name || !$email || !$phone || !$course || !$message || !$terms) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']); exit;
}

// Validate fields
if (!valid_name($name)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid name. Only letters and spaces allowed.']); exit;
}
if (!valid_email($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']); exit;
}
if (!valid_phone($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Phone must be 10 digits (numbers only).']); exit;
}
if (strlen($message) < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Message too short. Minimum 10 characters.']); exit;
}

// Course Names Map
$courses = [
    'digital-marketing-ai' => 'Digital Marketing with AI',
    'web-designing'        => 'Web Designing',
    'multimedia'           => 'Multimedia',
    'diploma'              => '1 Year Diploma'
];
$course_name = $courses[$course] ?? $course;

// Email sending
$to = "gurukull.best@gmail.com,executive.gurukull@gmail.com";
$subject = "New Contact Form Submission – $course_name";

// Email body
$body = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <h2 style='color:#2c3e50;'>New Contact Form Submission</h2>
    <table cellpadding='8' cellspacing='0'>
        <tr><td><strong>Name:</strong></td><td>{$name}</td></tr>
        <tr><td><strong>Email:</strong></td><td>{$email}</td></tr>
        <tr><td><strong>Phone:</strong></td><td>{$phone}</td></tr>
        <tr><td><strong>Course:</strong></td><td>{$course_name}</td></tr>
        <tr><td><strong>Message:</strong></td><td>" . nl2br($message) . "</td></tr>
    </table>
    <br>
    <p style='color: #888;'>Submitted on: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>";

// Email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Gurukull Contact Form <noreply@gurukullinstitute.in>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
if (mail($to, $subject, $body, $headers)) {
    // Save log for backup
    $log_data = json_encode([
        'name'    => $name,
        'email'   => $email,
        'phone'   => $phone,
        'course'  => $course,
        'message' => $message
    ]);
    
    file_put_contents('contact_submissions.log', date('Y-m-d H:i:s') . " - $log_data\n", FILE_APPEND);
    log_entry("Email sent successfully to $to", "SUCCESS");

    echo json_encode(['status' => 'success', 'message' => '✅ Thank you! Your message has been received.']);
} else {
    log_entry("Failed to send to: $to", "ERROR");
    echo json_encode(['status' => 'success', 'message' => '⚠️ Form received. Email delivery pending.']);
}
?>
