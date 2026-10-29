<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {

    // Gmail SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // ==========================================
    // CHANGE THESE TWO VALUES
    // ==========================================

    $mail->Username = 'sonr84199@gmail.com';

    $mail->Password = 'Vishnukv@*#1234';

    // ==========================================

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Sender
    $mail->setFrom(
        'sonr84199@gmail.com',
        'MediBook'
    );

    // ==========================================
    // SEND TEST EMAIL TO YOUR EMAIL
    // ==========================================

    $mail->addAddress(
        'sonr84199@gmail.com'
    );

    $mail->isHTML(true);

    $mail->Subject = 'MediBook Email Test';

    $mail->Body = '
        <div style="font-family:Arial;padding:25px;">
            <h2 style="color:#159d94;">🩺 MediBook</h2>

            <h3>Email Notification Test</h3>

            <p>
                Congratulations! Your MediBook email
                notification system is working successfully.
            </p>

            <p>
                This is a test email sent using Gmail SMTP
                and PHPMailer.
            </p>

            <hr>

            <p style="color:#777;">
                MediBook Hospital Appointment System
            </p>
        </div>
    ';

    $mail->AltBody =
        'MediBook email notification system is working successfully.';

    $mail->send();

    echo "
        <div style='font-family:Arial;padding:30px;'>
            <h2 style='color:green;'>✅ Email Sent Successfully!</h2>
            <p>Check your Gmail inbox.</p>
        </div>
    ";

} catch (Exception $e) {

    echo "
        <div style='font-family:Arial;padding:30px;'>
            <h2 style='color:red;'>❌ Email Failed</h2>
            <p>{$mail->ErrorInfo}</p>
        </div>
    ";
}