<?php
require('../config/conexao.php');
//verifica se está logado
auth($_SESSION['TOKEN']);



if(isset($_POST['deslogar'])){

session_start();
session_destroy();
session_unset();
header('location: ../index.php?return');
}

?>
<form method="post">
<button name="deslogar">Deslogar</button>
</form>



