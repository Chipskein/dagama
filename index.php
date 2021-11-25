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
    include './backend/infra/connection.php';
    session_start();
    if(isset($_SESSION['userid'])){
      echo "<h2>Voce já esta logado</h2>";
      header("refresh:1;url=mar.php");
      die();
    }
    $paises=getPaises();
    $estados=getStates();
    $cidades=getCities();
    echo "<script>";
      echo "let states=[];";
      echo "let cities=[];";
      foreach($estados as $estado){
        echo "estado={";
        echo "pais:$estado[pais],";
        echo "codigo:$estado[codigo],";
        echo "nome:\"$estado[nome]\"";
        echo "};";
        echo "states.push(estado);";
      }
      foreach($cidades as $cidade){
        echo "cidade={";
        echo "uf:$cidade[uf],";
        echo "codigo:$cidade[codigo],";
        echo "nome:\"$cidade[nome]\"";
        echo "};";
        echo "cities.push(cidade);";
      }
      echo "estado=null;";
      echo "cidade=null;";
    echo "</script>";
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
            <input class="inputs" name="username" type="text" placeholder='Username'>
            <input class="inputs" name="email" type="email" placeholder="Email">
            <input class="inputs" name="password" type="password" placeholder='Senha'>
            <input class="inputs" name="cpassword" type="password" placeholder='Confirmar senha'>
            <input class="inputs" name="bdate" type="date" placeholder='Nascimento'>
            <!-- <p>Genero</p> -->
            <select class="inputs" name="genero">
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
                <option value="O">Outro</option>
            </select>
            <select class="inputs" name="pais" id='pais'>
              <option value="null" selected>Selecione o seu pais:</option>
              <?php
                foreach($paises as $pais){
                    echo "<option value=$pais[codigo]>$pais[nome]</option>";
                }
              ?>
            </select>
            <select class="inputHalf hide" name="estado" id=estado>
            <option value="null" selected>Selecione o seu estado:</option>
              <!--
                <option value="1" selected>Brasil</option>
              -->
            </select>
            <select class="inputHalf hide" name="cidade" id=cidade>
                <option value="null" selected>Selecione o sua cidade:</option>
            </select>
           
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
    let select_pais=document.getElementById("pais")
    let select_estado=document.getElementById("estado")
    let select_cidade=document.getElementById("cidade")
    select_pais.onchange=()=>{
      if(select_pais.selectedIndex!=0){
        
        pais=select_pais.value;
        select_estado.classList.remove("hide");
        Array.from(select_estado.options).forEach(function(e) {
          if (e.value!="null") e.remove();
        });
        states.forEach(e=>{
          if(e.pais==pais){
            option=document.createElement("option");
            option.value=e.codigo;
            option.innerHTML=e.nome;
            select_estado.append(option);
          };
        })
      }
      else{
        select_estado.classList.add("hide");
        Array.from(select_estado.options).forEach(function(e) {
          if (e.value!="null") e.remove();
        });
        select_cidade.classList.add("hide");
        Array.from(select_cidade.options).forEach(function(e) {
          if (e.value!="null") e.remove();
        });
      }
    }
    select_estado.onchange=()=>{
      if(select_estado.selectedIndex!=0){
        select_cidade.classList.remove("hide");
        Array.from(select_cidade.options).forEach(function(e) {
          if (e.value!="null") e.remove();
        });
        cities.forEach(e=>{
          estado=select_estado.value;
          if(e.uf==estado){
            option=document.createElement("option");
            option.value=e.codigo;
            option.innerHTML=e.nome;
            select_cidade.append(option);
          };
        })
      }
      else{
        select_cidade.classList.add("hide");
        Array.from(select_cidade.options).forEach(function(e) {
          if (e.value!="null") e.remove();
        });
      }
    }

  </script>
</body>
</html>