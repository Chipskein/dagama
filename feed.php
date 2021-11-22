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
    $locaisArray = getLocais();
    $assuntosArray = getAssuntos();
    $pessoasArray = getPessoas();
    $suggestFriends = suggestFriends($_SESSION['userid'], 4, 0);
    $postsArray = getPosts(0, 10);
    $errorMessage = [];

    // var_dump($_POST);
    // echo "<br>";
    // print_r($locaisArray);
    // echo "<br>";
    // print_r($assuntosArray);
    // echo "<br>";
    // print_r($pessoasArray);
    // echo "<br>";
    // print_r($postsArray);
    // echo "<br>";

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
      echo "<form name=\"newPost\" action=\"feed.php?user=$_SESSION[userid]\" method=\"post\" >";
        echo "<textarea name=\"texto\" class=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
        echo "<div class=\"insert-interacao-smallBtns\">";
          echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
          echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
          echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
        echo "</div>";
        echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"novoPost\" />";
        echo "<hr id=\"post-hr\" class=\"post-hr\" >";
        echo "<div class=\"post-divLocal\">";
        echo "<select name=\"local\">";
          foreach ($locaisArray as $value) {
            echo "<option value=\"".$value['codCidade']."\">".$value['nomeCidade'].", ".$value['nomeUf']." - ".$value['nomePais']."</option>";
          }
          echo "<option value=\"0\">Outro</option>";
        echo "</select>";
        echo "</div>";
          
        echo "<div class=\"post-divPessoas\">";
        echo "<select name=\"pessoas\">";
          foreach ($pessoasArray as $value) {
            echo "<option value=\"".$value['codigo']."\">";
            echo "<img class=\"post-selectUser-icon\" src=\"".$value["img"]."\" alt=\"\" srcset=\"\">";
            echo $value['username']."</option>";
          }
        echo "</select>";
        echo "</div>";

        echo "<div class=\"post-divAssuntos\">";
        echo "<select name=\"assuntos\">";
        foreach ($assuntosArray as $value) {
          echo "<option value=\"".$value['codigo']."\">".$value['nome']."</option>";
        }
        echo "<option value=\"0\">Outro</option>";
        echo "</select>";
        echo "</div>";

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
?>
  <div class="div-post">
    <div class="div-post-top">
      <img src="imgs/icons/user-icon.png" alt="" class="div-post-top-icon">
      <div class="div-post-top-infos">
        <p class="div-post-top-username">@fulaninho 20/04/2021 00:00</p>
        <p class="div-post-top-subjects"><b>Cripto moedas, bitcoin e elon musk...</b></p>
      </div>
    </div>
    <div class="div-post-txt">
      <p><b>@pedrinho66</b> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, aliquet dui. Morbi bibendum sem odio, nec cursus dolor bibendum vel. Vestibulum cursus magna ante, commodo sagittis lorem semper in. Etiam non iaculis lectus. In hac habitasse platea dictumst. In id tristique diam, id posuere eros. Phasellus at lorem fermentum, ullamcorper mauris eget, cursus lectus. Quisque elementum ipsum urna, sed ornare dolor dictum et. Etiam condimentum odio ante, et tristique augue rutrum sit amet. Cras faucibus nibh enim, ac facilisis mi ornare sit amet. Donec vitae posuere lacus. Pellentesque rutrum ac ex lacinia ultrices. Ut sodales quam quis est dapibus lacinia. Maecenas leo turpis, luctus non justo at, dapibus consequat neque.</p>
    </div>
    <div class="div-post-icons-bar">
      <div class="div-post-icons-bar-divs">
        <p>12</p><img src="imgs/icons/Like.png" class="div-post-icons-bar-icons" alt="">
      </div>
      <div class="div-post-icons-bar-divs">
        <p>5</p><img src="imgs/icons/chat.png" class="div-post-icons-bar-icons" alt="">
      </div>
      <div class="div-post-icons-bar-divs">
        <p>2</p><img src="imgs/icons/send.png" class="div-post-icons-bar-icons" alt="">
      </div>
    </div>
    <br>
    <br>
    <hr class="post-hr">
  </div>
<?php

    echo "</main>";
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
<!-- <div onclick="openModal('abrirModal')" ><p>Open Modal</p></div> -->
<script src="functions.js"></script>
</body>
</html>
