<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Dagama | Valide seu email</title>
</head>
<body>
<?php
echo "
<div align=center>
<div class=main-container>
    <main class=main-Vemail>
    <img src=/imgs/icon.png alt=logo class=logo-Vemail>
        <h1>Validação Enviada</h1>
        <h3>$user[email]</h3>
        <p>Por favor, acesse o link no email para confirmar seu cadastro e navegar conosco! </p>
        <p>Após a confirmação, você será logado automaticamente.</p>
        <div class=main-button>
            <p class=main-button><a href=/sendmail/$user[codigo] class=main-button>Clique aqui para receber o email<a></p>
        </div>
    </main>  
</div>
</div>";
?>
</body>
</html>