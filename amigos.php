<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="../styles.css">
  <title>Dagama | Navio</title>
</head>
<body class=perfil>
<?php
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  $user=[];
  
  if(!isset($_SESSION['userid'])){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  } else {
      if(isset($_GET['user'])){
        $amigosUser = getFriends($_GET['user'], 0, 10);
        if($_SESSION['userid'] == $_GET['user']){
          $amigos=getRequestAndFriends($_SESSION["userid"],false);
          if(isset($_POST['desfazerAmizade'])){
            $response = delFriend($_SESSION['userid'], $_POST['amigo']);
            if($response) header("refresh:1;url=amigos.php");
            else echo "Erro ao desfazer amizade...";
          }
        }
      } else {
        if($_SESSION['userid']){
          $amigos=getRequestAndFriends($_SESSION["userid"],false);
          $amigosUser = getFriends($_SESSION['userid'], 0, 10);
          if(isset($_POST['desfazerAmizade'])){
            $response = delFriend($_SESSION['userid'], $_POST['amigo']);
            if($response) header("refresh:1;url=amigos.php");
            else echo "Erro ao desfazer amizade...";
          }
        }
        else{
          echo "Usuario inválido";
          header("refresh:1;url=mar.php");
          die();
        }
      }
  }
?>
 <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <select id="select-filtro" name="select-filtro">
        <option value="nome">Nome</option>
        <option value="data">Data</option>
      </select>
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
<main>
    <div align=center>
    <!--Add onlick change-->
    <div class="container-center">
    <?php
      echo "<div class=\"mar-top-row\">";
        echo "<div class=\"order-btn\">";
        echo "<select id=\"select-ordenar\" name=\"select-ordenar\">";
          echo "<option value=\"nome\">Nome</option>";
          echo "<option value=\"data\">Data de criação</option>";
          echo "<option value=\"qtd\">Qtd interacoes</option>";
        echo "</select>";
        echo "<select id=\"select-ordenar-2\" name=\"select-ordenar-2\">";
          echo "<option value=\"cres\">Cres</option>";
          echo "<option value=\"decre\">Decre</option>";
        echo "</select>";
        echo "<button class=\"insert-interacao-submit\" name=\"ordenarBtn\">Ordenar<button/>";
        echo "<button class=\"btn-create-porto\"><a href=\"createPorto.php\">Criar um porto</a></button>";
        echo "</div>";
      echo "</div>";
      if(!isset($_GET['user']) && count($amigos) > 0){
        echo "<a href=solicitacoes.php class=header>Você tem ".count($amigos)." solicitações</a>";
      } else {
        if($_SESSION['userid'] == $_GET['user'] && count($amigos) > 0){
          echo "<a href=solicitacoes.php class=header>Você tem ".count($amigos)." solicitações</a>";
        }
      }
      echo "<div class=\"div-amigo\">";
      if(count($amigosUser)>0){
        foreach ($amigosUser as $amigo) {
          echo "<div class=\"div-amigo-row\">";
          echo "<div class=\"row\">";
            echo "<a href=navio.php?user=$amigo[amigoCod]><img src=\"$amigo[imgAmigo]\" alt=\"\" class=\"div-amigo-image\"></a>";
            echo "<div class=\"div-amigo-textos\">";
              echo "<a href=navio.php?user=$amigo[amigoCod]><p style=\"color: #fff\">$amigo[nameAmigo]</p></a>";
              echo "<p class=\"\">Amigos desde $amigo[dateAceito]</p>";
            echo "</div>";
            echo "</div>";
            if(isset($_GET['user'])){
              if($_SESSION['userid'] == $_GET['user']){
                echo "<form action=\"amigos.php\" method=\"post\">";
                echo "<button class=\"insert-interacao-submit\" name=\"desfazerAmizade\">Desfazer Amizade<button/>";
                echo "<input type=\"hidden\" name=\"amigo\" value=\"$amigo[amigoCod]\" />";
                echo "</form>";
              }
            } else {
              echo "<form action=\"amigos.php\" method=\"post\">";
              echo "<button class=\"insert-interacao-submit\" name=\"desfazerAmizade\">Desfazer Amizade<button>";
              echo "<input type=\"hidden\" name=\"amigo\" value=\"$amigo[amigoCod]\" />";
              echo "</form>";
            }
          echo "</div>";          
        }
      }
      else echo "<p>Você Não tem amigos ainda</p>";
    ?>
    </div>
</main>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<!-- <?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?> -->

</body>
</html>
