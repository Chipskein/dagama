<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title="Mar";
  require 'components/head.php'
?>
<body class="mar_porto">
<?php
  
  /*
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
  */
  require 'components/header.php'

?>
<main>
  <br>
  <div align=center>
  <h1>Portos</h1>
  <?php
    echo "<div class=\"mar-top-row\">";
      echo "<div class=\"order-btn\">";
      echo "<button class=\"btn-create-porto\"><a href=\"/createPorto\">Criar um porto</a></button>";
      echo "</div>";
    echo "</div>";
      foreach($portos as $porto){
        echo "<div class=mar_porto>";
          echo "<div class=\"porto-icon\" style=\"background-image: url($porto[img])\"></div>";
          echo "<h2 class=mar_porto><a href=/porto/$porto[codigo]>$porto[nome]</a></h2>";
          echo "<div class=text-porto> <p class=mar_porto>$porto[descr] </p> </div>";
          if($porto['participa'])
          {
            //echo "<form action=\"/sairPorto/$porto[codigo]\" name=\"porto-form\" method=\"get\" >";
            echo "<a class=\"insert-interacao-participa\" href=\"/sairPorto/$porto[codigo]\"> <p class=\"insert-interacao-entrar-text\">Sair</p></a>";
            //echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
          } 
          else 
          {
            //echo "<form action=\"/entrarPorto/$porto[codigo]\" name=\"porto-form\" method=\"get\" >";
            echo "<a class=\"insert-interacao-entrar\" href=\"/entrarPorto/$porto[codigo]\"><p class=\"insert-interacao-entrar-text\">Entrar</p></a>";
            //echo "<input type=\"hidden\" name=\"entrarPorto\" value=\"entrar\"/>";
          }
          echo "</form>";
        echo "</div>";
      }
    ?>
    <p></p>
  </div>
</main>
<?php
  $route="/mar";
  require "components/FooterPage.php";
?>
</body>
</html>
