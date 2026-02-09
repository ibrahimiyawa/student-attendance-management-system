<?php
require_once 'includes/mailer.php';

if (sendAbsentNotification(
    'Test Student', 
    'parent@test.com', 
    date('Y-m-d'), 
    'Test absence'
)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}