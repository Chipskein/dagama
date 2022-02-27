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
  include '../backend/infra/services.php';
  session_start();
  function url($campo, $valor) {
    $result = array();
    if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
    $result[$campo] = $campo."=".$valor;
    return("usuarios.php?".strtr(implode("&", $result), " ", "+"));
  }
  $campo = $_GET['select-filtro'];
  $limit=5;
  $offset= isset($_GET['offset']) ? $_GET['offset']:0;
  $order = null;

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
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
<main>
    <div align=center>
    <div class="container-center">
        <?php
        $portos = getAllPortos($offset, $limit, $order, $_GET['username']);
        $users=getAllUserInfo($offset,$limit,$_GET['username']);
        $total=countAllUsers();

        echo "<div class=\"div-amigo\">";
        if($campo == 'porto'){
          if(count($portos)>0){
              foreach ($portos as $porto) {
              echo "<div class=\"div-amigo-row\">";
                  echo "<div class=\"row\">";
                      echo "<a href=porto.php?porto=$porto[codigo]><img src=\"$porto[img]\" alt=\"\" class=\"div-amigo-image\"></a>";
                      echo "<div class=\"div-amigo-textos\">";
                          echo "<p style=\"color: #fff\">$porto[nome]</p>";
                          echo "<p class=\"\">Registrado em $porto[dataRegis]</p>";
                          // echo "<p class=\"\">criado por $porto[perfil]</p>";
                          echo "<p class=\"\">Ativo $porto[ativo]</p>";
                      echo "</div>";
                  echo "</div>";
              echo "</div>";
              }
          }
          else echo "<p>Sem usuarios</p>";
        }
        if($campo == 'perfil'){
          if(count($users)>0){
              foreach ($users as $user) {
              echo "<div class=\"div-amigo-row\">";
                  echo "<div class=\"row\">";
                      echo "<a href=navio.php?user=$user[codigo]><img src=\"$user[img]\" alt=\"\" class=\"div-amigo-image\"></a>";
                      echo "<div class=\"div-amigo-textos\">";
                          echo "<p style=\"color: #fff\">$user[username]</p>";
                          $datanasc="$user[datanasc]";
                          echo "<p class=\"\">Registrado em $user[dataregis]</p>";
                          echo "<p class=\"\">Email é $user[email]</p>";
                          echo "<p class=\"\">Nasceu em $datanasc</p>";
                          echo "<p class=\"\">Genero é $user[genero]</p>";
                          echo "<p class=\"\">Mora em $user[pais]</p>";
                          echo "<p class=\"\">Ativo $user[ativo]</p>";
                      echo "</div>";
                  echo "</div>";
              echo "</div>";
              }
          }
          else echo "<p>Sem Portos com esse nome</p>";
        }
        echo "</div>";
        ?>
    </div>
</main>
<footer class="container-bottom" >
    <?php 
    echo "<p align=center>";
        for ($page = 0; $page < ceil($total/$limit); $page++) {
            echo (($offset == $page*$limit) ? ($page+1) : "<a class=page-link href=\"".url("offset", $page*$limit)."\">".($page+1)."</a>")." \n";
        }
    echo "</p>";
    ?>
</footer>
<!-- <?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?> -->

</body>
</html>
