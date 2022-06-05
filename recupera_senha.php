<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require('config/phpmailer/src/Exception.php');
require('config/phpmailer/src/PHPMailer.php');
require('config/phpmailer/src/SMTP.php');

require('config/conexao.php');

if(isset($_SESSION['TOKEN'])){
    
    $sql = $pdo -> prepare('SELECT * FROM usuarios WHERE token=?');
    $sql -> execute(array($_SESSION['TOKEN']));
    $usuario = $sql -> fetchAll(PDO::FETCH_ASSOC);

    if($usuario){

         header('location: paginas/dashboard.php');
    }
}


    if(isset($_POST['recupera-email']) && !empty($_POST['recupera-email'])){

        $email = limpaPost($_POST['recupera-email']);
        $status = 'confirmado';
        $cod = uniqid();
        $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? and _status=? LIMIT 1");
        $sql -> execute(array($email,$status));
        $usuario = $sql->fetch(PDO::FETCH_ASSOC);

        if($usuario){

            $sql = $pdo -> prepare("UPDATE usuarios SET recupera_senha=? where email=?");
            $sql -> execute(array($cod,$email));

            $mail = new PHPMailer(true);

            try{
                $mail->isSMTP(); 
                $mail->Host     = 'smtp.titan.email';
                $mail->SMTPAuth = true;
                $mail->Username = 'comercial@dehagencia.com';
                $mail->Password   = 'LvkdZJEmLn';   
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail -> setFrom('comercial@dehagencia.com',"Sistema de Login");
                $mail ->addAddress($email, $nome);
                $mail->isHTML(true);                                  //
                $mail->Subject = 'Recuperar senha';
                $mail->Body = '<h1>Recuperar senha link abaixo</h1><br><br><a href="https://novo.dehagencia.com/login/trocar-senha.php?cod_confirm='.$cod.'">Confirme aqui</a>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail -> send();
               

            } catch(Exception $exceptMailer){

                echo 'Houve um erro ao enviar informações';
            }
            header("location: confirmacao-recupera-senha.php");
        } else {
            $erro_geral = "Email cadastrado não encontrado ou não confirmado";
        }
        
    }

?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>  
    <title>Recuperar senha</title>
</head>
<body>
    <form method="post">
        <div style="width:100%;text-align:center;">
            <h1>Recuperar senha</h1>
        </div>
        <?php
            if(isset($erro_geral)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_geral; ?>
                </div>
        <?php } ?>
        <p>Informe o Email cadastrado no sistema</p>
        <div class="input-group">
            <img class="input-icon" src="icones/email.png" alt="">
            <input
            type="email" required name="recupera-email" placeholder="Digite seu E-mail">
        </div>
        <div class="input-group">
            <button class="btn-blue" type="submit">Enviar código por Email</button>
        </div>
        <a href="index.php">Voltar para Login</a>
    </form>
    <script>
        setTimeout(()=>{
            const message = document.querySelector('.erro-geral');
            message.style.display="none";
        },3000)
    </script>
</body>
</html>