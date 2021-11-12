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
  session_start();
  $user=[];
  if(!$_SESSION['userid']){
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  else{
      $user=getUserInfo($_SESSION['userid']);
      echo "<script>";
      //colocar as variaveis do php aqui;
      echo "let val='$user[img]'";
      echo "</script>";
  }
?>
<main>
            <div id="imgs_register">
                <div id="img_input1">
                    <div id="img_perfil"></div>
                </div>
            </div>
</main>
<script>
    img_perfil.style.backgroundImage=`url(${val})`;
    imgInp.onchange = evt => {
      const [file] = imgInp.files
      const img_perfil=document.getElementById("img_perfil");
      if (file) {
          img_perfil.style.backgroundImage=`url(${URL.createObjectURL(file)})`;
      }
      else{
          img_perfil.style.backgroundImage=`url(${val})`
      }
    }
</script>
</body>
</html>
