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
include './infra/services.php';

    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    if(!isset($_SESSION['userid'])){
        $erros = [];
        echo "<br>";
        var_dump($_POST);
        var_dump($_FILES);
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

            if($_POST['pais'] == "null"){
                $erros[] = "informe um país";
            }
            if($_POST['pais'] == "outro"){
                if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newpais']) || trim($_POST['newpais']) == "") $erros[] = "Nome do pais inválido";

                if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newestado']) || trim($_POST['newestado']) == "") $erros[] = "Nome do estado inválido";

                if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newcidade']) || trim($_POST['newcidade']) == "") $erros[] = "Nome de cidade inválido";
            }
            if($_POST['pais'] != "outro" && $_POST['pais'] != "null") {
                $paisesArray = getPaises();
                $c = 1;
                $found = 0;
                foreach ($paisesArray as $pais) {
                    if($pais['codigo'] == $_POST['pais']){
                        $found = 1;
                    }
                    if($c == count($paisesArray) && $found = 0){
                        $erros[] = "Pais não cadastrado";
                    }
                }

                if($_POST['estado'] == "null"){
                    $erros[] = "informe um estado";
                }
                if($_POST['estado'] == "outro"){
                    if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newestado']) || trim($_POST['newestado']) == "") $erros[] = "Nome do estado inválido";

                    if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newcidade']) || trim($_POST['newcidade']) == "") $erros[] = "Nome de cidade inválido";
                }
                if($_POST['estado'] != "outro" && $_POST['estado'] != "null") {
                    $estadosArray = getStates();
                    $c2 = 1;
                    $found2 = 0;
                    foreach ($estadosArray as $estado) {
                        if($estado['codigo'] == $_POST['estado']){
                            $found2 = 1;
                        }
                        if($c2 == count($estadosArray) && $found2 = 0){
                            $erros[] = "Estado não cadastrado";
                        }
                    }
                    if($_POST['cidade'] == "null"){
                        $erros[] = "informe uma cidade";
                    }
                    if($_POST['cidade'] == "outro"){
                        if(!preg_match("/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$/", $_POST['newcidade']) || trim($_POST['newcidade']) == "") $erros[] = "Nome de cidade inválido";
                    }
                    if($_POST['cidade'] != "outro" && $_POST['cidade'] != "null") {
                        $cidadesArray = getCities();
                        $c3 = 1;
                        $found3 = 0;
                        foreach ($cidadesArray as $cidade) {
                            if($cidade['codigo'] == $_POST['cidade']){
                                $found3 = 1;
                            }
                            if($c3 == count($cidadesArray) && $found3 = 0){
                                $erros[] = "Cidade não cadastrada";
                            }
                        }
                    }
                }
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
            if($_POST['pais'] == "outro"){
                $pais = addPais($_POST['newpais']);
                $estado = addEstado($_POST['newestado'], $pais);
                $cidade = addCidade($_POST['newcidade'], $estado);
            }
            if($_POST['estado'] == "outro"){
                $estado = addEstado($_POST['newestado'], $_POST['pais']);
                $cidade = addCidade($_POST['newcidade'], $estado);
            }
            if($_POST['cidade'] == "outro"){
                $cidade = addCidade($_POST['newcidade'], $_POST['estado']);
            }
            if($_POST['cidade'] != "outro" && $_POST['cidade'] != "null"){
                $cidade = $_POST['cidade'];
            }
            
            $genero = "$_POST[genero]";
            $photo = null;
            if($_FILES['photo']['tmp_name'])
            $photo = is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo'] : null;

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
