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
<main>


<?php
  include './backend/infra/connection.php';
  echo "<h1> -18 </h1>";
  //$masc = numerosGraficoMasc(0, 18, 'Brasil', $mes);
  $mascres = numerosGraficoMasc(0, 18, 'Brasil', 12);
  $masc = $mascres[0]["total"];
  
  $femres = numerosGraficoFem(0, 18, 'Brasil', 12);
  $fem = $femres[0]["total"];


?>

</main>
<footer>

  </footer>
</body>
</html>