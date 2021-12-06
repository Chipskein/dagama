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
    if (isset($_GET["orderby"])) $result["orderby"] = "orderby=".$_GET["orderby"];
    $result[$campo] = $campo."=".$valor;
    return("mar.php?".strtr(implode("&", $result), " ", "+"));
  }
function pages($campo, $valor){
    $result = array();
    if (isset($_GET["page"])) $result["page"] = "page=".$_GET["page"];
    $result[$campo] = $campo."=".$valor;
    return '&'.(strtr(implode("&",$result), " ", "+"));
}
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    $limit=2;//mudar pra 10 dps
    $offset= isset($_GET['offset']) ? $_GET['offset']:0;
    //falta o pesquisar e ordenar
    $orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "data desc";
    $portos=getAllPorto($_SESSION['userid'], false, $offset, $limit, $orderby);
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
    <?php
            echo "<form action=\"navio.php?user=$_SESSION[userid]\" id=\"formOrderby\" method=\"get\">";
            echo "<div class=\"order-btn\">";
            echo "<p>Ordene por </p>";
            echo "<select onchange=\"document.getElementById('formOrderby').submit();\" id=\"select-ordenar\" name=\"orderby\">";
            echo "<option value=\"data desc\" ".($_GET['orderby'] == "data desc" ? "selected" : "").">Data descrecente</option>";
              echo "<option value=\"data asc\" ".($_GET['orderby'] == "data asc" ? "selected" : "").">Data crescente</option>";
              echo "<option value=\"tmpQtd.qtd desc\" ".($_GET['orderby'] == "tmpQtd.qtd desc" ? "selected" : "").">Nome descrescente</option>";
              echo "<option value=\"tmpQtd.qtd asc\" ".($_GET['orderby'] == "tmpQtd.qtd asc" ? "selected" : "").">Nome crescente</option>";
            echo "</select>";
            echo "</div>";
            echo "</form>";
      echo "<div class=\"mar-top-row\">";
        echo "<div class=\"order-btn\">";
        echo "<button class=\"btn-create-porto\"><a href=\"createPorto.php\">Criar um porto</a></button>";
        echo "</div>";
      echo "</div>";
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<div class=\"porto-icon\" style=\"background-image: url($porto[img])\"></div>";
            echo "<h2 class=mar_porto><a href=porto.php?porto=$porto[codigo]>$porto[nome]</a></h2>";
            echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
            echo "<form action=\"porto.php?porto=$porto[codigo]\" name=\"porto-form\" method=\"post\" >";
            if($porto['participa']){
              echo "<button class=\"insert-interacao-participa\"> <p class=\"insert-interacao-entrar-text\">Participando</p></button>";
              // echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
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
          <?php
      echo "<footer style=\"padding-top:20px; padding-bottom:20px\" align=center>";
      $links = 4;
      $page = isset($_GET["page"]) ? strtr($_GET["page"], " ", "%") : 0;
      echo "<div class=\"row\">";
      echo "<a class=\"paginacaoNumber\" href=\"".url("offset",0*$limit).pages("page", 1)."\">primeira </a>";
      for($pag_inf = $page - $links ;$pag_inf <= $page - 1;$pag_inf++){
          if($pag_inf >= 1 ){
              echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_inf-1)*$limit).pages("page", $pag_inf)."\"> ".($pag_inf)." </a>";
          }
      };
      if($page != 0 ){
          echo "<a class=\"paginacaoNumber\" style=color:yellow;>$page</a>";
      };
      for($pag_sub = $page+1;$pag_sub <= $page + $links;$pag_sub++){
          if($pag_sub <= ceil($total/$limit)){
              echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_sub-1)*$limit).pages("page", $pag_sub)."\"> ".($pag_sub)." </a>";
          }
      }
      echo "<a class=\"paginacaoNumber\" href=\"".url("offset",ceil($total/$limit)*$limit/$limit).pages("page", ceil($total/$limit))."\"> ultima</a>";
      echo "</div>";
      echo "</footer>";
          ?>
  <script src="./teste.js" type="module">
    </script>
</body>
</html>
