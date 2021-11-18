<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <title>Dagama | Feed</title>
</head>
<body>
<?php
  include './backend/infra/connection.php';
  session_start();
  if(isset($_SESSION['userid'])){
    $user = getUserInfo("$_GET[user]");
    $limit = 10;
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $feedArray = getFeed($offset,$limit);
    $suggestFriends = suggestFriends($user['codigo'], 4, 0);
?>
  <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a a-selected\" href=feed.php?user=$_SESSION[userid]>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php?user=$_SESSION[userid]>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <!-- <aside class="container-left">
    <p>Portos Atracados:</p>
  </aside>
  <aside class="container-right">
    <p>Ondas do momento:</p>
  </aside> -->
  <main class="container-center">
<?php
    // initial insert post
    echo "<div class=\"insert-interacao\">";
    echo "<div class=\"insert-interacao-user\">";
    echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
    echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
    echo "</div>";
    echo "<form name=\"newPost\" action=\"\" method=\"\">";
    echo "<textarea name=\"texto\" class=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
    echo "<div class=\"insert-interacao-smallBtns\">";
    echo "<a class=\"insert-interacao-smallBtns-a\" href=\"\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</a>";
    echo "<a class=\"insert-interacao-smallBtns-a\" href=\"\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</a>";
    echo "<a class=\"insert-interacao-smallBtns-a\" href=\"\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</a>";
    echo "</div>";
    echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"insert-interacao-submit\" />";
    echo "</form>";
    echo "</div>";

    // add friends
    echo "<div class=\"add-amigo\">";
    echo "<div class=\"add-amigo-top\">";
    echo "<p class=\"add-amigo-suggesTxt\">Sugestão de amigos:</p>";
    echo "<p class=\"add-amigo-verMais\">VER MAIS</p>";
    echo "</div>";
    echo "<div class=\"add-amigo-cards\">";
    foreach ($suggestFriends as $person) {
      echo "<div id=\"".$person['codigo']."\" class=\"add-amigo-card\">";
      echo "<img class=\"add-amigo-card-icon\" src=\"".$person['img']."\" alt=\"\" srcset=\"\">";
      echo "<p class=\"add-amigo-card-name\">".$person['username']."</p>";
      echo "<input class=\"add-amigo-card-button\" type=\"submit\" value=\"Adicionar\" />";
      echo "</div>";        
    }
    echo "</div>";
    echo "</div>";

    // posts
    echo "</main>";
    // print_r($feedArray);
    // print_r($user);
    // print_r($suggestFriends);
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
</body>
</html>
