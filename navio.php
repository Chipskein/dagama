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
<body>
<?php
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  $user=[];
  if(!isset($_SESSION['userid'])){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  else{
      if(isset($_GET['user'])){
        $user=getUserInfo("$_GET[user]");
        if(!$user){
          echo "Usuario inválido";
          header("refresh:1;url=mar.php");
        }
      }
      else{
        echo "Usuario inválido";
        header("refresh:1;url=mar.php");
      }
  }
?>
<main>
    <div align=center>
    <!--Add onlick change-->
      <div id="img_perfil" class=perfil></div>
      <?php echo "<h3>$user[username]</h3>";?>
    </div>
</main>
<?php 
  echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>
<script>

</script>
</body>
</html>
