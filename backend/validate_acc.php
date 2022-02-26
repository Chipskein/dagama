<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../public/imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="../public/css/styles.css">
    <title>Dagama | Ativando Usuario</title>
</head>
<body>
<?php
if(isset($_GET['id'])){
    include './infra/services.php';
    $user=getUserInfoRegister($_GET['id']);
    if($user){
        //user[ativo]==0
        if(!$user['ativo']||$user['ativo']=='f'){
            $activated=activateUser($_GET['id']);
            if($activated){
                echo "<h2 align=center>Seu usuario foi ativado</h2>";
                //auto-login
                if(!isset($_SESSION)) { 
                    session_start(); 
                    $_SESSION['email']=$activated['email'];
                    $_SESSION['password']=$activated['password'];
                } 
                header("refresh:2;url=login2.php");               
            }
            else{
                echo "<h2 align=center>Um erro ocorreu</h2>";
            }   
        }
        else{
            echo "<h2 align=center>Seu usuario já esta ativado</h2>";
        }  
    }
    else{
        echo "<h2 align=center>Usuario não encontrado</h2>";
    } 
}
else{
    echo "<h2 align=center>URL inválido</h2>";
} 
?>
</body>
</html>