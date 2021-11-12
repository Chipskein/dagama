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
session_start();
if($_SESSION['userid']){
    echo "<h2 align=center>Você já esta logado!</h2>";
    header("refresh:1;url=mar.php");
    die();
}
//echo "<h2>Registrado, por favor confirme seu email!</h2><br>";
//echo "<input type=\"button\" class=\"button\" id=\"loginBtn\" onclick=\"verificar()\" value=\"Entrar\"/>";
?>
<div align=center>
    <!--REVER DESIGN-->
    <main id=main_Validar_email>
        <h1>Validação Enviada</h1>
        <h3>user@email.com</h3>
        <p>Obrigado por se cadastrar no dagama ,para concluir seu cadastro deves clicar no link enviado para o email cadastrado</p>
        <button>Renviar</button>
        <button>Logar</button>
        <img src="./imgs/icon.png" alt="">
    </main>
</div>
</body>
</html>