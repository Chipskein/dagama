<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="../styles.css">
    <title>Dagama | Register</title>
</head>
<body>
<?php
    include './infra/connection.php';
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    if(isset($_SESSION['userid'])){
        $erros = [];
        echo "<br>";
        if(isset($_POST['descr']) && isset($_POST['nome']))
        {
            
        } 
        else $erros[] = "campos faltando";

        if($erros != []) {
            echo "<h2>Erro: ".implode(", ", $erros)."</h2>";
            header("refresh:2;url=../index.php");
            die();
        } 
        else {
            echo "<h2 align=center>Adicionando Porto...</h2>";
            $perfil = "$_SESSION[userid]";
            $nome = "$_POST[nome]";
            $descr = "$_POST[descr]";
            $img= is_uploaded_file($_FILES['img']['tmp_name']) ? $_FILES['img']:null;
            $registered = addPorto($perfil,$nome,$descr,$img);
            if($registered){
                echo "<h2 align=center>Porto Adicionado</h2>";
                header("refresh:2;url=../mar.php");
                die();
               // header("refresh:2;url=../porto.php?id=$id");
            } 
            else echo "Um erro ocorreu!";
        }
    }
    else {
        echo "<h2>Você não está logado</h2>";
        header("refresh:1;url=../index.php");
        die();
    }
?>    
</body>
</html>
