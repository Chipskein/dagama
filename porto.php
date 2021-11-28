<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <title>Dagama | Porto</title>
</head>
<body>
<?php
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    //validar porto
    if(isset($_GET['porto'])){
      if(isset($_POST['entrarPorto'])){
        $response = entrarPorto($_SESSION['userid'], $_GET['porto']);
        if(!$response){
          echo "Erro ao entrar no porto";
        } else {
          header("refresh:0;url=porto.php?porto=$_GET[porto]"); 
        }
      }
      if(isset($_POST['sairPorto'])){
        $response = sairPorto($_SESSION['userid'], $_GET['porto']);
        if(!$response){
          echo "Erro ao sair do porto";
        } else {
          header("refresh:0;url=porto.php?porto=$_GET[porto]");
        }
      }

      $portoInfo=getPortInfo($_GET['porto'], $_SESSION['userid']);
      if($portoInfo){
        // var_dump($portoInfo);
      }
      else{
        echo "<h2 align=center>Porto Inválido</h2>";
        header("refresh:1;url=mar.php");
        die();
      }
    }
    else {
      echo "<h2 align=center>Porto Inválido</h2>";
      header("refresh:1;url=mar.php");
      die();
    }
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
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
      echo "<a class=\"header-links-a a-selected\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <aside class="container-aside-porto" align=center>
    <?php 
      echo "<div class=\"porto-img\" style=\"background-image: url($portoInfo[img])\"></div>";
      echo "<p class=portoTitle>$portoInfo[nome]</p>";
      echo "<div class=\"portoDesc\"><p>$portoInfo[descr]</p></div>";
      echo "<form action=\"porto.php?porto=$portoInfo[codigo]\" name=\"porto-form\" method=\"post\" >";
      if($portoInfo['participa']  && !$portoInfo['owner']){
        echo "<button class=\"porto-sair-btn\"><p class=\"porto-entrar-btn-txt\">Sair</p></button>";
        echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
      }
      if(!$portoInfo['participa'] && !$portoInfo['owner']){
        echo "<button class=\"porto-entrar-btn\"> <p class=\"porto-entrar-btn-txt\">Entrar</p></button>";
        echo "<input type=\"hidden\" name=\"entrarPorto\" value=\"entrar\"/>";
      }
      if($portoInfo['owner']){
        echo "<div class=\"porto-sair-btn\"> <p class=\"porto-entrar-btn-txt\">Editar porto</p></div>";
        echo "<input type=\"hidden\" name=\"editarPorto\" value=\"editar\"/>";
      }
      echo "</form>";
    ?>
  </aside>
  <main class="container-main-porto">
    <div class="container-center">
    <?php
      echo "<div class=\"insert-interacao\">";
        echo "<div class=\"insert-interacao-user\">";
          echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$_SESSION["userimg"]."\" alt=\"\" srcset=\"\">";
          echo "<p class=\"insert-interacao-user-name\">".$_SESSION["username"].":</p>";
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

      // echo "<div class=\"center\">";
      // echo "<div class=\"darkbluebg\">";
      // echo "<div class=\"headerPost\">";
      // echo "<img class=\"interaction-mainuser-user-icon\" src=\"imgs/icons/user-icon.png\" alt=\"\" srcset=\"\">";
      // echo "<div class=\"\">";
      // echo "<p class=\"insert-interacao-user-name\">Nome Junior</p>";
      // echo "<p class=\"insert-interacao-user-assunto\">elon musk, criptos e sexo</p>";
      // echo "</div>";
      // echo "</div>";
      // echo "<a class=\"share\" href=\"\"><img class=\"share\" src=\"imgs/icons/send.png\" alt=\"\" srcset=\"\"></a>";
      // echo "<p class=\"insert-interacao-user-name\">askldjfkasdhfjksahfkjsdhfkjahsd j asdjklfhajklsdhfjklas jksdahfkjsahfkjasd kjsda hsdjkafhsdkajfhsakdjf s kjsahfkjsadhfkjsahdjk sadkj asdjkfhsakjfsakjfasdjk fsadkj sdajkfsjkadfhjkasdhfjkasdhkjsadfksadjkasdf</p>";
      // echo "<div class=\"row2\">";
      // echo "<p class=\"nLikes\">12</p>";
      // echo "<a class=\"\" href=\"\"><img class=\"commentPorto\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\"></a>";
      // echo "</div>";
      // echo "<a class=\"commentPorto\" href=\"\"><img class=\"commentPorto\" src=\"imgs/icons/chat.png\" alt=\"\" srcset=\"\"></a>";
      // echo "</div>";
      // echo "</div>";
    ?>
    </div>
  </main>
</div>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>
</body>
</html>
