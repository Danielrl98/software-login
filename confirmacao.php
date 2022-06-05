<?php
    require('./config/conexao.php');
    
    if(isset($_GET['cod_confirm']) && !empty($_GET['cod_confirm'])){
        //LIMPAR O GET
        $cod = limpaPost($_GET['cod_confirm']);

        //CONSULTAR SE ALGUM USUARIO TEM ESSE CODIGO DE CONFIRMAÇÃO
        $status = "confirmado";

        $sql = $pdo -> prepare('SELECT * FROM usuarios where codigo_confirmacao=? limit 1');
        $sql -> execute(array($cod));
        $receber_dados = $sql -> fetch(PDO::FETCH_ASSOC);
       
        if($receber_dados){
            $sql = $pdo -> prepare("UPDATE usuarios SET _status=? where codigo_confirmacao=?");
        $sql -> execute(array($status,$cod));
            if($sql){

                    header('location: index.php?result=ok');
            }
            
        } else{
            echo "<h1>Cógigo de confirmação Invalido</h1>";
        }


    }
?>