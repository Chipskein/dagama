<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Logando</title>
</head>
<body>
<?php
    include './infra/connection.php';
    echo "<div align=center>";
    if(isset($_POST['email'])&&isset($_POST['password'])){
        echo "<h2>Logando...</h2>";
        $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
        if(preg_match($regex_email,$_POST['email'])){
            $email="$_POST[email]";
            //to HASH password_hash($password,PASSWORD_DEFAULT);
            //email=abfn@gmail.com
            //password=kasjfkajsfjaisf
            $pass="$_POST[password]";//need be hashed
            $passed=Login("$email","$pass");
            if($passed){
                echo "<br>Logado</br>";
                //pegar dados do usuario;
                $USERID=$passed['codigo'];
                echo $USERID;
                //iniciar sessão e armazenar os dados do usuario em na sessão.
            }
            else{
                echo "<h2>Credenciais Inválidas</h2>";
                header("refresh:1;url=../index.html");
                die();
            }
        }
        else{
            echo "<h2>Credenciais Inválidas</h2>";
            header("refresh:1;url=../index.html");
            die();
        }
    }
    else{ 
        echo "<h2>Credenciais Inválidas</h2>";
        header("refresh:1;url=../index.html");
        die();
    }
    echo "</div>";

?>    
</body>
</html>
