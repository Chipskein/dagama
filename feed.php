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
    $user = getUserInfo("$_SESSION[userid]");
    $limit = 10;
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $feedArray = getFeed($offset,$limit);
    $suggestFriends = suggestFriends($_SESSION['userid'], 4, 0);
    $errorMessage = [];

    // var_dump($_POST);
    // sendFriendRequest para enviar solicitacao
    if(isset($_POST['sendFriendRequest'])){
      $erros = [];
      if(!preg_match('#^[0-9]+$#', $_POST['sendFriendRequest'])){
        $erros[] = "A pessoa precisa ser um número";
      } else {
        $response = getRequestAndFriends($user['codigo'], true);
        for($c = 0; $c < count($response); $c++) {
          if($response[$c]['amigo'] == $_POST['sendFriendRequest'] && $response[$c]['ativo'] == 1){
            $erros[] = "Solicitação já enviada";
          }
          if(($response[$c]['otherPerfil'] != $user['codigo'] ? $response[$c]['otherPerfil'] : $response[$c]['otherAmigo']) == $_POST['sendFriendRequest'] && $response[$c]['otherAtivo'] == 1){
            $erros[] = "Você já é amigo deste usuário";
          }
        }
      }
      if($erros == []){
        sendFriendRequest($user['codigo'], $_POST['sendFriendRequest']);
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
      } else {
        $errorMessage['friendRequest'] = ['sendFriendRequest', $_POST['sendFriendRequest'], implode(', ', $erros)];
      }
    } 
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
    echo array_key_exists('friendRequest',$errorMessage) ? $errorMessage['friendRequest'][2] : '';
    // modal pros erros
    // echo "<div id=\"abrirModal\" class=\"modal\">";
    // echo "<div onclick=\"closeModal('abrirModal')\" class=\"fechar\">x</div>";
    // echo "<h2>".$errorMessage['friendRequest']."</h2>";
    // echo "<p>Err message: ".$errorMessage['friendRequest'][2]."</p>";
    // echo "</div>";

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
    if(count($suggestFriends) > 0){
      echo "<div class=\"add-amigo\">";
      echo "<div class=\"add-amigo-top\">";
      echo "<p class=\"add-amigo-suggesTxt\">Sugestão de amigos:</p>";
      echo "<p class=\"add-amigo-verMais\">VER MAIS</p>";
      echo "</div>";
      echo "<div class=\"add-amigo-cards\">";
      foreach ($suggestFriends as $person) {
        echo "<div id=\"card".$person['codigo']."\" class=\"add-amigo-card\">";
        echo "<img class=\"add-amigo-card-icon\" src=\"".$person['img']."\" alt=\"\" srcset=\"\">";
        echo "<p class=\"add-amigo-card-name\">".$person['username']."</p>";
        echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\" >";
        if($person['enviado'] == 'true' || (isset($_POST['sendFriendRequest']) && $_POST['sendFriendRequest'] == $person['codigo'])){
          echo "<input type=\"hidden\" name=\"unsendFriendRequest\" value=\"".$person['codigo']."\" />";
          echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button-selected\" disabled  type=\"submit\" value=\"Enviado\" />";  
        }
        if($person['recebido'] != 'true' && $person['enviado'] != 'true' && (isset($_POST['sendFriendRequest']) ? $_POST['sendFriendRequest'] != $person['codigo'] : true)) {
          echo "<input type=\"hidden\" name=\"sendFriendRequest\" value=\"".$person['codigo']."\" />";
          echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button\"  type=\"submit\" onclick=\"
            let cardInput = document.getElementById('cardInput'+".$person['codigo'].");
            cardInput.className = 'add-amigo-card-button-selected'; cardInput.value = 'Enviado'";
          echo "\" value=\"Adicionar\" />";
        }
        echo "</form>";
        echo "</div>";        
      }
      echo "</div>";
      echo "</div>";
    }

    // posts
    echo "</main>";

    // print_r($feedArray);
    // print_r($user);
    // foreach ($suggestFriends as $value) {
    //   echo "$value[codigo] - $value[username] - $value[enviado] - $value[recebido]";
    //   echo "<br>";
    // }
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
<div onclick="openModal('abrirModal')" ><p>Open Modal</p></div>
<script src="functions.js"></script>
</body>
</html>
