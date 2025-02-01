<?php
namespace App\Services;

use App\Utils\LoggerHelper;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    private $config;

    public function __construct()
    {
        $this->config = CONFIG_EMAIL;
    }

    public function sendEmail(string $to, string $subject, string $message): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['port'];

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            LoggerHelper::logInfo("Erro ao enviar e-mail: " . $mail->ErrorInfo);
            return false;
        }
    }
}