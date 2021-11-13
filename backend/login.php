<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Dagama</title>
</head>
<body>
<?php
    include './infra/connection.php';
    session_start();
    echo "<div align=center>";
    if(!$_SESSION['userid']){
        if(isset($_POST['email'])&&isset($_POST['password'])){
            echo "<h2 align=center>Logando...</h2>";
            $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
            if(preg_match($regex_email,$_POST['email'])){
                $email="$_POST[email]";
                //email=abfn@gmail.com
                //password=kasjfkajsfjaisf
                $pass="$_POST[password]";
                $passed=Login("$email","$pass");
                if($passed){
                    if($passed['ativo']==1){
                        echo "<h2 align=center>Logado</h2>";
                        $USERID=$passed['codigo'];
                        session_start();
                        $_SESSION["userid"] = $USERID;
                        header("refresh:1;url=../mar.php");
                        die();
                    }
                    else{
                        echo "<h2 align=center>Ative seu usuario</h2>";
                        header("refresh:1;url=../validarEmail.php");
                        die();
                    }
                }
                else{
                    echo "<h2>Credenciais Inválidas</h2>";
                    header("refresh:1;url=../index.php");
                    die();
                }
            }
            else{
                echo "<h2>Credenciais Inválidas</h2>";
                header("refresh:1;url=../index.php");
                die();
            }
        }
        else{ 
            echo "<h2>Credenciais Inválidas</h2>";
            header("refresh:1;url=../index.php");
            die();
        }
    }
    else{
        echo "<h2>Você já esta logado!!</h2>";
        header('refresh=1;url=../mar.php');
        die();
    }
    echo "</div>";

?>    
</body>
</html>
