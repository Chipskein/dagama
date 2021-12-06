<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="styles.css">
    <title>Dagama | Valide seu email</title>
</head>
<body>
<?php
include './backend/infra/connection.php';
if(!isset($_SESSION)) { 
    session_start(); 
}
if(isset($_SESSION['userid'])){
    echo "<h2 align=center>Você já esta logado!</h2>";
    header("refresh:1;url=mar.php");
    die();
}
if(isset($_GET["id"])){
    $user=getUserInfoRegister($_GET["id"]);
    if($user){
        echo "
        <div align=center>
        <div class=main-container>
            <main class=main-Vemail>
            <img src=./imgs/icon.png alt=logo class=logo-Vemail>
                <h1>Validação Enviada</h1>
                <h3>$user[email]</h3>
                <p>Por favor, acesse o link no email para confirmar seu cadastro e navegar conosco! </p>
                <p>Após a confirmação, você será logado automaticamente.</p>
                <div class=main-button>
                    <p class=main-button><a href=./backend/sendmail.php?id=$_GET[id] class=main-button>Clique aqui para receber o email<a></p>
                </div>
            </main>  
        </div>
        </div>";
    }
    else{
        echo "<h2 align=center>Um erro ocorreu</h2>";
        header("refresh:2;url=index.php");
    }
}
else{
    echo "<h2 align=center>ID inválido</h2>";
    header("refresh:2;url=index.php");
}
?>
</body>
</html>