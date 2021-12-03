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
  function url($campo, $valor) {
    $result = array();
    if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
    $result[$campo] = $campo."=".$valor;
    return("usuarios.php?".strtr(implode("&", $result), " ", "+"));
  }
?>
 <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <select id="select-filtro" name="select-filtro">
        <option value="nome">Nome</option>
        <option value="data">Data</option>
      </select>
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
    </div>
  </header>
<main>
    <div align=center>
    <div class="container-center">
        <?php
        $limit=5;
        $offset= isset($_GET['offset']) ? $_GET['offset']:0;
        $users=getAllUserInfo($offset,$limit);
        $total=countAllUsers();
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
            echo "</div>";
        echo "</div>";
        echo "<div class=\"div-amigo\">";
        if(count($users)>0){
            foreach ($users as $user) {
            echo "<div class=\"div-amigo-row\">";
                echo "<div class=\"row\">";
                    echo "<img src=\"$user[img]\" alt=\"\" class=\"div-amigo-image\">";
                    echo "<div class=\"div-amigo-textos\">";
                        echo "<p style=\"color: #fff\">$user[username]</p>";
                        $datanasc="$user[datanasc]";
                        echo "<p class=\"\">Registrado em $user[dataregis]</p>";
                        echo "<p class=\"\">Email é $user[email]</p>";
                        echo "<p class=\"\">Nasceu em $datanasc</p>";
                        echo "<p class=\"\">Genero é $user[genero]</p>";
                        echo "<p class=\"\">Mora em $user[cidade]</p>";
                        echo "<p class=\"\">Ativo $user[ativo]</p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            }
        }
        else echo "<p>Sem usuarios</p>";
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
