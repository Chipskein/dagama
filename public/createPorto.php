<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css" media="screen and (max-width: 1680px)"/>
    <title>Dagama | Criar Porto</title>
</head>
<body>
<?php
  include '../backend/infra/services.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    // var_dump($_POST);
    // var_dump($_FILES);
    if(isset($_POST['confirmar'])){
      $erros = [];
      if(!isset($_POST['nome']) || !isset($_POST['descricao'])){
        $erros[] = "Campos faltando";
      } else {
        $regex = "/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ,.\-_:]+)?)+$/";
        if(!preg_match($regex, $_POST['descricao'])){
          $erros[] = "Descricão inválida = ".preg_match($regex, $_POST['descricao']);
        }
        if(strlen($_POST['descricao']) > 250){
          $erros[] = "Descrição grande demais";
        }
        if(!preg_match($regex, $_POST['nome'])){
          $erros[] = "Nome inválido = ".preg_match($regex, $_POST['nome']);
        }
        if(strlen($_POST['nome']) > 250){
          $erros[] = "Nome grande demais";
        }
        if(trim($_POST['nome']) == ""){
          $erros[] = "Nome não pode ser em branco";
        }
        if(isset($_FILES['photo'])){
          if($_FILES['photo']['name'] != ""){
            if($_FILES['photo']['type'] == ""){
              $erros[] = "Tipo da imagem inválido";
            }
            if($_FILES['photo']['size'] == ""){
              $erros[] = "Tamanho da imagem inválido";
            }
            if($_FILES['photo']['error'] !== 0 ){
              $erros[] = "Ocorreu um erro ao fazer upload dessa imagem";
            }
          }
        }
      }

      if($erros == []){
        $perfil = $_SESSION['userid'];
        $nome = $_POST['nome'];

        $descr = $_POST['descricao'];
        $img = is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo'] : null;
        $id = addPorto($perfil, $nome, $descr, $img);
        if($id) {
          header("refresh:1;url=porto.php?porto=$id");
          die();
        } else {
          echo "Erro!";
        }
      } else {
        echo "Erro: ".implode(',',$erros);
      }
    }
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
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
      echo "<a class=\"header-links-a a-selected\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main class="container-center">
    <div class="addporto-form-container">
      <form action="createPorto.php" method="post" id="formAddPorto" name="formAddPorto" enctype="multipart/form-data">
        <div id="porto_img_banner" class="addporto-img"></div>
        <input id="imgInp" type="file"  name="photo">
        <p class="addporto-main-txt">Cadastro de Porto</p>
        <div class="addporto-inputs">
          <div class="addporto-input-container">
            <p class="addporto-input-label">Nome: </p><input class="inputs" name="nome" type="text" required>
          </div>
          <div class="addporto-input-container">
            <p class="addporto-input-label">Descrição: </p><textarea class="addporto-input-txtarea" name="descricao" required></textarea>
          </div>
        </div>
        <input type="submit" name="confirmar" value="Criar Porto" class="addporto-confirm">
      </form>
    </div>
<?php

?>
  </main>

<script>
    imgInp.onchange = evt => {
        const [file] = imgInp.files
        const img_perfil=document.getElementById("porto_img_banner");
        if (file) {
            img_perfil.style.backgroundImage=`url(${URL.createObjectURL(file)})`;
        }
        else{
            img_perfil.style.backgroundImage="url(imgs/icon.png)";
        }
    }
</script>
</body>
</html>