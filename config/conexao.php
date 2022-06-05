<?php
session_start();

// DOIS MODOS POSSÍVEIS -> local, produção
$modo = 'producao';

if($modo == 'producao'){

    $servidor = 'localhost';
    $usuario = "dehage90_login";
    $senha = "123456";
    $banco = "dehage90_login";
}

//CONEXÃO COM O BANCO
if($modo == 'local'){
    $servidor = 'localhost';
    $banco="login";
    $usuario = "root";
    $senha = "";
}

try{

    $pdo = new pdo("mysql:host=$servidor;dbname=$banco",$usuario,$senha);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      
}
catch(PDOException $excep){

    echo "falha ao se conectar com o banco";
} 

//elimina dados incorretos dos campos
function limpaPost($dados){
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados  = htmlspecialchars($dados);
    return $dados;
};
function auth($receber_token){
    global $pdo;


    
    $sql = $pdo -> prepare('SELECT * FROM usuarios WHERE token=?  limit 1');
    $sql -> execute(array($receber_token));
     $usuario = $sql -> fetch(PDO::FETCH_ASSOC);
    
    if(!$usuario){
    
        header('location: ../index.php');
        session_destroy();
    }

}






?>