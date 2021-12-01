<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
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
  }
  else{
    if(isset($_GET['user'])){
        $postsArray = getPostsOnUser($_GET['user'], 0, 10);
        $user=getUserInfo("$_GET[user]");
        if(!$user){
          echo "Usuario inválido";
          header("refresh:1;url=mar.php");
          die();
        }
        $isOwner= "$_GET[user]"=="$_SESSION[userid]" ? true:false;
        $portosArray = getAllPorto($_GET['user'], true, 0, 3);
        $portosUser = getUserPortoQtd($_GET['user']);
        $amigosUser = getFriends($_GET['user'], 0, 3);
      }
      else{
        echo "Usuario inválido";
        header("refresh:1;url=mar.php");
        die();
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
      echo "<a class=\"header-links-a\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <aside id=direita>
    <div align=center class=background>
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
        echo "<p>Sem portos ainda</p>";
      }
      ?>
    </div>
  </aside>
  <main>
    <div align=center>
    <!--Add onlick change-->
      <br>
      <div id="img_perfil" class=perfil></div>
      <input id="imgInp" class="hidden" type="file" name="photo">
      <?php 
        if($isOwner)echo "<div id=camera-icon></div>";
      ?>
      <?php echo "<h3 class=perfil>$user[username]</h3>";?>
    </div>
    <br>
    <div align=center>
        <div class=perfil-amigos>
          <?php
            echo "<a href=amigos.php?user=$_GET[user] class=amigos> Amigos: ".($amigosUser ? $amigosUser[0]['qtdAmigos'] : 0)."</a>";
            if($isOwner) echo "<h3><a href=portosUser.php?owner class=amigos >Seus Portos: $portosUser</a></h3>";
          ?>
        </div>
    </div>
    <br>
    <div class="container-center">
    <?php
      echo "<div class=\"center\">";
      echo "<div class=\"insert-interacao\">";
      echo "<div class=\"insert-interacao-user\">";
      echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$_SESSION["userimg"]."\" alt=\"\" srcset=\"\">";
      echo "<p class=\"insert-interacao-user-name\">".$_SESSION["username"].":</p>";
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
      echo "</div>";

      // posts
    if($postsArray){
      foreach ($postsArray as $post) {
        echo "<div class=\"div-post\">";
          //Share
          if($post['isSharing']){
            $sharedPost = getOriginalPost($post['codPost']);
            echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
            echo "<div class=\"div-sharing-post\">";
              // Sharing-top
              echo "<div class=\"div-sharing-post-top\">";
                echo "<img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\">";
                echo "<div class=\"div-sharing-post-top-infos\">";
                  echo "<p class=\"div-sharing-post-top-username\"><i>@".$sharedPost['nomePerfil']."</i> ".$sharedPost['dataPost']."</p>";
                  echo "<p class=\"div-sharing-post-top-subjects\"><b>";
                  $tmpArray = [];
                  foreach($sharedPost['assuntos'] as $elements){
                    foreach ($elements as $key => $value) {
                      if($key === 'nomeAssunto') $tmpArray[] = $value;
                    }
                  }
                  echo implode($tmpArray, ', ');
                  echo "</b></p>";
                echo "</div>";
              echo "</div>";
              // Sharing-texto
              echo "<div class=\"div-sharing-post-txt\">";
                echo "<p>$sharedPost[textoPost]</p>";
              echo "</div>";
            echo "</div>";
          }
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
            // echo "<div class=\"div-post-icons-bar-divs\">";
            //   echo "<p>12</p><img src=\"imgs/icons/Like.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            // echo "</div>";
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>5</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            // echo "<div class=\"div-post-icons-bar-divs\">";
            //   echo "<p>2</p><img src=\"imgs/icons/send.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            // echo "</div>";
            echo "<div class=\"div-post-icons-bar-interagir\">";
              echo "<img src=\"$user[img]\" class=\"div-post-icons-bar-interagir-icon\" alt=\"\"><p>Interagir...</p>";
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
        echo "</div>";
      }
    }
    ?>
    </div>
</main>
<aside id=esquerda>
    <div align=center class=background>
      <div>
        <p class=SeusAmigos>Seus amigos </p>
      </div>
      <?php
        if(count($amigosUser) > 0){
          foreach ($amigosUser as $amigo) {
            echo "<a href=navio.php?user=$amigo[amigoCod]><div><img src=$amigo[imgAmigo] class=div-amigo-image><p class=nomeAmigo>$amigo[nameAmigo]</p></div></a>";
          }
        } else {
          echo "<p>Você ainda não tem nenhum amigo</p>";
        }
        
        echo "<a class=portosAtracadosMais href=amigos.php?user=$_GET[user]>Ver mais</a>"
      ?>
    </div>
</aside>
<?php 
  echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>
</div>
<!-- <footer>
          <<  
          >>
</footer>   -->
<script>
      const camera=document.getElementById("camera-icon");
      const img_perfil=document.getElementById("img_perfil");
      const file=document.getElementById("imgInp");
      camera.addEventListener('click', () =>{
        file.click()
      });
      file.addEventListener('change', (event) =>{
        let reader = new FileReader();

        reader.onload = () => {
          img_perfil.style.backgroundImage=`url(${reader.result})`;
        }
        reader.readAsDataURL(file.files[0])
      })
  </script>
</body>
</html>
