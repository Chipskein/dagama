<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama</title>
</head>
<body>
  <div id="main">
    <div class="divLogo">
      <img class="logo" src="imgs/icon.png" alt="logo">
    </div>
    <div id="loginContainer">
      <p class="headerTxt">Login</p>
      <div class="divLogin">
        <form action="/login" method="post" id="form" name="form">
          <div class="containerLogin">
            <input class="inputs" name="email" type="email" placeholder="Email">
            <input class="inputs" name="password" type="password" placeholder='Senha'>
            <input type="button" class="button" id="loginBtn" onclick="verificar()" value="Entrar"/>
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
        <form action="/register" method="post" id="formRegister" name="formRegister" enctype="multipart/form-data">
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
            <input class="inputHalf" name="password" type="password" placeholder='Senha'>
            <input class="inputHalf" name="cpassword" type="password" placeholder='Confirmar senha'>
            <input class="inputHalf" name="bdate" type="date" placeholder='Nascimento'>
            <select class="inputHalf" name="genero">
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
            <div id="divTermos">
              <input value='pass' name="termos" type="checkbox"> <p>Concordo com os <a href="/about" target="_blank" style="color: #7ED8FF;">termos de uso</a></p>
            </div>
            <input type="button" class="button registerBtn" id="registerBtn" onclick="register()" value="Enviar"/>
          </div>
        </form>
      </div>
    </div>
  <script src="js/functions.js"></script>
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
    let newpais=document.getElementById("newpais");
    let newestado=document.getElementById("newestado");
    let newcity=document.getElementById("newcidade");
    select_pais.onchange=()=>{
      if(select_pais.selectedIndex !=0 && select_pais.selectedIndex !=1 ){
        pais=select_pais.value;
        newpais.classList.add("hide-visibility");
        newestado.classList.add("hide-visibility");
        newcidade.classList.add("hide-visibility");
        select_estado.classList.remove("hide-visibility");
        Array.from(select_estado.options).forEach(function(e) {
          if (e.value!="null"&&e.value!="outro") e.remove();
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
        if(select_estado.selectedIndex == 0){
          newpais.classList.add("hide-visibility");
          newestado.classList.add("hide-visibility");
          newcidade.classList.add("hide-visibility");
        }
        if(select_pais.selectedIndex == 1){
          newpais.classList.remove("hide-visibility");
          newestado.classList.remove("hide-visibility");
          newcidade.classList.remove("hide-visibility");
          select_estado.classList.add("hide-visibility");
          select_cidade.classList.add("hide-visibility");
        }
        else{
          newpais.classList.add("hide-visibility");
          newestado.classList.add("hide-visibility");
          newcidade.classList.add("hide-visibility");
          select_estado.classList.add("hide-visibility");
          Array.from(select_estado.options).forEach(function(e) {
            if (e.value!="null"&&e.value!="outro") e.remove();
          });
          select_cidade.classList.add("hide-visibility");
          Array.from(select_cidade.options).forEach(function(e) {
            if (e.value!="null"&&e.value!="outro") e.remove();
          });
        }
      }
    }

    select_estado.onchange=()=>{
      if(select_estado.selectedIndex!=0&&select_estado.selectedIndex!=1){
        newpais.classList.add("hide-visibility");
        newestado.classList.add("hide-visibility");
        newcidade.classList.add("hide-visibility");
        select_cidade.classList.remove("hide-visibility");
        Array.from(select_cidade.options).forEach(function(e) {
          if (e.value!="null"&&e.value!="outro") e.remove();
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
        if(select_estado.selectedIndex==1){
          newestado.classList.remove("hide-visibility");
          newcidade.classList.remove("hide-visibility");
          select_cidade.classList.add("hide-visibility");
        }
        else{
          newestado.classList.add("hide-visibility");
          newcidade.classList.add("hide-visibility");
          select_cidade.classList.add("hide-visibility");
          Array.from(select_cidade.options).forEach(function(e) {
            if (e.value!="null"&&e.value!="outro") e.remove();
          });
        }
      }
    }
    select_cidade.onchange=()=>{
      if(select_cidade.selectedIndex==0){
        newcidade.classList.add("hide-visibility");
      }
      if(select_cidade.selectedIndex==1){ 
        newcidade.classList.remove("hide-visibility");
      }
      
    }

  </script>
</body>
</html>
<!-- php -S localhost:3000 -c php.ini -->
