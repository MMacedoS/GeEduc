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

    public function prepareMessageRecoveryPassword($user, $recovery) 
    {
        $message = '<h1>Olá! ' . $user->nome . ' </h1><p>Esta é o retorno de sua solicitação de recuperação de senha, por favor acesse o </p>';
        $message .= '</br> <a href="'.URL_PREFIX_APP.'/recuperar/'. $recovery .'">link de recuperação</a>';
        $message .= '</br></br>
        <p>Para mais informações entre en contato!!</p>
        </br></br>Att: Gestor Educacional - ' . URL_PREFIX_APP;

        return $message;
    }
}