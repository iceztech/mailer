<?php

namespace ZikzayMail\Mailer;

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private static $attachments;
    private PHPMailer $mail;
    private $sender;

    public function __construct(string $username, string $password, string $senderName = '', string $host = 'localhost')
    {
        $this->mail = new PHPMailer(true);
        $this->settings($host, $username, $password, $senderName);
    }

    private function settings($host, $username, $password, $senderName)
    {
        date_default_timezone_set('Etc/UTC'); // Enable verbose debug output
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->Hostname = 'localhost.localdomain';
        $this->mail->SMTPAuth = true;
        $this->mail->Priority = 1;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = 465;
        $this->mail->isHTML(true);

        $this->setSender($username, $senderName);
    }

    public function setSender(string $email, string $senderName = '')
    {
        try {
            $this->mail->setFrom($email, $senderName);
        } catch (Exception $e) {
        }
        return $this;
    }

    // private function prepare($to, $subject, $message, $cc = null, $bcc = null)
    // {
    //     $this->mail->Subject = $subject;
    //     $this->mail->Body = $message;
    //     $this->mail->AltBody = $message;
    //     try {
    //         $this->mail->sendTo($to);
    //         if ($cc) $this->mail->addCC($cc);
    //         if ($bcc) $this->mail->addBCC($bcc);
    //         return parent::send();
    //     } catch (Exception $e) {
    //         return false;
    //     }
    // }

    // public static function message($to, $subject, $message, $cc = null, $bcc = null)
    // {
    //     $self = new self;
    //     $self->getAttachment(self::$attachments);
    //     return $self->prepare($to, $subject, $message, $cc, $bcc);
    // }

    public static function attachments($file, $fileName = null)
    {
        self::$attachments[] = ['file' => $file, 'fileName' => $fileName];
    }

    /**
     * @param $to
     * @throws Exception
     */
    private function sendTo($to)
    {

        if (is_array($to)) {
            foreach ($to as $name => $email) {
                $this->mail->addAddress($email, $name);
                $this->mail->addReplyTo($email, $name);
            }
        } else {
            $this->mail->addAddress($this->mail->Username);
            $this->mail->addAddress($to);
            $this->mail->addReplyTo($to);
        }
    }

    public function setAttachment(array $filePath, $fileName = '')
    {
        try {
            $this->mail->addAttachment($filePath, $fileName);
        } catch (Exception $e) {
        }

        return $this;
    }

    public function send($to, $subject, $message, $cc = null, $bcc = null)
    {
        try {
            $this->sendTo($to);
            if ($cc) $this->mail->addCC($cc);
            if ($bcc) $this->mail->addBCC($bcc);
            //Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $message;
            $this->mail->AltBody = $message;

            $return = $this->mail->send();
            dnd($return);
        } catch (Exception $e) {
            return false;
        }
    }


    public static function sendMail($host, $username, $password, $senderName, array $mail)
    {
        $self = new self($username, $password, $senderName, $host);

        $self->setSender($username, $senderName);
        if (isset($mail['attach'])) {
            $self->setAttachment($mail['attach']['filePath'], $mail['attach']['fileName']);
        }
        $self->send($mail['to'], $mail['subject'], $mail['message']);
    }
}

function dnd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
