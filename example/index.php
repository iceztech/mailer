<?php

use ZikzayMail\Mailer\Mailer;

use function ZikzayMail\Mailer\dnd;

require 'src/Mailer.php';

$host = 'iceztech.com';
$username = 'isaac@iceztech.com';
$password = 'MailerPassword@123';
$subject = 'Test Subject';
$body = '<h1>Title</h1><p>The body of the message of the email</p>';
$to = 'zikzay@gmail.com';

// $send = Mailer::sendMail($host, $username, $password, 'Text Mailer', ['to' => $to, 'subject' => $subject, 'message' => $body]);
// var_dump($send);

$mail = new Mailer($username, $password, 'Iceztech', $host);
$snd = $mail->send($to, $subject, $message);
dnd($send);
