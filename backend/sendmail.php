<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="../styles.css">
    <title>Dagama | Valide seu email</title>
</head>
<body>
<?php 
    include './infra/connection.php';
    //Terminar
    if(isset($_GET['id'])){
        $user=getUserInfo($_GET['id']);
        if($user){
            if(!$user['ativo']||$user['ativo']=='f'){
                $email="$user[email]";
                echo "<h2 align=center>Enviando Email para $email</h2>";

                $start_link='';
                if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $start_link="http://";
                if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")) $start_link="https://";
                
                $link=$start_link."$_SERVER[HTTP_HOST]/backend/validate_acc.php?id="."$_GET[id]";
                $html="
                    <html lang=pt-BR>
                    <head>
                        <meta charset=UTF-8>
                        <meta http-equiv=X-UA-Compatibl content=IE=edge>
                        <meta name=viewport content=width=device-width, initial-scale=1.0>
                    </head>
                    <body>
                        <a href=$link>link</a>
                    </body>
                    </html>
                ";
                $send=send_mail($email,"Dagama | Validar conta ",$html);
                if($send){
                    echo "<h2 align=center>Email Enviado</h2>";
                }
                else{
                    echo "<h2 align=center>Erro:$send</h2>";
                }
            }
            else{
                echo "<h2 align=center>Seu usuario já foi validado</h2>";
            }
        };
    }
?>
</body>
</html>