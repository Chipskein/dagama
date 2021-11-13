<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="../imgs/icon.png" type="image/jpg">
    <title>Dagama | Login</title>
</head>
<body>
<?php
include './infra/connection.php';
if(!isset($_SESSION)) { 
    session_start(); 
}
if(isset($_SESSION['email'])&&isset($_SESSION['password'])){
    $login=Login2($_SESSION['email'],$_SESSION['password']);
    session_destroy();
    if($login){
        session_start();
        echo "<h2 align=center>Logado</h2>";
        $USERID=$login['codigo'];
        $_SESSION["userid"] = $USERID;
        header("refresh:1;url=../mar.php");
        die();
    }
    else{
        echo "<h2 align=center>Um erro ocorreu</h2>";
        die();
    }
}
?>
</body>
</html>