<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
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
    $limit=2;//mudar pra 10 dps
    $offset= isset($_GET['offset']) ? $_GET['offset']:0;
    //falta o pesquisar e ordenar
    $portos=getAllPorto($offset,$limit);
    $total=getTotalPorto();
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
    <br>
    <div align=center>
      <?php
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<img class=mar_porto src=$porto[img]>";
            echo "<h2 class=mar_porto><a href=porto.php?porto=$porto[codigo]>$porto[nome]</a></h2>";
            echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
            echo "<div class=\"insert-interacao-entrar\"> <p class=\"insert-interacao-entrar-text\">Entrar</p></div>";
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
