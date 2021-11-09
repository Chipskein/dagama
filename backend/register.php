<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Registrando</title>
</head>
<body>
<?php
    function validar_data($data){
        $day=substr($data,0,2);
        $month=substr($data,3,2);
        $year=substr($data,6);    
        $day_qt=0;
        $bissexto = false;
        if (($year % 4 == 0 && $year % 100 !== 0) || ($year % 400 == 0)) $bissexto = true;
        switch ($month) {
        case 1:
            $day_qt = 31;
            break;
        case 2:
            if ($bissexto) $day_qt = 29;
            else $day_qt = 28;
            break;
        case 3:
            $day_qt = 31;
            break;
        case 4:
            $day_qt = 30;
            break;
        case 5:
            $day_qt = 31;
            break;
        case 6:
            $day_qt = 30;
            break;
        case 7:
            $day_qt = 31;
            break;
        case 8:
            $day_qt = 31;
            break;
        case 9:
            $day_qt = 30;
            break;
        case 10:
            $day_qt = 31;
            break;
        case 11:
            $day_qt = 30;
            break;
        case 12:
            $day_qt = 31;
            break;
        }
        if ($day <= $day_qt) return true;
        else return false;
    };
?>
<?php
    include './infra/connection.php';
    if(isset($_POST['email'])&&isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['cpassword'])&&isset($_POST['bdate'])&&isset($_POST['pais'])){
       
        if(preg_match("/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/",$_POST['email'])&&preg_match("/^([0-9]{2}\/[0-9]{2}\/[0-9]{4})$/",$_POST['bdate'])&&validar_data($_POST['bdate'])&&preg_match("/^[1-9][0-9]*$/",$_POST['pais'])&&$_POST['cpassword']==$_POST['password']){
            echo "<h2 align=center>Registrando...</h2>";
            $email="$_POST[email]";
            $username="$_POST[username]";
            $password=password_hash("$_POST[password]",PASSWORD_DEFAULT);
            $bdate="$_POST[bdate]";
            $pais="$_POST[pais]";
            $photo= isset($_FILES['photo']) ? $_FILES['photo']:null;
            $registered=Register($email,$password,$bdate,$username,$pais,$photo);
            if($registered) echo "Registrado,Porfavor confirme seu email";
            else echo "Um erro ocorreu no registro";        
        }
        else{
            echo "<h2>Um erro ocorreu Retornando</h2>";
            header("refresh:1;url=../index.html");
            die();
        }
    }
    else{
        echo "<h2>Um erro ocorreu Retornando</h2>";
        header("refresh:1;url=../index.html");
        die();
    }
?>    
</body>
</html>
