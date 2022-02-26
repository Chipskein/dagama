<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Navio</title>
</head>
<body class=perfil>
<?php
  include '../backend/infra/services.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  $user=[];
  if(!isset($_SESSION['userid'])){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  else{
        $user=getUserInfo("$_SESSION[userid]");
        if(isset($_POST['editName'])){
            $name = "$_POST[editName]";
            if($name === ''||$name === ' ') {
                return header("refresh:1;url=editNavio.php");
            }else{
                $changeName = changeUserName($_SESSION['userid'],$name);
                header("refresh:1;url=editNavio.php");
            }
        }
        if(isset($_POST['editEmail'])){
            $email = "$_POST[editEmail]";
            $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
            if(preg_match($regex_email,$email)){
            $changeEmail = changeUserEmail($_SESSION['userid'],$email);
            header("refresh:1;url=editNavio.php");
            }else {
               return header("refresh:1;url=editNavio.php");
            }
        }
        if(isset($_POST['editSenha'])){
            if($_POST['editSenha'] === ''||$_POST['editSenha'] === ' '){
                return header("refresh:1;url=editNavio.php");
            }else{
                $senha = password_hash("$_POST[editSenha]", PASSWORD_DEFAULT);
                $changeSenha = changeUserSenha($_SESSION['userid'],$senha);
                header("refresh:1;url=editNavio.php");
            }
        }
        if(isset($_POST['ApagarPerfil'])){
            $ApagarUser = deactivateUser($_SESSION['userid']);
            header("refresh:1;url=../backend/logoff.php");
        }
        if(isset($_FILES["photo"])){
          $photo=$_FILES["photo"];
          $oldphoto=$_SESSION['userimg'];//link
          $oldphotoid=substr($oldphoto,47);
          $newimg=updateImg($_SESSION['userid'],$photo,$oldphotoid);
          header("refresh:1;url=editNavio.php");
        }
        if(!$user){
          echo "Usuario inválido";
          header("refresh:1;url=editNavio.php");
          die();
        }
  }
?>
<div id=principal> 
 <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <form class="header-searchBar" name="search" action="usuarios.php" method="get">
      <select id="select-filtro" name="select-filtro">
        <option value="perfil">Perfil</option>
        <option value="porto">Porto</option>
      </select>
      <input class="header-searchBar-input" name="username" type="text" placeholder="Faça sua pesquisa ..." />
      <button type='submit'><img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset=""></button>

  </form>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main class="container-center">
      <div class="UpdateUser-form-container">
          <h2 class="addporto-main-txt">Editar Navio</h2>
          <?php echo "<div id=\"img_perfil\" class=perfil  style=\"background-image:url('$user[img]')\";></div>" ?>
      <form id=formPhoto action=<?php echo "navio.php?user=$_SESSION[userid]"?> enctype=multipart/form-data method="POST">
        <input id="imgInp" class="hidden" type="file" name="photo">
    </form>
    <div id=camera-edit></div>
    <?php
echo "<div class=\"addporto-inputs\">";
echo "  <div class=\"addporto-input-container\">";
echo "    <p class=\"addporto-input-label\">Username:$user[username]</p>";
echo "<a onclick=editName()><img class=img-pencil src=\"imgs/icons/clarity_pencil-line.png\"</img></a>";
echo "  </div>";
echo "<form method=\"post\" id=\"formUpadateUserName\" name=\"formUpadateUserName\">";
echo "<input name=\"editName\" id=\"editName\" class=\"hidden\">";
echo "<button type=\"submit\" id=\"AlterarName\" class=\"hidden\">Alterar</button>";
echo "</form>";
echo "  <div class=\"addporto-input-container\">";
echo "    <p class=\"addporto-input-label\">Email:$user[email]</p>";
echo "<a onclick=editEmail()><img class=img-pencil src=\"imgs/icons/clarity_pencil-line.png\"</img></a>";
echo "  </div>";
echo "<form method=\"post\" id=\"formUpadateUserEmail\" name=\"formUpadateUserEmail\">";
echo "<input name=\"editEmail\" id=\"editEmail\" class=\"hidden\">";
echo "<button type=\"submit\" id=\"AlterarEmail\" class=\"hidden\">Alterar</button>";
echo "</form>";
echo "  <div class=\"addporto-input-container\">";
echo "    <p class=\"addporto-input-label\">Senha:**********</p>";
echo "<a onclick=editSenha()><img class=img-pencil src=\"imgs/icons/clarity_pencil-line.png\"</img></a>";
echo "  </div>";
echo "<form method=\"post\" id=\"formUpadateUserSenha\" name=\"formUpadateUserSenha\">";
echo "<input name=\"editSenha\" id=\"editSenha\" class=\"hidden\">";
echo "<button type=\"submit\" id=\"AlterarSenha\" class=\"hidden\">Alterar</button>";
echo "</form>";
echo "<form method=\"post\" id=\"formUpadateUserDel\" name=\"formUpadateUserDel\">";
echo "<input type=\"submit\" name=\"ApagarPerfil\" id=\"ApagarPerfil\" class=\"DeleteUser\" value=\"Apagar perfil\"></input>";
echo "</form>";
echo "</div>";
echo "</div>";
?>
  </main>

<!-- <footer>
          <<  
          >>
</footer>   -->
<script src="js/functions.js"></script>
<script>
const camera=document.getElementById("camera-edit");
const img_perfil=document.getElementById("img_perfil");
const file=document.getElementById("imgInp");
camera.addEventListener('click', () =>{
  file.click()
});
file.addEventListener('change', (event) =>{
  let reader = new FileReader();

  reader.onload = () => {
    img_perfil.style.backgroundImage=`url(${reader.result})`;
    if(window.confirm("Você deseja alterar sua foto?")) formPhoto.submit();
  }
  reader.readAsDataURL(file.files[0])
})

var tmpLocal = 0;
function addLocal(){
    var local = document.getElementById('select-local').value;
    local = JSON.parse(local);
    var table = document.getElementById('tableLocal');
    var option = document.getElementById('optionLocal'+local.id);
    option.remove();
    tmpLocal = local.id;
    table.innerHTML += `<tr id="row${local.id}"><td>${local.name}<input type="hidden" value="${local.id}" name="local" /></td><td><button onclick="removeLocal('${local.id}', '${local.name}')">❌</button></td></tr>`;
    document.getElementById('select-local').disabled = true;
    document.getElementById('select-local-button').disabled = true;
}
function removeLocal(id, name){
    var table = document.getElementById('tableLocal');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-local');
    select.innerHTML += `<option id='optionLocal${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
    tmpLocal = 0;
    document.getElementById('select-local').disabled = false;
    document.getElementById('select-local-button').disabled = false;
}

var pessoas = [];
function addPessoas(){
    var pessoa = document.getElementById('select-pessoas').value;
    pessoa = JSON.parse(pessoa);
    var table = document.getElementById('tablePessoas');
    var option = document.getElementById('optionPessoa'+pessoa.id);
    option.remove();
    pessoas.push(pessoa.id);
    table.innerHTML += `<tr id="row${pessoa.id}"><td>${pessoa.name}<input type="hidden" value="${pessoa.id}" name="pessoa${pessoa.id}" /></td><td><button onclick="removePessoas('${pessoa.id}', '${pessoa.name}')">❌</button></td></tr>`;
}
function removePessoas(id, name){
    var table = document.getElementById('tablePessoas');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-pessoas');
    select.innerHTML += `<option id='optionPessoa${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
    for(var i = 0; i < pessoas.length; i++){ 
        if ( pessoas[i] == id) {
            pessoas.splice(i, 1); 
        }    
    }
}

var assuntos = [];
function addAssuntos(){
    var assunto = document.getElementById('select-assuntos').value;
    assunto = JSON.parse(assunto);
    var table = document.getElementById('tableAssuntos');
    var option = document.getElementById('optionAssunto'+assunto.id);
    option.remove();
    assuntos.push(assunto.id);
    table.innerHTML += `<tr id="row${assunto.id}"><td>${assunto.name}<input type="hidden" value="${assunto.id}" name="assunto${assunto.id}" /></td><td><button onclick="removeAssuntos('${assunto.id}', '${assunto.name}')">❌</button></td></tr>`;
}
function removeAssuntos(id, name){
    var table = document.getElementById('tableAssuntos');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-assuntos');
    select.innerHTML += `<option id='optionAssunto${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
    for(var i = 0; i < assuntos.length; i++){ 
        if ( assuntos[i] == id) {
            assuntos.splice(i, 1); 
        }    
    }
}
function editName() {
    let input = document.getElementById("editName")
    if(input.className == ''){
        input.className = 'hidden'
        input.placeholder = 'Escreva seu usuário'
    } else{input.className = ''}
    let button = document.getElementById("AlterarName")
    if(button.className == 'addporto-confirm'){
        button.className = 'hidden'
    } else{button.className = 'addporto-confirm'}
}
function editEmail() {
    let input = document.getElementById("editEmail")
    if(input.className == ''){
        input.className = 'hidden'
        input.placeholder = 'Escreva seu email'
    } else{input.className = ''}
    let button = document.getElementById("AlterarEmail")
    if(button.className == 'addporto-confirm'){
        button.className = 'hidden'
    } else{button.className = 'addporto-confirm'}
}
function editSenha() {
    let input = document.getElementById("editSenha")
    if(input.className == ''){
        input.className = 'hidden'
        input.placeholder = 'Escreva sua nova senha'
    } else{input.className = ''}
    let button = document.getElementById("AlterarSenha")
    if(button.className == 'addporto-confirm'){
        button.className = 'hidden'
    } else{button.className = 'addporto-confirm'}
}
</script>
</body>
</html>
