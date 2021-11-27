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
  $amigos=getRequestAndFriends($_SESSION["userid"],true);
  // if(!$amigos){
  //     echo "<h2 align=center>Usuario Inválido</h2>";
  //     header('refresh:1;url=mar.php');
  // }

?>
 <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
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
      echo "<div class=\"header-searchBar\">";
      echo "<img class=\"header-searchBar-icon\" src=\"imgs/icons/search.png\">";
      echo "<input class=\"header-searchBar-input\" type=text placeholder=\"digite o nome do seu amigo\" />";
      echo "</div>";
      echo "<div class=\"div-amigo\">";
      // if(count($amigos)>0){
        foreach($amigos as $amigo){
          echo "<p>$amigo[amigo] ";
        };
        echo "<div class=\"div-amigo-row\">";
        echo "<div class=\"row\">";
          echo "<img src=\"imgs/icons/user-icon.png\" alt=\"\" class=\"div-amigo-image\">";
          echo "<div class=\"div-amigo-textos\">";
            echo "<p class=\"\">Joaquino corno</p>";
          echo "</div>";
          echo "</div>";
          echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"insert-interacao-submit\" value=\"Aceitar\" />";
          echo "<input class=\"insert-interacao-submit red\" type=\"submit\" name=\"insert-interacao-submit\" value=\"Recusa\" />";
        echo "</div>";
      // }
      // else echo "<p>Você Não tem amigos</p>";
    ?>
    </div>
</main>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<!-- <?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?> -->

</body>
</html>
