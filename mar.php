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
  include './backend/infra/connection.php';
  session_start();
  if($_SESSION['userid']){
    $portos=getAllPorto(0);
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
  <header class=header>
  <?php
    echo "<a href=backend/logoff.php><img class=ancora src=imgs/icons/ancora.png alt=logoff></a>";
  ?>  
  <img class=logo src="imgs/icon.png" alt="logo">
  <label><a class=pesquisar><img class=pesquisar src="imgs/icons/search.png" alt="pesquisar"></a><input type="text" placeholder="Faça sua pesquisa"></label>
    <?php echo "<a class=header href=feed.php?user=$_SESSION[userid]>Feed</a> ";?>
    <a class="onIt header">Mar</a>
    <?php echo "<a class=header href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";?>
  </header> 
  <main>
    <br>
    <div align=center>
      <?php
        foreach($portos as $porto){
          echo "<div class=mar_porto>";
            echo "<img class=mar_porto src=$porto[img]>";
            echo "<h2 class=mar_porto><a href=porto.php?porto=$porto[codigo]>$porto[nome]</a></h2>";
            echo "<p class=mar_porto>$porto[descr]</p>";
          echo "</div>";
        }
      ?>
    </div>
  </main>
  <footer>
    <div align=center>
        <h3><< 1 2 3 >></h3>
    </div>
  </footer>
</body>
</html>
