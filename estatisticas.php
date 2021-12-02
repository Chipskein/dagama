<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <title>Dagama | Estatisticas</title>
</head>
<body class="mar_porto">
<main>


<?php
  include './backend/infra/connection.php';
  var_dump($_POST);
  $paises=getPaises();
  $grupos=getGrupos();
  echo "<h1> Gráfico Interações </h1>";
  //$masc = numerosGraficoMasc(0, 18, 'Brasil', $mes);
  $mascres = numerosGraficoMasc(0, 18, 'Brasil', 12);
  $masc = $mascres[0]["total"]; 
  $femres = numerosGraficoFem(0, 18, 'Brasil', 12);
  $fem = $femres[0]["total"];
  //10) Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem, no país P nos últimos D dias
  echo "<h1> Mais curtidas por país </h1>";
  if(!isset($_POST["select-paises-curtidas"])&&!isset($_POST["dia"])&&!isset($_POST["hora"])&&!isset($_POST["curtidas"])){
    echo "<form id=form-paises-curtidas method=POST>";
      echo "<select name=select-paises-curtidas>";
        echo "<option selected >Selecione o pais</option>";
        foreach($paises as $pais){
          echo "<option value=$pais[codigo]>$pais[nome]</option>";
        }
      echo "</select><br>";
      echo "Dia:<input name=dia type=number min=1><br>";
      echo "Hora:<input name=hora type=number min=1><br>";
      echo "Curtidas:<input name=curtidas type=number min=0><br>";
      echo "<input type=button value='Enviar' onclick={document.getElementById(\"form-paises-curtidas\").submit()}>";
    echo "</form>";
  }
  else{
      //validar valores
      $pais=$_POST["select-paises-curtidas"];
      $dia=$_POST["dia"];
      $hora=$_POST["hora"];
      $likes=$_POST["curtidas"];
      $count=countLikesbyCountry($pais,$dia,$hora,$likes);
      if($count){
        echo " $count";
      }  
  }
  //11) Mostrar qual faixa etária mais interagiu às postagens do grupo G nos últimos D dias
  echo "<h1> Faixa Etária por grupo </h1>";
    if(!isset($_POST["select-grupo"])&&!isset($_POST["dias"])){
      echo "<form id=form-grupos method=POST>";
        echo "<select name=select-grupo>";
          echo "<option selected >Selecione o grupo</option>";
          foreach($grupos as $grupo){
            echo "<option value=$grupo[codigo]>$grupo[nome]</option>";
          }
        echo "</select><br>";
        echo "Dias:<input name=dias type=number min=1><br>";
        echo "<input type=button value='Enviar' onclick={document.getElementById(\"form-grupos\").submit()}>";
      echo "</form>";
    }
    else{
        //echo resultado
    }
  //12) Mostrar quais os top T assuntos mais interagidos por mês no país P nos últimos M meses
  echo "<h1> Top assuntos por mês no pais </h1>";
    if(!isset($_POST["select-top-assuntos"])&&!isset($_POST["meses"])&&!isset($_POST["top"])){
      echo "<form id=form-top-assuntos method=POST>";
        echo "<select name=select-top-assuntos>";
          echo "<option selected >Selecione o pais</option>";
          foreach($paises as $pais){
            echo "<option value=$grupo[codigo]>$grupo[nome]</option>";
          }
        echo "</select><br>";
        echo "Meses:<input name=meses type=number min=1><br>";
        echo "Top:<input name=top type=number min=1><br>";
        echo "<input type=button value='Enviar' onclick={document.getElementById(\"form-top-assuntos\").submit()}>";
      echo "</form>";
    }
    else{
        //echo resultado
    }
  //13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P nos últimos M meses
  echo "<h1> Quais assuntos são consectivos </h1>";
    if(!isset($_POST["select-top-assuntos2"])&&!isset($_POST["meses2"])&&!isset($_POST["top2"])){
      echo "<form id=form-top-assuntos2 method=POST>";
        echo "<select name=select-top-assuntos2>";
          echo "<option selected >Selecione o pais</option>";
          foreach($paises as $pais){
            echo "<option value=$pais[codigo]>$pais[nome]</option>";
          }
        echo "</select><br>";
        echo "Meses:<input name=meses2 type=number min=1><br>";
        echo "Top:<input name=top2 type=number min=1><br>";
        echo "<input type=button value='Enviar' onclick={document.getElementById(\"form-top-assuntos2\").submit()}>";
      echo "</form>";
    }
    else{
        //echo resultado
    }
?>
</main>
</body>
</html>