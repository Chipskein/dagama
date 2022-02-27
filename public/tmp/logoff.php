<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/styles.css">
    <title>Dagama</title>
</head>
<body>
<?php
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    if(isset($_SESSION['userid'])){
        unset($_SESSION['userid']);
        unset($_SESSION['userimg']);
        unset($_SESSION['username']);
        session_destroy();
        echo "<h2 align=center>Tchau Tchau Volte sempre</h2>";
        header("refresh:1;url=../index.php");
        die();
    }
    else {
        echo "<h2 aling=center>Você não esta logado</h2>";
        header("refresh:1;url=../index.php");
        die();
    }
?>
</body>
</html>
