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
    include './infra/services.php';
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    echo "<div align=center>";
    if(!isset($_SESSION['userid'])){
        if(isset($_POST['email'])&&isset($_POST['password'])){
            echo "<h2 align=center>Logando...</h2>";
            $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
            if(preg_match($regex_email,$_POST['email'])){
                $email="$_POST[email]";
                $pass="$_POST[password]";
                $passed=Login("$email","$pass");
                if($passed){
                    if($passed['ativo']=='1'||$passed['ativo']==='t'){
                        echo "<h2 align=center>Logado</h2>";
                        $USERID=$passed['codigo'];
                        $USERIMG=$passed['img'];
                        $USERNAME=$passed['username'];
                        $_SESSION["userid"] = $USERID;
                        $_SESSION["userimg"] = $USERIMG;
                        $_SESSION["username"] = $USERNAME;
                        header("refresh:1;url=../index.php");
                        die();
                    }
                    else{
                        echo "<h2 align=center>Ative seu usuario</h2>";
                        header("refresh:1;url=../validate_acc.php?id=$passed[codigo]");
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
        header('refresh=1;url=../public/mar.php');
        die();
    }
    echo "</div>";
?>    
</body>
</html>
