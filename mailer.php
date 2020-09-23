<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Load Composer's autoloader
require 'vendor/autoload.php';

mysqli_set_charset($conn, "utf8");
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $obj['email'];
    $id = $obj['id'];

// Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'enduro.mobph@gmail.com'; // SMTP username
        $mail->Password = 'qbjijzblooeowgqj'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port = 587; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('enduro.mobph@gmail.com', 'Enduro Mailer');
        // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
        $mail->addAddress($email);               // Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        
        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Enduro Mailer';
        $mail->Body = '
    <!DOCTYPE html>
            <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" type="text/css">
            <title>Verify Now</title>
                </head>
                    <body style="background-color: #F0F2F5;  text-align: center; margin: auto; padding:  15% 10% 25% 10% !important; font-family: poppins;">
                        <div class="container-fluid main-container" style="margin: auto;">
                        <div class="inner-container" style="margin: auto; border-radius: 5px; background-color: #ffffff; text-align: center; padding: 5% 8% 9% 8%;">
                        <div class="title-container">
                            <h2 style="color: #618930; font-size: 25px;">ENDURO MTB PH</h2>
                        </div>
                        <div class="sub-title-container">
                        <h3 class="sub-title-container" style="margin-top: -5%; font-size: 16px;">
                         Email verification
                        </h3>
                        </div>
                        <div class="phrase-container">
                            <p class="p-phrase" style="color: gray; font-size: 15px;">
                            First we must confirm your email address to continue creating your account.
                            This is to ensure the ptotection of your data.
                            </p>
                         </div>
                        <a href=http://18.216.125.206/api/enduro_verify.php?id=' . $id . '>
                        <div class="sendVerification" style="margin-top: 7%; ">
                            <button class="btn-sendEmailVerification" style="background-color: #618930; color: #ffffff; font-size: 20px; width: 90%; padding-top: 2%; padding-bottom: 2%; border-radius: 7px; border-color: #618930; box-shadow: 0px;">
                                Verify Email
                            </button>
                        </div>
                        </a>
                    </div>

                    <div class="outer-texts" style="margin-top: 5%;">
                        <small>
                            &copy; Enduro MTB PH Dev Team 2020
                        </small>
                    </div>
                </div>
            </body>
            </html>
    ';
        $mail->send();

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    die("Something went wrong");
}
