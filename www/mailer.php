<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';
            if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'ethanespineli04@gmail.com';                     //SMTP username
        $mail->Password   = 'oife cuxg sfst ydgj';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('ethanespineli04@gmail.com', 'Ethan'); // Sender
        $mail->addAddress($_POST['email'], '');     //Add a recipient
        $mail->addReplyTo('ethanespineli04@gmail.com', 'Information');
        //$mail->addBCC('bcc@example.com');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Order Pickup';
        $mail->Body  = "<h1> This message was sent to " . $_POST['email'] . "<h1>";

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    header("location: receipt.php");
}
// DB Connect test_user - password
?>