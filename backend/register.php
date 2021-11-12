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
    $erros = [];
    // var_dump($_POST);
    echo "<br>";
    // TODO: Validação de gênero, termos
    if(isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['cpassword']) && isset($_POST['bdate']) &&
        isset($_POST['pais']) && isset($_POST['termos'])){
        // FIXME: erro ao usar email institucional
        if(!preg_match("/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/", $_POST['email'])){
            $erros[] = "email inválido";
        } else {
            if(in_array($_POST['email'], getEmails())){
                $erros[] = "email já cadastrado";
            }
        }
        if(!preg_match("/^[a-zA-Z0-9\. ]*$/", $_POST['username']) || trim($_POST['username']) == ""){
            $erros[] = "username inválido";
        }
        if(!preg_match("/^([0-9]{4}-[0-9]{2}-[0-9]{2})$/", $_POST['bdate'])){
            $erros[] = "formato de data inválido";
        } else {
            if(!validar_data($_POST['bdate'])){
                $erros[] = "data inválida";
            }
        }
        if(!preg_match("/^[1-9][0-9]*$/", $_POST['pais'])){
            $erros[] = "país inválido";
        } else {
            if(!in_array($_POST['pais'], getPaises())){
                $erros[] = "pais não cadastrado";
            }
        }
        if(!preg_match("/^[a-zA-Z0-9]{6}$/", $_POST['password'])){
            $erros[] = "senha inválido: ela precisa ter no mínimo 6 caracteres ou números";
        }
        if($_POST['cpassword'] != $_POST['password']){
            $erros[] = "as senhas precisam ser iguais";
        }
    } else {
        $erros[] = "campos faltando";
    }
    if($erros != []) {
        echo "<h2>Erro: ".implode(", ", $erros)."</h2>";
        header("refresh:2;url=../index.php");
        die();
    } else {
        echo "<h2 align=center>Registrando...</h2>";
            $email = "$_POST[email]";
            $username = "$_POST[username]";
            $password = password_hash("$_POST[password]", PASSWORD_DEFAULT);
            $bdate = "$_POST[bdate]";
            $pais = "$_POST[pais]";
            $genero = "$_POST[genero]";
            $photo = isset($_FILES['photo']) ? $_FILES['photo'] : null;
            $registered = Register($email, $password, $bdate, $username, $genero, $pais, $photo);
            if($registered){
                header("refresh:2;url=validEmail.php");
                die();
            } 
            else echo "Um erro ocorreu no registro!";
    }
?>    
</body>
</html>
