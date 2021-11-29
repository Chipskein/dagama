<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
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
    $portosArray = getAllPorto($_SESSION['userid'], true, 0, 3);
    $errorMessage = [];
    // if(isset($_POST['novoPost'])){
    //   $response = addInteracao($perfil, $texto, $perfil_posting, $porto, $isSharing, $post, $isReact)
    // }

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
    // print_r($portosArray);
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
<div id=principal>
  <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a a-selected\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <aside id=direita>
    <div align=center class=background2>
      <p class=portosAtracados>Portos atracados:</p>
      <?php
      if($portosArray){
        foreach ($portosArray as $value) {
          echo "<div class=\"row porto-feed-container\">
            <div class=\"portos-img\" style=\"background-image: url($value[img])\"></div>
            <a class=nomePort href=porto.php?porto=$value[codigo]>$value[nome]</a>
          </div>";
        }
        echo "<br><a class=portosAtracadosMais href=portosUser.php>Ver todos</a>";
      } else {
        echo "<p>Você não está em nenhum porto ainda</p>";
      }
      ?>
    </div>
  </aside>
  <aside id=esquerda>
  <div align=center class=background2>
        <p class=portosAtracados>Ondas do momento:</p>
      <div align=start>
        <p class=trending>1ª Elon musk</p>
        <p class=trending>1ª Elon musk</p>
        <p class=trending>1ª Elon musk</p>
      </div>
    </div>
  </aside>
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
        echo "<input list=\"local\">";
        echo "<datalist id=\"local\" name=\"local\">";
          foreach ($locaisArray as $value) {
            echo "<option value=\"".$value['nomeCidade']."\"></option>";
          }
          echo "<option value=\"Outro\">";
        echo "</datalist>";
        echo "</div>";
          
        echo "<div class=\"post-divPessoas\">";
        echo "<input list=\"pessoas\">";
        echo "<datalist id=\"pessoas\" name=\"pessoas\">";
          foreach ($pessoasArray as $value) {
            echo "<option value=\"".$value['username']."\">";
            echo "<img class=\"post-selectUser-icon\" src=\"".$value["img"]."\" alt=\"\" srcset=\"\">";
            echo $value['username']."</option>";
          }
        echo "</datalist>";
        echo "</div>";

        echo "<div class=\"post-divAssuntos\">";
        echo "<input list=\"assuntos\">";
        echo "<datalist id=\"assuntos\" name=\"assuntos\">";
        foreach ($assuntosArray as $value) {
          echo "<option value=\"".$value['nome']."\">";
        }
        echo "<option value=\"Outro\">";
        echo "</datalist>";
        echo "</div>";

      echo "</form>";
    echo "</div>";

    // add friends FIXME:
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
    if($postsArray){
      foreach ($postsArray as $post) {
        echo "<div class=\"div-post\">";
          //Top
          echo "<div class=\"div-post-top\">";
            echo "<img src=\"".$post['iconPerfil']."\" alt=\"\" class=\"div-post-top-icon\">";
            echo "<div class=\"div-post-top-infos\">";
              echo "<p class=\"div-post-top-username\"><i>@".$post['nomePerfil']."</i> ".$post['dataPost']."</p>";
              echo "<p class=\"div-post-top-subjects\"><b>";
              $tmpArray = [];
              // print_r($post['assuntos']);
              foreach($post['assuntos'] as $elements){
                foreach ($elements as $key => $value) {
                  if($key === 'nomeAssunto') $tmpArray[] = $value;
                }
              }
              echo implode($tmpArray, ', ');
              echo "</b></p>";
            echo "</div>";
          echo "</div>";
          //Texto
          echo "<div class=\"div-post-txt\">";
            echo "<p>$post[textoPost]</p>";
          echo "</div>";
          //Ícones
          echo "<div class=\"div-post-icons-bar\">";
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>12</p><img src=\"imgs/icons/Like.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>5</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>2</p><img src=\"imgs/icons/send.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
          echo "</div>";
          echo "<br><br>";
          //Comentários
          if($post['comentarios'] && $post['comentarios'] != []){
            echo "<hr class=\"post-hr\">";
            foreach ($post['comentarios'] as $elem) {
              echo "<div class=\"comment-container\">";
                echo "<div class=\"comment-container-top\">";
                  echo "<img src=\"".$elem['iconPerfil']."\" alt=\"\" class=\"comment-icon\">";
                  echo "<p class=\"comment-txt\"><i>@".$elem['nomePerfil']."</i> ";
                  echo ($elem['textoPost'] ? $elem['textoPost'] : '');
                  echo ", em ".$elem['dataPost'];
                  echo "</p>";
                echo "</div>";
                echo "<div class=\"comment-reagir\"><p>Reagir</p></div>";
              echo "</div>";
            }
          }
          // echo "<div class=\"comment-container\">";
          //   echo "<div class=\"comment-container-top\">";
          //     echo "<img src=\"imgs/icons/user-icon.png\" alt=\"\" class=\"comment-icon\">";
          //     echo "<p class=\"comment-txt\">@asdasddsa reagiu com <img src=\"imgs/icons/reactions/laughing.png\" alt=\"\" class=\"comment-emoji\">, marcando @bigSmoke2002 - “Olha isso aquLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, ali man”, em  Rio Grande, RS - 10/09/2021 00:00</p>";
          //   echo "</div>";
          //   echo "<div class=\"comment-reagir\"><p>Reagir</p></div>";

          //     echo "<div class=\"inner-comment-container\">";
          //       echo "<div class=\"comment-container-top\">";
          //         echo "<img src=\"imgs/icons/user-icon.png\" alt=\"\" class=\"comment-icon\">";
          //         echo "<p class=\"comment-txt\">@asdasddsa reagiu com <img src=\"imgs/icons/reactions/laughing.png\" alt=\"\" class=\"comment-emoji\">, marcando @bigSmoke2002 - “Olha isso aquLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, ali man”, em  Rio Grande, RS - 10/09/2021 00:00</p>";
          //       echo "</div>";
          //       echo "<div class=\"comment-reagir\"><p>Reagir</p></div>";
          //     echo "</div>";

                  
          //   echo "<div class=\"comment-container-top\">";
          //     echo "<img src=\"imgs/icons/user-icon.png\" alt=\"\" class=\"comment-icon\">";
          //     echo "<p class=\"comment-txt\">@asdasddsa reagiu com <img src=\"imgs/icons/reactions/laughing.png\" alt=\"\" class=\"comment-emoji\">, marcando @bigSmoke2002 - “Olha isso aquLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, alLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet lectus sodales, varius dui ac, ali man”, em  Rio Grande, RS - 10/09/2021 00:00</p>";
          //   echo "</div>";
          //   echo "<div class=\"comment-reagir\"><p>Reagir</p></div>";

          // echo "</div>";
        echo "</div>";
      }
    }

    echo "</main>";
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  ?>
  </div>
<!-- <div onclick="openModal('abrirModal')" ><p>Open Modal</p></div> -->
<script src="functions.js"></script>
</body>
</html>
