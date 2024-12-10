<?php
// require_once('class.phpmailer.php');
// require_once('class.smtp.php');

// /**
//  * Funkcja wysyłająca e-mail z tokenem do resetu hasła.
//  *
//  * @param string $toEmail Adres e-mail odbiorcy.
//  * @param string $token Token do resetu hasła.
//  * @return bool Zwraca true, jeśli wysłanie się powiodło, false w przeciwnym razie.
//  */
// function sendPasswordResetEmail($toEmail, $token) {
//     $mail = new PHPMailer();

//     // Ustawienia SMTP
//     $mail->isSMTP();
//     $mail->Host = "poczta.o2.pl"; 
//     $mail->SMTPAuth = true; 
//     $mail->Username = "bartekryba.2001@o2.pl"; 
//     $mail->Password = "aaa"; 
//     $mail->SMTPSecure = 'ssl'; 
//     $mail->Port = 465; 

//     // Nadawca i odbiorca
//     $mail->setFrom("bartekryba.2001@o2.pl", "AdvertiseSupport");
//     $mail->addAddress($toEmail);

//     // Treść wiadomości
//     $resetLink = "http://localhost/reset_password.php?token=" . urlencode($token);
//     $mail->Subject = "Password recovery";
//     $mail->Body = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";
//     $mail->AltBody = "Reset your password using this link: $resetLink";

//     // Wyślij wiadomość
//     return $mail->send();
// }
?>
