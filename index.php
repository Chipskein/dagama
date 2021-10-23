<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="styles.css">
    <title>dagama</title>
</head>
<body>
    <div align="center">
        <div id="main">
            <div align="center">
                <img id="logo" src="imgs/icon.png" alt="logo">
            </div>
            <div align="center">
                <form action="" method="post" >
                    <div id="form1">
                    <label>
                        Email:<br><input name="email"type="email" placeholder="you@example.com">
                    </label>
                    <br>
                    <br>
                    <label>
                        <!-- precisa ser hashada-->
                        Password:<br><input name="password" type="password">
                    </label>
                    <br>
                    <br>
                    </div>
                </form>
            </div>

            <div class="buttons">
                <div class="button"><br><a onclick="verificar()">logar</a></div>
                <div class="button"><br><a href="register.php">registrar</a></div>
            </div>
            <br>
        </div>
    </div >
    <script src="functions.js"></script>
</body>
</html>