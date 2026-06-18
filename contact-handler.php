<?php
// REMAX Partners - Contact Form Handler
// Sends form submissions to sjjpartnersinc@gmail.com

$to = 'slrotondo@earthlink.net';

// Sanitize inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

$first_name  = clean($_POST['first_name'] ?? '');
$last_name   = clean($_POST['last_name'] ?? '');
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone       = clean($_POST['phone'] ?? '');
$interest    = clean($_POST['interest'] ?? '');
$message     = clean($_POST['message'] ?? '');

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=1');
    exit;
}

$subject = 'New Contact Form Submission - REMAX Partners';

$body  = "New contact form submission from remaxpartners.com\n";
$body .= "================================================\n\n";
$body .= "Name:      $first_name $last_name\n";
$body .= "Email:     $email\n";
$body .= "Phone:     $phone\n";
$body .= "Interest:  $interest\n\n";
$body .= "Message:\n$message\n\n";
$body .= "================================================\n";
$body .= "Submitted: " . date('Y-m-d H:i:s T') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers  = "From: REMAX Partners Website <noreply@remaxpartners.com>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    header('Location: contact.html?sent=1');
} else {
    header('Location: contact.html?error=1');
}
exit;
?>
