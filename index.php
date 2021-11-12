<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <title>Dagama</title>
</head>
<body>
  <?php 
    session_start();
    if($_SESSION['userid']){
      echo "<h2>Voce já esta logado</h2>";
      header("refresh:1;url=mar.php");
      die();
    }
  ?>
  <div id="main">
    <div class="divLogo">
      <img class="logo" src="imgs/icon.png" alt="logo">
    </div>
    <div id="loginContainer">
      <p class="headerTxt">Login</p>
      <div class="divLogin">
        <form action="backend/login.php" method="post" id="form" name="form">
          <div class="containerLogin">
            <input class="inputs" name="email" type="email" placeholder="Email">
            <input class="inputs" name="password" type="password" placeholder='Senha'>
            <input type="button" class="button" id="loginBtn" onclick="verificar()" value="Entrar"/>
            <div class="containerLinkMenor">
              <a class="linkMenor">Esqueceu sua senha?</a>
            </div>
            <input type="button" class="button registerBtn"  onclick="showRegister()" value="Cadastrar-se"/>
          </div>
        </form>
      </div>
    </div>
    
    <div id="registerContainerHidden">
      <div class="row">
        <button onclick="login()"><img class="arrowBack" src="imgs/icons/right-arrow.png" alt=""></button>
        <p class="headerTxt">Cadastro</p>
      </div>
      <div class="divRegister">
        <form action="backend/register.php" method="post" id="formRegister" name="formRegister" enctype="multipart/form-data">
          <div class="containerRegister">
            <div id="imgs_register">
                <div id="img_input1">
                    <!-- Adicionar Crop-->
                    <div id="img_perfil"></div>
                </div>
                <input id="imgInp" type="file"  name="photo">
            </div>
            <input class="inputs" name="email" type="email" placeholder="Email">
            <input class="inputs" name="username" type="text" placeholder='Username'>
            <input class="inputs" name="password" type="password" placeholder='Senha'>
            <input class="inputs" name="cpassword" type="password" placeholder='Confirmar senha'>
            <input class="inputs" name="bdate" type="date" placeholder='Nascimento'>
            <select class="inputHalf" name="pais">
                <option value="1" selected>Brasil</option>
            </select>
            <select class="inputHalf" name="genero">
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
                <option value="O">Outro</option>
            </select>
            <br>
            <div id="divTermos">
              <input value='pass' name="termos" type="checkbox"> <p>Concordo com os <a href="LICENSE" target="_blank" style="color: #7ED8FF;">termos de uso</a></p>
            </div>
            <input type="button" class="button registerBtn" id="registerBtn" onclick="register()" value="Enviar"/>
          </div>
        </form>
      </div>
    </div>
  <script src="functions.js"></script>
  <script>
    imgInp.onchange = evt => {
      const [file] = imgInp.files
      const img_perfil=document.getElementById("img_perfil");
      if (file) {
          img_perfil.style.backgroundImage=`url(${URL.createObjectURL(file)})`;
      }
      else{
          img_perfil.style.backgroundImage="url(imgs/icons/user-icon.png)"
      }
    }
  </script>
</body>
</html>