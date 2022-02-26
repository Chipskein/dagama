<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <title>Dagama | Navio</title>
</head>
<body class=perfil>
<?php
function url($campo, $valor) {
  $result = array();
  if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
  $result[$campo] = $campo."=".$valor;
  return("amigos.php?".strtr(implode("&", $result), " ", "+"));
}
function pages($campo, $valor){
    $result = array();
    if (isset($_GET["page"])) $result["page"] = "page=".$_GET["page"];
    $result[$campo] = $campo."=".$valor;
    return '&'.(strtr(implode("&",$result), " ", "+"));
}
  include '../backend/infra/services.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  $amigos=getRequestAndFriends($_SESSION["userid"],false);
  $offset=0;
  $total = 5;
  $limit = 5;
  // var_dump($amigos);
  if(isset($_POST['insert-interacao-accept'])){
    $response = confirmFriendRequest($_SESSION["userid"], (int)$_POST['insert-interacao-accept']);
    if($response) header("refresh:1;url=solicitacoes.php");
    else echo "Erro ao aceitar solicitação amizade...";
  }
  if(isset($_POST['insert-interacao-decline'])){
    $response = declineFriendRequest($_SESSION["userid"],$_POST["insert-interacao-decline"]);
    if($response) header("refresh:1;url=solicitacoes.php");
    else echo "Erro ao rejeitar solicitação amizade...";
  }
  // if(!$amigos){
  //     echo "<h2 align=center>Usuario Inválido</h2>";
  //     header('refresh:1;url=mar.php');
  // }

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
      echo "<a class=\"header-links-a\" href=feed.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
<main>
    <div align=center>
    <!--Add onlick change-->
    <div class="container-center">
    <?php
      // if(count($amigos)>0){
        foreach($amigos as $amigo){
          echo "<div class=\"margin-top\">";
          echo "<div class=\"div-amigo\">";
          echo "<div class=\"div-amigo-row\">";
          echo "<div class=\"row\">";
            echo "<img src=\"$amigo[img]\" alt=\"\" class=\"div-amigo-image\">";
            echo "<div>";
              echo "<p class=\"nome-amigo-solicitacao\">$amigo[nome]</p>";
              echo "<p class=\"data-amigo-solicitacao\">Data de envio: $amigo[data]</p>";
              echo "</div>";
              echo "</div>";
            echo "<form name=\"addAmigo\" action=\"solicitacoes.php\" method=\"post\" >";
              echo "<button class=\"add-amigo-card-button-solicitacao\">Aceitar</button>";
              echo "<input class=\"hidden\" type=\"hidden\" name=\"insert-interacao-accept\" value=".$amigo['amigocod']." />";
            echo "</form>";
            echo "<form name=\"DelAmigo\" action=\"solicitacoes.php\" method=\"post\" >";
            echo "<button class=\"remove-amigo-card-button-solicitacao\">Rejeitar</button>";
            echo "<input class=\"hidden\" type=\"hidden\" name=\"insert-interacao-decline\" value=".$amigo['amigocod']." />";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        };
      // }
      // else echo "<p>Você Não tem amigos</p>";
    ?>
    </div>
</main>
<?php 
echo "<footer style=\"padding-top:20px; padding-bottom:20px\" align=center>";
$links = 4;
$page = isset($_GET["page"]) ? strtr($_GET["page"], " ", "%") : 0;
echo "<div style=\"row\">";
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

</body>
</html>
