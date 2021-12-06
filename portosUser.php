<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Portos Atracados</title>
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
    $portos = 0;
    if(isset($_GET['owner'])){
      $portos = getUserPorto($_SESSION['userid'], $offset, $limit);
    } else {
      if(isset($_GET['user'])){
        if($_SESSION['userid'] == $_GET['user']){
          $portos = getAllPorto($_SESSION['userid'], true, $offset, $limit, 0);
        } else {
          $portos = getAllPorto($_GET['user'], true, $offset, $limit, 0);          
        }
      }
    }
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
    <form class="header-searchBar" name="search" action="usuarios.php" method="get">
      <select id="select-filtro" name="select-filtro">
        <option value="perfil">Perfil</option>
        <option value="porto">Porto</option>
      </select>
      <input class="header-searchBar-input" name="username" type="text" placeholder="Faça sua pesquisa ..." />
      <button type='submit'><img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset=""></button>

  </form>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main>
    <br>
    <div align=center>
      <?php 
        if(isset($_GET['owner'])){
          echo "<h1>Seus portos</h1>";
        } else {
          echo "<h1>Seus portos e portos atracados</h1>";
        }
      ?>
      <?php
      echo "<div class=\"mar-top-row\">";
        echo "<div class=\"order-btn\">";
        echo "<select id=\"select-ordenar\" name=\"select-ordenar\">";
          echo "<option value=\"nome\">Nome</option>";
          echo "<option value=\"data\">Data de criação</option>";
          echo "<option value=\"qtd\">Qtd interacoes</option>";
        echo "</select>";
        echo "<select id=\"select-ordenar-2\" name=\"select-ordenar-2\">";
          echo "<option value=\"cres\">Cres</option>";
          echo "<option value=\"decre\">Decre</option>";
        echo "</select>";
        echo "<button class=\"insert-interacao-submit\" name=\"ordenarBtn\">Ordenar<button/>";
        echo "<button class=\"btn-create-porto\"><a href=\"createPorto.php\">Criar um porto</a></button>";
        echo "</div>";
      echo "</div>";
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<div class=\"porto-icon\" style=\"background-image: url($porto[img])\"></div>";
            echo "<h2 class=mar_porto><a href=porto.php?porto=$porto[codigo]>$porto[nome]</a></h2>";
            echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
            echo "<form action=\"porto.php?porto=$porto[codigo]\" name=\"porto-form\" method=\"post\" >";
            if(!isset($_GET['owner'])){
              // if($porto['participa']){
              //   echo "<button class=\"insert-interacao-participa\"> <p class=\"insert-interacao-entrar-text\">Participando</p></button>";
              //   echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
              // } else {
              //   echo "<button class=\"insert-interacao-entrar\">Entrar</button>";
              //   echo "<input type=\"hidden\" name=\"entrarPorto\" value=\"entrar\"/>";
              // }
            } else {
              echo "<button class=\"insert-interacao-del\">Deletar</button>";
              echo "<input type=\"hidden\" name=\"excluirPorto\" value=\"deletar\"/>";
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
