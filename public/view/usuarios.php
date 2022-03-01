<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title="Pesquisa";
  require 'components/head.php';
?>
<body>
<main>
    <div align=center>
    <?php require 'components/header.php'?>
      <div class="container-center">
          <?php
            echo "<div class=\"div-amigo\">";
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
              else echo "<p>Sem portos com esse nome</p>";
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
              else echo "<p>Sem usuarios com esse nome</p>";
            echo "</div>";
          ?>
      </div>
    </div>
</main>
</body>
</html>
