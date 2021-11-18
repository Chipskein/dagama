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
  $user=[];
  if(!isset($_SESSION['userid'])){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  else{
      if(isset($_GET['user'])){
        $user=getUserInfo("$_GET[user]");
        if(!$user){
          echo "Usuario inválido";
          header("refresh:1;url=mar.php");
          die();
        }
        $isOwner= "$_GET[user]"=="$_SESSION[userid]" ? true:false;
      }
      else{
        echo "Usuario inválido";
        header("refresh:1;url=mar.php");
        die();
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
      echo "<a class=\"header-links-a\" href=feed.php?user=$_SESSION[userid]>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php?user=$_SESSION[userid]>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
<main>
    <div align=center>
    <!--Add onlick change-->
      <br>
      <div id="img_perfil" class=perfil></div>
      <?php 
        if($isOwner)echo "<div class=camera-icon></div>";
      ?>
      <?php echo "<h3 class=perfil>$user[username]</h3>";?>
    </div>
    <br>
    <div align=center>
        <div class=perfil-amigos>
          <h3>Amigos:0</h3>
          <?php
            if($isOwner) echo "<h3>Seus Portos:0</h3>";
          ?>
          <h3>Portos:0</h3>
        </div>
    </div>
    <br>
    <div align=center>
        <div class=make-post>
          <?php
            echo "<div class=post-icon style=background-image:url($_SESSION[userimg]);></div>";
            echo "<h3 class=post-name>$_SESSION[username]:</h3>";
          ?>
          <div>
              <textarea class=post-input name="post-input" placeholder="Escreva um post"></textarea>
          </div>
        </div>
    </div>
</main>
<footer><h3 align=center><< 1 2 3 >></h3></footer>
<?php 
  echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>

</body>
</html>
