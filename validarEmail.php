<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Email</title>
</head>
<body>
<?php
include './infra/connection.php';
echo "<h2>Registrado, por favor confirme seu email!</h2><br>";
echo "<input type=\"button\" class=\"button\" id=\"loginBtn\" onclick=\"verificar()\" value=\"Entrar\"/>";

?>
</body>
</html>