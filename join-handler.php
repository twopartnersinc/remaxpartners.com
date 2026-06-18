<?php
// REMAX Partners - Join Our Team Form Handler
// Sends applications to sjjpartnersinc@gmail.com

$to = 'sjjpartnersinc@gmail.com';

function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

$first_name      = clean($_POST['first_name'] ?? '');
$last_name       = clean($_POST['last_name'] ?? '');
$email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone           = clean($_POST['phone'] ?? '');
$license_status  = clean($_POST['license_status'] ?? '');
$dre             = clean($_POST['dre'] ?? '');
$experience      = clean($_POST['experience'] ?? '');
$message         = clean($_POST['message'] ?? '');

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: join.html?error=1');
    exit;
}

$subject = 'New Join Our Team Application - REMAX Partners';

$body  = "New agent application from remaxpartners.com\n";
$body .= "============================================\n\n";
$body .= "Name:            $first_name $last_name\n";
$body .= "Email:           $email\n";
$body .= "Phone:           $phone\n";
$body .= "License Status:  $license_status\n";
$body .= "CA DRE #:        $dre\n";
$body .= "Experience:      $experience\n\n";
$body .= "About Themselves:\n$message\n\n";
$body .= "============================================\n";
$body .= "Submitted: " . date('Y-m-d H:i:s T') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers  = "From: REMAX Partners Website <noreply@remaxpartners.com>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    header('Location: join.html?submitted=1');
} else {
    header('Location: join.html?error=1');
}
exit;
?>
