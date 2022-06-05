<?php

require('config/conexao.php');



if(isset($_SESSION['TOKEN'])){

    $sql = $pdo -> prepare('SELECT * FROM usuarios WHERE token=?');
    $sql -> execute(array($_SESSION['TOKEN']));
    $usuario = $sql -> fetchAll(PDO::FETCH_ASSOC);

    if($usuario){

         header('location: paginas/dashboard.php');

    }
}

if(isset($_POST['login-email']) && isset($_POST['login-senha'])){
    
    if(!empty($_POST['login-email']) && !empty($_POST['login-senha'])){

        $email = limpaPost($_POST['login-email']);
        $senha = limpaPost($_POST['login-senha']);
        $senha_cript = md5($senha);

        $sql = $pdo -> prepare("SELECT * FROM usuarios WHERE email=? and senha=? limit 1");
        $sql -> execute(array($email,$senha_cript));
        $usuario = $sql -> fetch(PDO::FETCH_ASSOC);

        if($usuario){
            //EXISTE UM USUARIO
            //VERIFICAR SE FOI CONFIRMADO
            if($usuario['_status'] == "confirmado"){
                //CRIAR UM TOKEN
                $token = uniqid().date('d-m-Y-H-i-s');

                $sql = $pdo -> prepare("UPDATE usuarios set token=? where email=? and senha=?");
                $sql -> execute(array($token,$email,$senha_cript));

                if($sql){
                    
                    $_SESSION['TOKEN'] = $token;
                    header('location: paginas/dashboard.php');
                
                }
            }
            else {
                $erro_status="Usuário não confirmado, verifique seu email";
            }
        }
        else {

          $erro_login ="<p>Usuário ou Senha inválidos</p>";
         }
    }
    else {
        $erro_login ="<p>Campos Vazios, verifique</p>";
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
    <title>Login</title>
</head>
<body>
    <form method="post">
        <div style="width:100%;text-align:center;">
            <h1>Login</h1>
        </div>

        <?php if(isset($_GET['result'])){?>
            <div class="sucesso animate__animated animate__rubberBand">
               Cadastrado com sucesso
        </div>

        <?php }?>

        <?php if(isset($_GET['senha-alterada'])){?>
            <div class="sucesso animate__animated animate__rubberBand">
             Senha alterada com sucesso
        </div>

        <?php }?>

        <?php
            if(isset($erro_login)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_login; ?>
                </div>
        <?php } ?>
        <?php
            if(isset($erro_status)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_status; ?>
                </div>
        <?php } ?>

        <div class="input-group">
            <img class="input-icon" src="icones/email.png" alt="">
            <input 
            <?php if(isset($_POST['login-email'])){
              
                echo 'value="'.$_POST['login-email'].'"';
               
            }?>
            type="email" required name="login-email" placeholder="Digite seu E-mail">
        </div>
        <div class="input-group">
            <img class="input-icon" src="icones/senha.png" alt="">
            <input type="password" required name="login-senha" placeholder="Digite sua Senha">
            <button class="btn-blue" type="submit">Fazer Login</button>
        </div>
        <div class="links-login">
            <a href="cadastrar.php">Ainda não tenho Cadastro</a>
            <a href="recupera_senha.php">Recuperar senha</a>
        </div>
    </form>
    <script>
        setTimeout(()=>{
            const message = document.querySelector('.sucesso');
            message.style.display="none";
        },3000)
    </script>
</body>
</html>