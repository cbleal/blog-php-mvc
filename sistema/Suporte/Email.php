<?php

namespace sistema\Suporte;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class Email
{
    private PHPMailer $mail;
    private array $anexos;

    public function __construct()
    {
        //Create an instance; passing `true` enables exceptions
        $this->mail = new PHPMailer(true);

                            //Server settings

        //Enable verbose debug 
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

        //Send using SMTP
        $this->mail->isSMTP();

        //Set the SMTP server to send through
        $this->mail->Host = EMAIL_HOST;

        //Enable SMTP authentication
        $this->mail->SMTPAuth = true;

        //SMTP username
        $this->mail->Username = EMAIL_USERNAME;

        //SMTP password
        $this->mail->Password = EMAIL_PASSWORD;

        //Enable implicit TLS encryption
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        //TCP port to connect to;
        $this->mail->Port = EMAIL_PORT;

        //Set the language for error messages 
        $this->mail->setLanguage('pt-br');

        //The character set of the message
        $this->mail->CharSet = 'utf-8';

        //Enable HTML mode
        $this->mail->isHTML(true);

        //Instanciar o atributo array $anexos
        $this->anexos = [];
    }

    public function criar(
        string $assunto,
        string $conteudo,
        string $destinatarioEmail,
        string $destinatarioNome,
        ?string $responderEmail = null,
        ?string $responderNome = null,
        ): static
    {
        $conteudo .= $this->rodape();

        //Add recipient
        $this->mail->Subject = $assunto;
        $this->mail->Body = $conteudo;
        $this->mail->isHTML($conteudo);
        $this->mail->addAddress($destinatarioEmail, $destinatarioNome);

        if ($responderEmail !== null && $responderNome !== null) {
            $this->mail->addReplyTo($responderEmail, $responderNome);
        }

        return $this;
    }

    public function enviar(string $remententeEmail, string $remetenteNome): bool
    {
        try {
            //Recipient
            $this->mail->setFrom($remententeEmail, $remetenteNome);

            //Percorrer os anexos e adicionar ao mail (addAttachment)
            foreach ($this->anexos as $anexo) {
                $this->mail->addAttachment($anexo['caminho'], $anexo['nome']);
            }

            //Send
            $this->mail->send();

            return true;
        } catch (Exception $e) {
            $e = $this->mail->ErrorInfo;
            return false;
        }
    }

    /**
     * @param string $caminho Caminho do arquivo
     * @param string $nome Nome do arquivo
     */
    public function anexar(string $caminho, ?string $nome = null): static
    {      
        $nome = $nome ?? basename($caminho);
        $this->anexos[] = [
            'caminho' => $caminho,
            'nome'    => $nome
        ];        
        return $this;
    }

    public function rodape(): string
    {
        return "<hr><small>Enviado por ".SITE_NOME."em ".date('d/m/Y')."às ".date('H:i')."</small>";
    }
}