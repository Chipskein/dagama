<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title="Portos";
  require 'components/head.php';
?>
<body class="mar_porto">
  <?php
    require 'components/header.php';
  ?>
  <main>
    <br>
    <div align=center>
      <?php 
        if(isset($_GET['owner'])){
          echo "<h1>Meus portos</h1>";
        } else {
          echo "<h1>Meus portos e portos atracados</h1>";
        }
      ?>
      <?php
      echo "<div class=\"mar-top-row\">";
        echo "<button class=\"btn-create-porto\"><a href=\"/createPorto\">Criar um porto</a></button>";
      echo "</div>";
      if($portos){
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<div class=\"porto-icon\" style=\"background-image: url($porto[img])\"></div>";
            echo "<h2 class=mar_porto><a href=/porto/$porto[codigo]>$porto[nome]</a></h2>";
            echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
            echo "<form action=\"/delporto\" name=\"porto-form\" method=\"post\" >";
            if($IsOwner){
              echo "<button class=\"insert-interacao-del\">Deletar</button>";
              echo "<input type=\"hidden\" name=\"excluirPorto\" value=\"deletar\"/>";
            }
            echo "</form>";
          echo "</div>";
        }
      }
      $route="/portosUser/$id";
      require 'components/FooterPage.php';
     
      ?>
      <p></p>
    </div>
  </main>
</body>
</html>
