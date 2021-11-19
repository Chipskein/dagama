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
      $portoInfo=getPortInfo($_GET['porto']);
      if($portoInfo){
        var_dump($portoInfo);
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
 <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a a-selected\" href=mar.php?user=$_SESSION[userid]>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
<main>
    <div class="container-center">
    <?php
      echo "<div class=\"center\">";
      echo "<div class=\"darkbluebg\">";
      echo "<div class=\"headerPost\">";
      echo "<img class=\"interaction-mainuser-user-icon\" src=\"imgs/icons/user-icon.png\" alt=\"\" srcset=\"\">";
      echo "<div class=\"\">";
      echo "<p class=\"insert-interacao-user-name\">Nome Junior</p>";
      echo "<p class=\"insert-interacao-user-assunto\">elon musk, criptos e sexo</p>";
      echo "</div>";
      echo "</div>";
      echo "<a class=\"share\" href=\"\"><img class=\"share\" src=\"imgs/icons/send.png\" alt=\"\" srcset=\"\"></a>";
      echo "<p class=\"insert-interacao-user-name\">askldjfkasdhfjksahfkjsdhfkjahsd j asdjklfhajklsdhfjklas jksdahfkjsahfkjasd kjsda hsdjkafhsdkajfhsakdjf s kjsahfkjsadhfkjsahdjk sadkj asdjkfhsakjfsakjfasdjk fsadkj sdajkfsjkadfhjkasdhfjkasdhkjsadfksadjkasdf</p>";
      echo "<div class=\"row2\">";
      echo "<p class=\"nLikes\">12</p>";
      echo "<a class=\"\" href=\"\"><img class=\"commentPorto\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\"></a>";
      echo "</div>";
      echo "<a class=\"commentPorto\" href=\"\"><img class=\"commentPorto\" src=\"imgs/icons/chat.png\" alt=\"\" srcset=\"\"></a>";
      echo "</div>";
      echo "</div>";
    ?>
    </div>
</main>
<footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer>
<?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
  ?>
</body>
</html>
