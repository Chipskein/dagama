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
    function validar_data($data){
        $dataArr = explode('-', $data);
        $day = $dataArr[2];
        $month = $dataArr[1];
        $year = $dataArr[0];
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

    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    if(!isset($_SESSION['userid'])){
        $erros = [];
        echo "<br>";
        if(isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['cpassword']) && isset($_POST['bdate']) &&isset($_POST['pais']) && isset($_POST['termos']) && isset($_POST['genero']))
        {
            if($_POST['termos']!='pass') $erros[] = "Você não concordou com os termos de uso";
            if($_POST['genero'] != 'M' && $_POST['genero'] != 'F' && $_POST['genero'] != 'O') $erros[] = "gêneno inválido";
            if(!preg_match("/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/", $_POST['email'])) $erros[] = "email inválido";
            else {
                if(emailExists($_POST['email'])) $erros[] = "email já cadastrado";   
            }
            if(trim($_POST['username']) == "") $erros[] = "username inválido";
            if(!preg_match("/^([0-9]{4}-[0-9]{2}-[0-9]{2})$/", $_POST['bdate'])) $erros[] = "formato de data inválido";
            else {
                if(!validar_data($_POST['bdate'])) $erros[] = "data inválida";
            }
            if(!preg_match("/^[1-9][0-9]*$/", $_POST['pais'])) $erros[] = "país inválido";
            else {
                //if(!in_array($_POST['pais'], getPaises())) $erros[] = "pais não cadastrado";
            }
            //ta travando o heroku ver o porque
            //if(trim("$_POST[password]")!=''&&strlen("$_POST[password]")>=6) $erros[] = "senha inválido: ela precisa ter no mínimo 6 caracteres ou números";
            if($_POST['cpassword'] != $_POST['password']) $erros[] = "as senhas precisam ser iguais";
        } 
        else $erros[] = "campos faltando";

        if($erros != []) {
            echo "<h2>Erro: ".implode(", ", $erros)."</h2>";
            header("refresh:2;url=../index.php");
            die();
        } 
        else {
            echo "<h2 align=center>Registrando...</h2>";
            $email = "$_POST[email]";
            $username = "$_POST[username]";
            $password = password_hash("$_POST[password]", PASSWORD_DEFAULT);
            $bdate = "$_POST[bdate]";//converter bdate to yyyy/mm/dd
            $cidade = "$_POST[cidade]";
            $genero = "$_POST[genero]";
            $photo= is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo']:null;
            $registered = Register($email, $password, $bdate, $username, $genero, $cidade, $photo);
            if($registered){
                $id=getIdbyEmail($email);
                header("refresh:2;url=../validarEmail.php?id=$id");
                die();
            } 
            else echo "Um erro ocorreu no registro!";
        }
    }
    else {
        echo "<h2>Você já esta logado</h2>";
        header("refresh:1;url=../mar.php");
        die();
    }
?>    
</body>
</html>
