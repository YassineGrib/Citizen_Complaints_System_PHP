<?php
/**
 * Email Configuration
 * 
 * This file contains the email settings for the Citizen Complaints System.
 * It uses PHPMailer for sending emails.
 */

// PHPMailer settings
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'complaints@example.com');
define('MAIL_PASSWORD', 'your-password');
define('MAIL_FROM', 'complaints@example.com');
define('MAIL_FROM_NAME', 'Citizen Complaints System');
define('MAIL_ENCRYPTION', 'tls');

// Function to send email using PHPMailer
function send_email($to, $subject, $body, $attachments = []) {
    // Check if PHPMailer is installed
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer is not installed. Email not sent.');
        return false;
    }
    
    // Include PHPMailer
    require 'vendor/autoload.php';
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;
        
        // Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        
        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
