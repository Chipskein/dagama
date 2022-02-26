<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <title>Dagama | Porto</title>
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
      if(!isset($_GET['porto'])){
            echo "<h2 align=center>Porto inválido!</h2>";
            header("refresh:1;url=mar.php");
      } else {
        if($_SESSION['userid']){
          $participantesPorto = getPortoParticipants($_GET['porto'], 0, 5);
          $portoInfo = getPortInfo($_GET['porto'], $_SESSION['userid']);
          if(isset($_POST['removerParticipante'])){
            $response = sairPorto($_POST['participante'], $_GET['porto']);
            if($response) header("refresh:1;url=participantesPorto.php?porto=$_GET[porto]");
            else echo "Erro ao remover participante...";
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
      echo "<a class=\"header-links-a\" href=feed.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
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
      echo "<input class=\"header-searchBar-input\" type=text placeholder=\"Procure um participante\" />";
      echo "</div><br>";
      echo "<div class=\"div-amigo\">";
      if(count($participantesPorto)>0){
        foreach ($participantesPorto as $part) {
          echo "<div class=\"div-amigo-row\">";
          echo "<div class=\"row\">";
            echo "<a href=navio.php?user=$part[codPart]><img src=\"$part[imgPart]\" alt=\"\" class=\"div-amigo-image\"></a>";
            echo "<div class=\"div-amigo-textos\">";
              echo "<a href=navio.php?user=$part[codPart]><p style=\"color: #fff\">$part[nomePart]</p></a>";
              echo "<p class=\"\">Participante desde $part[dataRegis]</p>";
            echo "</div>";
            echo "</div>";
            if($portoInfo['owner']){
                echo "<form action=\"participantesPorto.php?porto=$_GET[porto]\" method=\"post\">";
                echo "<button class=\"insert-interacao-submit\" name=\"removerParticipante\">Remover participante<button/>";
                echo "<input type=\"hidden\" name=\"participante\" value=\"$part[codPart]\" />";
                echo "</form>";
            }
          echo "</div>";          
        }
      }
      else echo "<p>Este porto ainda não tem participantes</p>";
    ?>
    </div>
</main>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<!-- <?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?> -->

</body>
</html>
