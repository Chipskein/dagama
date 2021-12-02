<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Mar</title>
</head>
<body class="mar_porto">
<?php
  function url($campo, $valor) {
    $result = array();
    if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
    $result[$campo] = $campo."=".$valor;
    return("mar.php?".strtr(implode("&", $result), " ", "+"));
  }
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    $limit=10;//mudar pra 10 dps
    $offset= isset($_GET['offset']) ? $_GET['offset']:0;
    //falta o pesquisar e ordenar
    $portos=getAllPorto($_SESSION['userid'], false, $offset, $limit);
    $total=getTotalPorto();
    
    if(isset($_POST['entrarPorto'])){
      $response = entrarPorto($_SESSION['userid'], $_POST['entrarPorto']);
      if(!$response){
        echo "Erro ao entrar no porto";
      } else {
        header("refresh:0;url=porto.php?porto=$_POST[entrarPorto]"); 
      }
    }
    if(isset($_POST['sairPorto'])){
      $response = sairPorto($_SESSION['userid'], $_POST['entrarPorto']);
      if(!$response){
        echo "Erro ao sair do porto";
      } else {
        header("refresh:0;url=porto.php?porto=$_POST[entrarPorto]");
      }
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
      echo "<a class=\"header-links-a a-selected\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main>
    <br>
    <div align=center>
    <h1>Mar de portos</h1>
      <div class="mar-top-row">
        <div class="order-btn"><img src="./imgs/icons/hamburger.png" class="order-btn-icon" alt="" srcset=""><p class="order-btn-txt">Ordenar</p></div>
        <button class="btn-create-porto"><a href="createPorto.php">Criar um porto</a></button>
      </div>
      <?php
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<div class=\"porto-icon\" style=\"background-image: url($porto[img])\"></div>";
            echo "<h2 class=mar_porto><a href=porto.php?porto=$porto[codigo]>$porto[nome]</a></h2>";
            echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
            echo "<form action=\"porto.php?porto=$porto[codigo]\" name=\"porto-form\" method=\"post\" >";
            if($porto['participa']){
              echo "<button class=\"insert-interacao-participa\"> <p class=\"insert-interacao-entrar-text\">Participando</p></button>";
              echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
            } else {
              echo "<button class=\"insert-interacao-entrar\">Entrar</button>";
              echo "<input type=\"hidden\" name=\"entrarPorto\" value=\"entrar\"/>";
            }
            echo "</form>";
          echo "</div>";
        }
      ?>
      <p></p>
    </div>
  </main>
  <footer>
    <div align=center>
        <h3>
          <<  
          <?php
            //provisório
            for ($page = 0; $page < ceil($total/$limit); $page++) {
              echo (($offset == $page*$limit) ? ($page+1) : "<a class=page-link href=\"".url("offset", $page*$limit)."\">".($page+1)."</a>")." \n";
            }
          ?>
          >>
       </h3>  
    </div>
  </footer>
</body>
</html>
