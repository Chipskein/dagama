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
        $where = $_GET['username'] ? $_GET['username'] : '';
        $amigosUser = getFriends($_GET['user'], 0, 10,$where);
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
          $where = $_GET['username'] ? $_GET['username'] : '';
          $amigos=getRequestAndFriends($_SESSION["userid"],false);
          $amigosUser = getFriends($_SESSION['userid'], 0, 10, $where);
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
      echo "<form class=\"header-searchBar\" name=\"search\" action=\"amigos.php\" method=\"get\">";
      echo "<input class=\"header-searchBar-input\" name=\"username\" type=text placeholder=\"digite o nome do seu amigo\" />";
      echo "<button><img class=\"header-searchBar-icon2\" src=\"imgs/icons/search.png\"></button>";
      echo "</form>";


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
      else echo "<p>Não foi encontrado amigos</p>";
    ?>
    </div>
</main>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<!-- <?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?> -->

</body>
</html>
