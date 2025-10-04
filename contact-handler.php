<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $number = htmlspecialchars($_POST['number']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA with cURL
    $secret = "6LdnULYrAAAAAAFstqiE_rPpblOS8O2P4Q8jCkhh";
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $secret, 'response' => $recaptcha_response]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        die("reCAPTCHA failed. Please try again.");
    }

    $mail = new PHPMailer(true);
    try {
        // SMTP settings (Mailtrap Example)
        $mail->isSMTP();
        $mail->Host = 'mail.rajbariit.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@rajbariit.com';
        $mail->Password = 'aa11bb22AA11BB22';
        $mail->Port = 587;

        // Gmail Example (uncomment if needed)
        /*
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = 'YOUR_GMAIL_ADDRESS';
        $mail->Password = 'YOUR_GMAIL_APP_PASSWORD';
        */

        $mail->setFrom($email, $name);
        $mail->addAddress("admin@rajbariit.com", "Admin");

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message";
        $mail->Body = "     <b>Name:</b> $name <br>
                            <b>Phone:</b> $number <br>
                            <b>Email:</b> $email <br>
                            <b>Subject:</b> $subject <br>
                            <b>Message:</b> $message";

        $mail->send();
        header("Location: success.html");
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
