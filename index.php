<?php

// Arquivo index responsável pela inicialização do sistema

// carregar o autoload do composer para gerenciar os carregamentos de classes

require './vendor/autoload.php';

// carregar a dependencia de gerenciamento de rotas
require './rotas.php';




/*
//classe email
use sistema\Suporte\Email;

$assunto = 'Email sistema Blog';
$conteudo = 'Este email foi enviado através do sistema unset blog desenvolvido em PHP MVC';
$destinatarioEmail = 'cbleal@bol.com.br';
$destinatatioNome = 'CBLeal Bol';
$remetenteEmail = 'cborgesleal@gmail.com';
$remetenteNome = 'CBorgesLeal Gmail';
$responderEmail = 'cborgesleal@hotmail.com';
$responderNome = 'Email Resposta';

try {
    $email = new Email();

    $email->criar(
        $assunto, 
        $conteudo, 
        $destinatarioEmail, 
        $destinatatioNome,
        $responderEmail,
        $responderNome,
    );

    $email->anexar('./uploads/texto.txt');
    $email->anexar('./uploads/filepdf.pdf', 'GIT-PDF');

    $email->enviar($remetenteEmail, $remetenteNome);

    echo "Email enviado com sucesso";

} catch (Exception $e) {
    echo $e->getMessage();
}

*/


/*
// e-mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;              //Enable verbose debug output
    $mail->isSMTP();                                    //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';           //Set the SMTP server to send through
    // $mail->Host       = 'mail.cblinf.com.br';           //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                           //Enable SMTP authentication
    $mail->Username   = 'cborgesleal@gmail.com';    //SMTP username
    // $mail->Username   = '_mainaccount@cblinf.com.br';    //SMTP username
    $mail->Password   = 'iqejiooyxtemamix';                   //SMTP password
    // $mail->Password   = 'p2HAkE$Xu#';                   //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    //Enable implicit TLS encryption
    // $mail->Port       = 465;                            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->Port       = 465;

    //Recipients
    $mail->setFrom('cborgesleal@gmail.com', 'CBLinf');
    $mail->addAddress('cborgesleal@hotmail.com', 'Borges Leal');     //Add a recipient
    
    
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Teste Envio E-mail pelo Gmail';
    $mail->Body    = '<strong>E-mail enviado utilizando uma conta do Gmail.</strong>';

    $mail->send();
    echo 'E-mail enviado com sucesso';
} catch (Exception $e) {
    echo "E-mail não foi enviado: Erro {$mail->ErrorInfo}";
}
*/

/*
// paginação
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?? 1;
echo $pagina;
echo "<hr>";

$limite = 5;
$offset = ($pagina - 1) * $limite;

// $posts = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28];

use sistema\Modelo\PostModelo;
$posts = new PostModelo();

// $paginar = array_slice($posts, $offset, $limite);
$paginar = $posts->busca()->limite($limite)->offset($offset)->resultado(true);

// foreach ($paginar as $post) {
//     echo $post . "<br>";
// }

foreach ($paginar as $post) {
    echo $post->id . ' ' . $post->titulo . '<br>';
}

echo "<hr>";

$total = $posts->busca(null, "COUNT(id)", "id")->total();
$total = ceil($total / $limite);
// $total = ceil(count($posts) / $limite);

echo "Página {$pagina} de {$total}";

echo "<hr>";

if ($pagina > 1) {
    echo '<a href="?pagina='.($pagina - 1).'">Anterior </a>';
}

$inicio = max(1, $pagina - 2);
$fim = min($total, $pagina + 3);

for ($i=$inicio; $i <=$fim ; $i++) { 
    if ($i == $pagina) {
        echo $i . ' ';
    } else {
        echo '<a href="?pagina='.$i.'">'.$i.' '.'</a>';
    }
}

if ($pagina < $total) {
    echo '<a href="?pagina='.($pagina + 1).'"> Próximo</a>';
}
*/

/*
//upload
use sistema\Biblioteca\Upload;

if (! empty($arquivo = $_FILES)) {
    $arquivo = $_FILES['arquivo'];
    $upload = new Upload();
    $upload->arquivo($arquivo, null, 'imagens');
    if ($upload->getResultado()):
        echo "Upload do arquivo {$upload->getResultado()} realizado com sucesso.";
    else:
        echo $upload->getErro();
    endif;
    r($upload);
}

echo '<hr>';
*/

/*
if (!empty($arquivo = $_FILES)) {
    $handle = new \Verot\Upload\Upload($_FILES['arquivo']);
    if ($handle->uploaded) {
        $handle->file_new_name_body   = 'image_resized';
        $handle->process('uploads/images/');

        $handle->image_resize         = true;
        $handle->image_x              = 100;
        $handle->image_ratio_y        = true;
        $handle->process('uploads/images/thumbs/');
        if ($handle->processed) {
            echo 'image resized';
            $handle->clean();
        } else {
            echo 'error : ' . $handle->error;
        }
    }
}
*/
