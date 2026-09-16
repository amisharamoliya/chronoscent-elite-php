<?php
// Dummy Mailer Utility for Demonstration purposes
// In a real environment, you would use PHPMailer here.
// require 'vendor/autoload.php';
// use PHPMailer\PHPMailer\PHPMailer;

function send_email($to, $subject, $body) {
    // Simulated delay
    // sleep(1);
    
    /* 
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com';
        $mail->Password   = 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('noreply@chronoscent.com', 'Chronoscent');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
    */
    
    // Log the simulated email to a file for testing
    $log_entry = "[" . date('Y-m-d H:i:s') . "] Sent to: $to | Subject: $subject | Body: $body\n";
    file_put_contents(__DIR__ . '/../mail_log.txt', $log_entry, FILE_APPEND);
    return true;
}
?>
