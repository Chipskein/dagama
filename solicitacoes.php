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
  $amigos=getRequestAndFriends($_SESSION["userid"],false);
  // var_dump($amigos);
  if(isset($_POST['insert-interacao-accept'])){
    $response = confirmFriendRequest($_SESSION["userid"], (int)$_POST['insert-interacao-accept']);
    if($response) header("refresh:1;url=solicitacoes.php");
    else echo "Erro ao aceitar solicitação amizade...";
  }
  if(isset($_POST['insert-interacao-decline'])){
    echo 'texte';
    $response = declineFriendRequest($_SESSION["userid"],$_POST["insert-interacao-decline"]);
    if($response) header("refresh:1;url=solicitacoes.php");
    else echo "Erro ao rejeitar solicitação amizade...";
  }
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
      // if(count($amigos)>0){
        foreach($amigos as $amigo){
          echo "<div class=\"margin-top\">";
          echo "<div class=\"div-amigo\">";
          echo "<div class=\"div-amigo-row\">";
          echo "<div class=\"row\">";
            echo "<img src=\"$amigo[img]\" alt=\"\" class=\"div-amigo-image\">";
            echo "<div>";
              echo "<p class=\"nome-amigo-solicitacao\">$amigo[nome]</p>";
              echo "<p class=\"data-amigo-solicitacao\">Data de envio: $amigo[data]</p>";
              echo "</div>";
              echo "</div>";
            echo "<form name=\"addAmigo\" action=\"solicitacoes.php\" method=\"post\" >";
              echo "<button class=\"add-amigo-card-button-solicitacao\">Aceitar</button>";
              echo "<input class=\"hidden\" type=\"hidden\" name=\"insert-interacao-accept\" value=".$amigo['amigocod']." />";
            echo "</form>";
            echo "<form name=\"DelAmigo\" action=\"solicitacoes.php\" method=\"post\" >";
            echo "<button class=\"remove-amigo-card-button-solicitacao\">Rejeitar</button>";
            echo "<input class=\"hidden\" type=\"hidden\" name=\"insert-interacao-decline\" value=".$amigo['amigocod']." />";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        };
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
