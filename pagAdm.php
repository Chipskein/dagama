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
      $results=[];
          
      $count = countLikesbyCountry($pais,$dia,$hora,$likes);
      echo '<p>Resposta:</p><br>';
      print_r($count);
      
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
        $grupo=$_POST["select-grupo"];
        $dias=$_POST["dias"];

            
        $faixa = getFaixaEtaria($grupo,$dia);
        echo '<p>Resposta:</p><br>';
        echo $faixa;
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
//15 Desativar temporariamente as contas dos usuários do país P que não possuem qualquer interação há mais de A anos


/*
 function deactivateUser($user){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set ativo='0' where codigo=$user");
                return true;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db,"update perfil set ativo=false where codigo=$user");
                return pg_fetch_array($response);
            } 
        }
        else exit;
    }
*/
//16 Atribuir automaticamente um selo de fã, com validade determinada para a semana atual, para os usuários do grupo G conforme a tabela
   

//17 Mostrar o gráfico de colunas da quantidade de interações

var_dump($_POST);
$paises=getPaises();
$grupos=getGrupos();
echo "<h1> Gráfico Interações </h1>";
echo "<h2> -18 </h2>";
$mascres = numerosGraficoMasc(0, 18, 'Brasil', $mes);
$masc = $mascres[0]["total"]; 
$femres = numerosGraficoFem(0, 18, 'Brasil', $mes);
$fem = $femres[0]["total"];


echo "<h2> -18 </h2>";
$mascres2 = numerosGraficoMasc(0, 18, 'Brasil', $mes);
$masc2 = $mascres2[0]["total"]; 
$femres2 = numerosGraficoFem(0, 18, 'Brasil', $mes);
$fem2 = $femres2[0]["total"];

echo "<table>";
echo "<tr><th>- </th> <th>18 </th> </tr>";

echo "</table>";
?>
</main>
</body>
</html>