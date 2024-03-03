<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\composer\vendor\autoload.php'; // Adjust the path to your PHPMailer autoload file

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;									
	$mail->isSMTP();											
	$mail->Host	 = 'smtp-relay.brevo.com';					
	$mail->SMTPAuth = true;							
	$mail->Username = '4enmity@gmail.com';				
	$mail->Password = 'Q52JN1P96cbtarYf';						
	$mail->SMTPSecure = 'tls';							
	$mail->Port	 = 587;

	$mail->setFrom('4enmity@gmail.com', 'Kerim');		
	$mail->addAddress('ekerimkarahan@gmail.com');
			
	$mail->isHTML(true);								
	$mail->Subject = 'test email';
	$mail->Body =  'HTML message in a body <b> bold</b>';
	$mail->AltBody = 'Body in plain text for non-HTML mail clients';
	$mail->send();
	echo "Mail has been sent successfully!";
	} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
?>