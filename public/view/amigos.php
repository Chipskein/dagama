<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title='Amigos';
  require 'components/head.php';
?>
<body class=perfil>
<?php

  $user=[];
  /*
  if(!isset($_SESSION['userid'])){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  } else {
    if(isset($_GET['username'])){
      $where = $_GET['username'];
    } else{
      $where = '';
    }
      if(isset($_GET['user'])){
        $offset=0;
        $total = 5;
        $limit = 5;
        $amigosUser =[];//getFriends($_GET['user'], $offset, 10,$where);
        if($_SESSION['userid'] == $_GET['user']){
          $amigos=[];//getRequestAndFriends($_SESSION["userid"],false);
          if(isset($_POST['desfazerAmizade'])){
            $response = delFriend($_SESSION['userid'], $_POST['amigo']);
            if($response) header("refresh:1;url=amigos.php");
            else echo "Erro ao desfazer amizade...";
          }
        }
      } else {
        if($_SESSION['userid']){
          $where = $_GET['username'] ? $_GET['username'] : '';
          $amigos=[];//getRequestAndFriends($_SESSION["userid"],false);
          $amigosUser = [];//getFriends($_SESSION['userid'], $offset, $limit, $where);
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
  */
?>
<?php
  require 'components/header.php';
?>
<main>
    <div align=center>
    <!--Add onlick change-->
    <div class="container-center">
    <?php
      echo "<form class=\"header-searchBar\" name=\"search\" action=\"amigos.php\" method=\"get\">";
      echo "<input class=\"header-searchBar-input\" name=\"username\" type=text placeholder=\"digite o nome do seu amigo\" />";
      echo "<button><img class=\"header-searchBar-icon2\" src=\"/imgs/icons/search.png\"></button>";
      echo "</form>";


      if(!isset($_GET['user']) && count($amigos) > 0){
        if(count($amigos) == 1) echo "<a href=solicitacoes.php class=paddingVertical>Você tem ".count($amigos)." solicitação de amizade</a>";
        if(count($amigos) > 1) echo "<a href=solicitacoes.php class=paddingVertical>Você tem ".count($amigos)." solicitações de amizade</a>";
      } else {
        if(isset($_GET['user'])){
          if($_SESSION['userid'] == $_GET['user'] && count($amigos) > 0){
            if(count($amigos) === 1) echo "<a href=solicitacoes.php class=paddingVertical>Você tem ".count($amigos)." solicitação de amizade</a>";
            if(count($amigos) > 1) echo "<a href=solicitacoes.php class=paddingVertical>Você tem ".count($amigos)." solicitações de amizade</a>";
          }
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
 <?php 
    $route="amigos";
    require 'components/FooterPage.php';
  ?> 
</main>

</body>
</html>
