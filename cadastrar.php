<?php
//envio smtp
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require('config/phpmailer/src/Exception.php');
require('config/phpmailer/src/PHPMailer.php');
require('config/phpmailer/src/SMTP.php');
    //ARQUIVOS DE CONEXÃO
require('config/conexao.php');

if(isset($_SESSION['TOKEN'])){

    $sql = $pdo -> prepare('SELECT * FROM usuarios WHERE token=?');
    $sql -> execute(array($_SESSION['TOKEN']));
    $usuario = $sql -> fetchAll(PDO::FETCH_ASSOC);

    if($usuario){

         header('location: paginas/dashboard.php');

    }
}

    //VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
    if(isset($_POST['nome-completo']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['repete-senha'])){

        if(empty($_POST['nome-completo']) or empty($_POST['email']) or empty($_POST['senha']) or empty($_POST['repete-senha']) or empty($_POST['termos'])){
            $erro_geral = "Campo vazio verifique";
        }else{
            //RECEBER VALORES VINDOS DO POST E LIMPAR
            $nome = limpaPost($_POST['nome-completo']);
            $email = limpaPost($_POST['email']);
            $senha= limpaPost($_POST['senha']);
            $senha_cripto = md5($senha);
            $repete_senha = limpaPost($_POST['repete-senha']);
            $checkbox = limpaPost($_POST['termos']);

            $recupera_senha = "";
            $token = "";
            $codigo_confirmacao = uniqid();
            $status = "novo";
            $data_cadastro = date('d-m-Y');
            

            //VERIFICAR SE NOME É APENAS LETRAS E ESPAÇOS
            if (!preg_match("/^[a-zA-Z-' ]*$/",$nome)) {
                $erro_nome = "Somente permitido letras e espaços em branco";
              }
              //VERIFICA SE EMAIL É VÁLIDO
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $ERRO_email = "formato de e-mail inválido";
            }
            //VERIFICA SE SENHA É MAIOR QUE 6 DÍGITOS
            if(strlen($senha) < 6){
                $erro_senha = "A senha deve ter 6 Caracteres ou mais";
            }
            //VERIFICA REPETIÇÃO DA SENHA
            if($senha !== $repete_senha){
                $erro_repete_senha = "As senha devem ser iguais";
            }
            //VERIFICAR SE CHECKBOX FOI ATIVADO
           // if($checkbox !== 'ok'){
               // $erro_checkbox = "A aceitação dos termos é obrigatória";
          //  }
          if(!isset($erro_geral) && !isset($erro_nome)  && !isset($ERRO_email)  && !isset($erro_senha)  && !isset($erro_repete_senha)){

            $sql = $pdo -> prepare("SELECT * FROM usuarios WHERE EMAIL=? LIMIT 1");
            $sql -> execute(array($email));
            $usuario = $sql -> fetchAll();

            if(!$usuario){
                $sql = $pdo -> prepare("INSERT INTO usuarios VALUES(NULL,?,?,?,?,?,?,?,?)");
                $sql -> execute(array($nome,$email,$senha_cripto,$recupera_senha,$token,$codigo_confirmacao,$status,$data_cadastro));
                
                if($modo = "producao"){
                        
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
                        $mail->Subject = 'Confirme seu Cadastro';
                        $mail->Body    = '<h1>Por favor confirme seu email abaixo</h1><br><br><a href="https://novo.dehagencia.com/login/confirmacao.php?cod_confirm='.$codigo_confirmacao.'">Confirme aqui</a>';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail -> send();
                      

                    } catch(Exception $exceptMailer){

                        echo "Houve um erro ao enviar informações $exceptMailer";
                    }
                } 
                echo "<script>location.href='./obrigado.php'</script>";
            }
            else{
                $erro_geral = "<p>Email já Cadastrado</p>";
            }
            
            
          }

        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Cadastrar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>  
</head>
<body>
    <form action="" method="post">
        <h1>Cadastrar</h1>
        <?php
            if(isset($erro_geral)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_geral; ?>
                </div>
        <?php } ?>
        
        <div class="input-group">
            <img class="input-icon" src="icones/usuario.png" alt="">
            <input <?php if(isset($_POST['nome-completo']) or isset($nome)){echo 'value="'.$nome.'"';}?>
            <?php if(isset($erro_geral) or isset($erro_nome)){echo "class='erro-input'";}?>
                 type="text" placeholder="Nome completo" name="nome-completo" required>
            <?php
                if(isset($erro_nome)){
                    echo "<div class='erro'>".$erro_nome."</div>";
                }
            ?>
            
        </div>
        <div class="input-group">
            <img class="input-icon" src="icones/email.png" alt="">
            <input <?php if(isset($_POST['email']) or isset($email)){echo "value='$email'";}?>
            <?php if(isset($erro_geral) or isset($ERRO_email)){echo "class='erro-input'";}?>
            type="email" placeholder="Seu melhor email" name="email" required>
            <?php
                if(isset($ERRO_email)){
                    echo "<div class='erro'>".$ERRO_email."</div>";
                }
            ?>
        </div>
        <div class="input-group">
            <img class="input-icon" src="icones/senha.png" alt="">
            <input <?php if(isset($_POST['senha']) or isset($senha)){echo "value='$senha'";}?>
            <?php if(isset($erro_geral) or isset($erro_senha)){echo "class='erro-input'";}?> 
            type="password" placeholder="Digite sua Senha" name="senha" required>
            <?php
                if(isset($erro_senha)){
                    echo "<div class='erro'>".$erro_senha."</div>";
                }
            ?>
        </div>
        <div class="input-group">
            <img class="input-icon" src="icones/senha.png" alt="">
            <input <?php if(isset($_POST['repete-senha']) or isset($repete_senha)){echo "value='$repete_senha'";}?>
             <?php if(isset($erro_geral) or isset($erro_repete_senha)){echo "class='erro-input'";}?> 
            type="password" placeholder="Repita a senha" name="repete-senha" required>
            <?php
                if(isset($erro_repete_senha)){
                    echo "<div class='erro'>".$erro_repete_senha."</div>";
                }
            ?>
           
        </div>
        <div class="input-group">
            <input type="checkbox" id="termos" name="termos" value="termos" required>
            <label for="termos">Ao se cadastrar você concorda com a nossa <a class="link" href="#">Política de privacidade</a> e os <a class="link" href="#">Termos de uso</a></label>
        </div>
        <button class="btn-blue" type="submit">Fazer Login</button>
        <a href="index.php">Já tenho uma conta</a>
    </form>
    <script>
        setTimeout(()=>{
            const message = document.querySelector('.erro');
            message.style.display="none";
        },3000)
    </script>
</body>
</html>