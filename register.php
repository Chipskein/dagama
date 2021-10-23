<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imgs/icon.png" type="image/jpg">
    <link rel="stylesheet" href="styles.css">
    <title>Register</title>
</head>
<body>
<div align="center">
        <div class="nav-bar">
            <img id="logo-nav-bar" src="imgs/icon.png" alt="logo">
        </div>
        <div id="main2">
            <div id="imgs_register">
                <div id="img_input1">
                    <div >
                        <img id="img_perfil" src="#" >
                    </div>
                    <input id="imgInp" type="file" accept="image/png,image/jpeg">
                </div>
                <div id="img_input2">
                    <div>
                        <img src="#" id="img_banner">
                    </div>
                    <input id="imgInp2" type="file" accept="image/png,image/jpeg">
                </div>
            </div>
            <div id="form_register">
                <form action="" method="post">
                    <label>
                        Email:<input type="email" name="email" id="email">
                    </label>
                    <br>
                    <label>
                        Confirm Email:<input type="email" name="c_email" id="c_email">
                    </label>
                    <br>
                    <label>
                        Username:<input type="text" name="username" id="username">
                    </label>
                    <br>
                    <label>
                        Password:<input type="password" name="password" id="password">
                    </label>
                    <br>
                    <label>
                        Confirm Password:<input type="password" name="c_password" id="c_password">
                    </label>
                    <br>
                    <label>
                        Data:<input type="date" name="date" id="date">
                    </label>
                    <br>
                    <label>
                        Pais:<select name="country" id="country">
                            <option value="Brazil">Brazil</option>
                        </select> 
                    </label>
                    <br>
                    <input type="checkbox">Concordo com os <a href="README.md">termos de uso</a>
                </form>
            </div>
        </div>
        <button class="button">Enviar</button>
</div>
<script>
    imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
            img_perfil.src = URL.createObjectURL(file)
        }
    }
    imgInp2.onchange = evt => {
        const [file] = imgInp2.files
        if (file) {
            img_banner.src = URL.createObjectURL(file)
        }
    }
</script>
</body>
</html>