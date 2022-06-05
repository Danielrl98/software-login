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

if($_GET['cod_confirm'] && !empty($_GET['cod_confirm'])){

    if(isset($_POST['trocar-senha']) && isset($_POST['trocar-senha-confirmacao'])){

        if(!empty($_POST['trocar-senha']) && !empty($_POST['trocar-senha-confirmacao'])){

            $cod = $_GET['cod_confirm'];
            $senha = limpaPost($_POST['trocar-senha']);
            $senha_cript = md5($senha);

            if($_POST['trocar-senha'] == $_POST['trocar-senha-confirmacao']){

                $sql = $pdo->prepare("UPDATE usuarios SET senha=? WHERE recupera_senha=?");
                $sql -> execute(array($senha_cript,$cod));

                if($sql){

                    header("location: index.php?senha-alterada=ok");

                } else {
                    $erro_envio = "Erro no envio, recarregue a página e tente novamente";
                }
            } else{

                $erro_senha = "Senha não coincidem, verifique";
            }
        } else {

            $erro_vazio = "Campos vazios, verifique";
    }       

    } 
} else {
    header("location: index.php");
}
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>  
    <title>Trocar senha</title>
</head>
<body>
    <form method="post">
        <div style="width:100%;text-align:center;">
            <h1>Trocar senha</h1>
        </div>
        <?php
            if(isset($erro_envio)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_envio; ?>
                </div>
        <?php } ?>
        <?php
            if(isset($erro_senha)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_senha; ?>
                </div>
        <?php } ?>
        <?php
            if(isset($erro_vazio)){?>
                <div class="erro-geral animate__animated animate__rubberBand">
                    <?php echo $erro_vazio; ?>
                </div>
        <?php } ?>
        <div class="input-group">
            <img class="input-icon" src="icones/senha.png" alt="">
            <input type="password" required name="trocar-senha" placeholder="Nova Senha mínimo de 6 dígitos">
            
        </div>
        <div class="input-group">
            <img class="input-icon" src="icones/senha.png" alt="">
            <input type="password" required name="trocar-senha-confirmacao" placeholder="Confirme sua nova senha">
            <button class="btn-blue" type="submit">Trocar senha</button>
        </div>
        <div class="links-login">
            <a href="cadastrar.php">Voltar para o ínicio</a>
        </div>
    </form>
    <script>
        setTimeout(()=>{
            const message = document.querySelector('.erro-geral');
            message.style.display="none";
        },3000)
    </script>
</body>
</html>