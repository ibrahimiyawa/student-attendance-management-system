<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendAbsentNotification($studentName, $parentEmail, $date, $reason = '') {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // For Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'drabba68@gmail.com'; // Your email
        $mail->Password   = 'jsjc sods apwm kopv';    // App password (not regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('your.email@gmail.com', 'College Attendance System');
        $mail->addAddress($parentEmail);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Attendance Notification: ' . $studentName . ' Absent';
        
        $message = "<h3>Attendance Notification</h3>
                   <p>Dear Parent/Guardian,</p>
                   <p>Your child <strong>$studentName</strong> was marked <strong>absent</strong> on $date.</p>";
        
        if (!empty($reason)) {
            $message .= "<p>Reason: $reason</p>";
        }
        
        $message .= "<p>Please contact the school if you believe this is an error.</p>
                    <p>Sincerely,<br>School Administration</p>";
        
        $mail->Body = $message;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}