<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <title>Dagama | ADM </title>
</head>
<body class="adm">
<main>

<?php
 /*
- 1) CRUD de localidades
            Falta excluir localidades, e validar, paginação
        - 2) CRUD de usuários
            Falta validar, paginação
        - 10) Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem,
        no país P nos últimos D dias
            ok
        - 11) Mostrar qual faixa etária mais interagiu às postagens do grupo G nos últimos D dias
            ok
        - 12) Mostrar quais os top T assuntos mais interagidos por mês no país P nos últimos M meses
            ok
        - 13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P 
        nos últimos M meses
            ok
        - 15) Desativar temporariamente as contas dos usuários do país P que não possuem qualquer interação há mais de A 
        anos
            ok
        - 17) Mostrar o gráfico de colunas da quantidade de interações por gênero por faixa etária no país P nos últimos M meses,
        como no exemplo
            ok
 */
 include './backend/infra/connection.php';
  $paises=getPaises();
  $grupos=getGrupos();
  //utils
  echo "<script>";
    echo "let paises=[];";
    echo "let grupos=[];";
    foreach ($paises as $pais){
      echo "pais={";
        echo"nome:'$pais[nome]',";
        echo"codigo:$pais[codigo]";
      echo "};";
      echo "paises.push(pais);";
    }
    foreach ($grupos as $grupo){
      echo "grupo={";
        echo"nome:'$grupo[nome]',";
        echo"codigo:$grupo[codigo]";
      echo "};";
      echo "grupos.push(grupo);";
    }
  echo "</script>";
  //17
  echo "<div align=center>";
    echo "<h1 align> Gráfico Interações </h1>";
    if(isset($_POST["grafico-paises"])&&isset($_POST["grafico-meses"])){
      if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["grafico-paises"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["grafico-meses"])){
          $lastmonths=$_POST["grafico-meses"];
          $pass=false;
          foreach ($paises as $pais){
            if($pais['codigo']==$_POST["grafico-paises"]){
              $pass=true;
              break;
            };
          }
          if($pass){
            $pais=$_POST["grafico-paises"];
            $tabledata=getInterationDatabyGender($pais,$lastmonths);
            $tabledata2=[];
            foreach($tabledata as $info){ 
              if($info["genero"]=='M'&&$info["faixaEtaria"]=='- 18'){
                $tabledata2[0]=$info;
              }

              if($info["genero"]=='F'&&$info["faixaEtaria"]=='- 18'){
                $tabledata2[1]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='18-21'){
                $tabledata2[2]=$info;
              }

              if($info["genero"]=='F'&&$info["faixaEtaria"]=='18-21'){
                $tabledata2[3]=$info;
              }


              if($info["genero"]=='M'&&$info["faixaEtaria"]=='21-25'){
                $tabledata2[4]=$info;
              }


              if($info["genero"]=='F'&&$info["faixaEtaria"]=='21-25'){
                $tabledata2[5]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='25-30'){
                $tabledata2[6]=$info;
              }

              if($info["genero"]=='F'&&$info["faixaEtaria"]=='25-30'){
                $tabledata2[7]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='30-36'){
                $tabledata2[8]=$info;
              }

              if($info["genero"]=='F'&&$info["faixaEtaria"]=='30-36'){
                $tabledata2[9]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='36-43'){
                $tabledata2[10]=$info;
              }

              if($info["genero"]=='F'&&$info["faixaEtaria"]=='36-43'){
                $tabledata2[11]=$info;
              }
            
              if($info["genero"]=='M'&&$info["faixaEtaria"]=='43-51'){
                $tabledata2[12]=$info;
              }
            
              if($info["genero"]=='F'&&$info["faixaEtaria"]=='43-51'){
                $tabledata2[13]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='51-60'){
                $tabledata2[14]=$info;
              }
            
              if($info["genero"]=='F'&&$info["faixaEtaria"]=='51-60'){
                $tabledata2[15]=$info;
              }

              if($info["genero"]=='M'&&$info["faixaEtaria"]=='60-'){
                $tabledata2[16]=$info;
              }
            
              if($info["genero"]=='F'&&$info["faixaEtaria"]=='60-'){
                $tabledata2[17]=$info;
              }

            }
            echo "<table class=adm align=center>";
              for($c=10;$c>0;$c--){
              echo "<tr>";
                for($c2=0;$c2<18;$c2++){
                  echo $c2%2==0 ? "<td class=\"masc\">":"<td class=\"fem\">"; 
                  /*
                  MF
                  01 -18
                  23 18-21
                  45 21-25
                  67 25-30
                  89 30-36
                  10 11 36-43
                  12 13 43-51
                  14 15 51-60
                  16 17 60 -
                  */
                    if(isset($tabledata2[$c2])){
                    $info=$tabledata2[$c2];
                    switch($c2){
                      case 0:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='- 18'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 1:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='- 18'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 2:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='18-21'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 3:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='18-21'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 4:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='21-25'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 5:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='21-25'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 6:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='25-30'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 7:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='25-30'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 8:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='30-36'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 9:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='30-36'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 10:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='36-43'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 11:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='36-43'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 12:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='43-51'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 13:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='43-51'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 14:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='51-60'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 15:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='51-60'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 16:
                        if($info["genero"]=='M'&&$info["faixaEtaria"]=='60-'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                      case 17:
                        if($info["genero"]=='F'&&$info["faixaEtaria"]=='60-'){
                          echo $info["total"]>=($c*10) ? " * ":" ";
                        }
                        else echo " ";
                        break;
                    };
                  }
                  echo "</td>";
                }
              echo "</tr>";
            }
            echo "<tr>";
              for($c2=0;$c2<18;$c2++){
                echo $c2%2==0 ? "<td class=\"masc\">":"<td class=\"fem\">"; 
                  if(isset($tabledata2[$c2])){
                  $info=$tabledata2[$c2];
                  switch($c2){
                    case 0:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='- 18'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 1:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='- 18'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 2:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='18-21'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 3:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='18-21'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 4:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='21-25'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 5:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='21-25'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 6:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='25-30'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 7:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='25-30'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 8:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='30-36'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 9:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='30-36'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 10:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='36-43'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 11:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='36-43'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 12:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='43-51'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 13:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='43-51'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 14:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='51-60'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 15:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='51-60'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 16:
                      if($info["genero"]=='M'&&$info["faixaEtaria"]=='60-'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                    case 17:
                      if($info["genero"]=='F'&&$info["faixaEtaria"]=='60-'){
                        echo $info["total"];
                      }
                      else echo "0";
                      break;
                  };
                }
                else echo "0";
                echo "</td>";
              }
            echo "</tr>";
            echo "
            <tr>
              <td class=masc>M</td><td class=fem>F</td> 
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
              <td class=masc>M</td><td class=fem>F</td>
            </tr>";
            echo "
            <tr>
              <td colspan=2> -18 </td>
              <td colspan=2> 18-21 </td>
              <td colspan=2> 21-25 </td>
              <td colspan=2> 25-30 </td>
              <td colspan=2> 30-36 </td>
              <td colspan=2> 36-43 </td>
              <td colspan=2> 43-51 </td>
              <td colspan=2> 51-60 </td>
              <td colspan=2> 60- </td>
            </tr>";
            echo "</table>";
          }
          else echo "<h4>País invalido</h4>";
      }
      else{
        echo "<h4>Erro não é um número</h4>";
      }
    }
    else{
      echo "<div align=center>";
      echo "<form  id=grafico-form method=post>";
      echo "Pais:<select class=inputs name=grafico-paises>";
      echo "<option selected >Selecione o pais</option>";
          foreach($paises as $pais){
            echo "<option value=$pais[codigo]>$pais[nome]</option>";
          }
      echo "</select><br>";
      echo "Ultimos Meses:<input type=number min=1 name=grafico-meses class=inputHalf required><br>";
      echo "<input class=button type=button value='Enviar' onclick=verificar17()>";
      echo "</form>";
      echo "</div>";
    }
  echo "</div>";
  //10) Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem, no país P nos últimos D dias
  echo "<div align=center>";
    echo "<h1>Quantos Usuarios Receberam Mais Curtidas</h1>";  
    if(isset($_POST["select-paises-curtidas"])&&isset($_POST["dia"])&&isset($_POST["hora"])&&isset($_POST["curtidas"])){
      //validar
      if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["select-paises-curtidas"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["dia"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["hora"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["curtidas"])){
        $dias=$_POST["dia"];//200
        $hora=$_POST["hora"];//1
        $likes=$_POST["curtidas"];//7
        $pass=false;
        foreach ($paises as $pais){
          if($pais['codigo']==$_POST["select-paises-curtidas"]){
            $pass=true;
            break;
          };
        }
        if($pass){
          $pais=$_POST["select-paises-curtidas"];//31
          $count = countLikesbyCountry($pais,$dias,$hora,$likes);
          $resp = $count ? $count:0;
          echo "<h4>$resp Usuarios Receberam Mais Curtidas de $likes em uma postagem em menos de $hora horas após a postagem no <p id=paisnome></p> nos últimos $dias dias</h4><br>";
          echo "<script>";
          echo "paises.forEach(e=>{
            if(e.codigo==$pais) document.getElementById(\"paisnome\").innerHTML=e.nome;
          });";
          echo "</script>";
        }
        else echo "<h4>Pais invalido</h4>"; 
      }
      else echo "<h4>Erro dados enviados não são números</h4>";
    }
    else{
        echo "<form id=form-paises-curtidas method=POST>";
        echo "Pais<select class=inputs name=select-paises-curtidas>";
          echo "<option selected >Selecione o pais</option>";
          foreach($paises as $pais){
            echo "<option value=$pais[codigo]>$pais[nome]</option>";
          }
        echo "</select><br>";
        echo "Ultimos Dia:<input class=inputHalf name=dia type=number min=1><br>";
        echo "Em menos Hora:<input class=inputHalf name=hora type=number min=1><br>";
        echo "Mais de Curtidas:<input class=inputHalf name=curtidas type=number min=0><br>";
        echo "<input class=button type=button value='Enviar' onclick=verificar10()>";
      echo "</form>";
    }
  echo "</div>";
  //11) Mostrar Qual faixa etária mais interagiu às postagens do grupo G nos últimos D dias
  echo "<div align=center>";
    echo "<h1> Faixa Etária por grupo </h1>";
      if(isset($_POST["select-grupo"])&&isset($_POST["dias"])){
        if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["select-grupo"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["dias"])){
          $dias=$_POST["dias"];
          $pass=false;
          foreach ($grupos as $grupo){
            if($grupo['codigo']==$_POST["select-grupo"]){
              $pass=true;
              break;
            };
          }
          $grupo=$_POST["select-grupo"];
          if($pass){
            $faixa = getFaixaEtaria($grupo,$dias);
            if($faixa){
              echo "<h4>$faixa[faixaEtaria] é a faixa etária que mais interagiu com às postagens do grupo <p id=gruponome></p> nos últimos $dias dias </h4><br>";
              echo "<script>";
              echo "grupos.forEach(e=>{
                if(e.codigo==$grupo) document.getElementById(\"gruponome\").innerHTML=e.nome;
              });";
              echo "</script>";
            }
            else echo "<h4>Sem Resposta</h4><br>";
          }
          else echo "<h4>Grupo Inválido</h4>";
        }
        else echo "<h4>Dados invalidos</h4>";
      }
      else{
          echo "<form id=form-grupos method=POST>";
          echo "<select class=inputs name=select-grupo>";
            echo "<option selected >Selecione o grupo</option>";
            foreach($grupos as $grupo){
              echo "<option value=$grupo[codigo]>$grupo[nome]</option>";
            }
          echo "</select><br>";
          echo "Dias:<input class=inputHalf name=dias type=number min=1><br>";
          echo "<input class=button type=button value='Enviar' onclick=verificar11()>";
        echo "</form>";
      }
  echo "</div>";
  //12) Mostrar quais os top T assuntos mais interagidos por mês no país P nos últimos M meses
  echo "<div align=center>";
    echo "<h1> Top assuntos por mês no pais </h1>";
    if(isset($_POST["select-top-assuntos"])&&isset($_POST["meses"])&&isset($_POST["top"])){
      if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["select-top-assuntos"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["meses"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["top"])){
        $pass=false;
        foreach ($paises as $pais){
          if($pais['codigo']==$_POST["select-top-assuntos"]){
            $pass=true;
            break;
          };
        }
        if($pass){
          $top=$_POST["top"];
          $pais=$_POST["select-top-assuntos"];
          $lastmonths=$_POST["meses"];
          $rows=getTop($pais,$top,$lastmonths);
          echo "<h3>TOP $top</h3>";
          echo "<table class=adm-assuntos>";
            echo "<tr>";
            echo "<td>rank</td>";
            echo "<td>pais</td>";
            echo "<td>mes</td>";
            echo "<td>qt</td>";
            echo "<td>assunto</td>";
            echo "</tr>";
          foreach($rows as $row){
            echo "<tr>";
            echo "<td>$row[rank]</td>";
            echo "<td>$row[pais]</td>";
            echo "<td>$row[mes]</td>";
            echo "<td>$row[qt]</td>";
            echo "<td>$row[assunto]</td>";
            echo "</tr>";
          }
          echo "</table>";
        }
        else echo "<h4>pais invalido</h4>";
      }
      else echo "<h4>Dados invalidos</h4>";
    }
    else{
        echo "<form id=form-top-assuntos method=POST>";
        echo "<select class=inputs name=select-top-assuntos>";
          echo "<option selected >Selecione o pais</option>";
          foreach($paises as $pais){
            echo "<option value=$pais[codigo]>$pais[nome]</option>";
          }
        echo "</select><br>";
        echo "Meses:<input class=inputHalf name=meses type=number min=1><br>";
        echo "Top:<input class=inputHalf name=top type=number min=1><br>";
        echo "<input class=button type=button value='Enviar' onclick=verificar12()>";
        echo "</form>";
    }
  echo "</div>";
  //13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P nos últimos M meses  
  echo "<div align=center>";
  echo "<h1> Quais assuntos são consectivos </h1>";
    if(isset($_POST["select-top-assuntos2"])&&isset($_POST["meses2"])&&isset($_POST["top2"])){
      if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["select-top-assuntos2"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["meses2"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["top2"])){
        $pass=false;
        foreach ($paises as $pais){
          if($pais['codigo']==$_POST["select-top-assuntos2"]){
            $pass=true;
            break;
          };
        }
        if($pass){
          $pais=$_POST["select-top-assuntos2"];
          $top=$_POST["top2"];
          $mes=$_POST["meses2"];
          $rows=getAssuntMoreHyped($pais,$top,$mes);
          echo "<h3>TOP $top</h3>";
          echo "<table class=adm-assuntos>";
            echo "<tr>";
            echo "<td>assunto</td>";
            echo "<td>vezes em q aparece</td>";
            echo "</tr>";
          foreach($rows as $row){
            echo "<tr>";
            echo "<td>$row[assunto]</td>";
            echo "<td>$row[total]</td>";
            echo "</tr>";
          }
          echo "</table>";
        }
        else echo "<h4>Pais Invalido</h4>";
      }
      else echo "<h4>Dados Invalidos</h4>";
    }
    else{
      echo "<form id=form-top-assuntos2 method=POST>";
      echo "<select class=inputs name=select-top-assuntos2>";
        echo "<option selected >Selecione o pais</option>";
        foreach($paises as $pais){
          echo "<option value=$pais[codigo]>$pais[nome]</option>";
        }
      echo "</select><br>";
      echo "Meses:<input class=inputHalf name=meses2 type=number min=1><br>";
      echo "Top:<input class=inputHalf name=top2 type=number min=1><br>";
      echo "<input class=button type=button value='Enviar' onclick=verificar13()>";
    echo "</form>";
    }
    echo "</div>";
  //15 Desativar temporariamente as contas dos usuários do país P que não possuem qualquer interação há mais de A anos
  echo "<div align=center>";
    echo "<h1> Desativar usuarios sem interações </h1>";
    if(isset($_POST['desativar-pais'])&&isset($_POST['desativar-minano'])){
      if(preg_match("#^[1-9]{1}[0-9]*$#",$_POST["desativar-pais"])&&preg_match("#^[1-9]{1}[0-9]*$#",$_POST["desativar-minano"])){
        $pass=false;
        foreach ($paises as $pais){
          if($pais['codigo']==$_POST["desativar-pais"]){
            $pass=true;
            break;
          };
        }
        if($pass){
          $pais=$_POST['desativar-pais'];
          $limityear=$_POST['desativar-minano'];
          $exec=deactivateAllDeadUsersByCountry($pais,$limityear);
          if($exec){
            echo "<h4>Usuarios do <p id=paisnome2></p> desativados com sucesso!</h4><br>";
            echo "<script>";
                  echo "paises.forEach(e=>{
                    if(e.codigo==$pais) document.getElementById(\"paisnome2\").innerHTML=e.nome;
                  });";
            echo "</script>";
          }
          else echo "<h4>FALHOU!</h4><br>";
        }
        else echo "<h4>Pais invalido</h4>";
      }
      else echo "<h4>Dados invalidos</h4>";
    }
    else{
      echo "<form id=form-desativar method=POST>";
            echo "<select class=inputs name=desativar-pais>";
              echo "<option selected >Selecione o pais</option>";
              foreach($paises as $pais){
                echo "<option value=$pais[codigo]>$pais[nome]</option>";
              }
            echo "</select><br>";
            echo "Anos sem interações:<input class=inputHalf name=desativar-minano type=number min=1><br>";
            echo "<input class=button type=button value='Enviar' onclick=verificar15()>";
          echo "</form>";
    }
  echo "<div>";
//16 Atribuir automaticamente um selo de fã, com validade determinada para a semana atual, para os usuários do grupo G conforme a tabela
  echo "<br><br>";
?>
</main>
<script>
function verificar10(){
  let form=document.getElementById("form-paises-curtidas");
  let select=document.getElementsByName("select-paises-curtidas")[0];
  let input1=document.getElementsByName("dia")[0];
  let input2=document.getElementsByName("hora")[0];
  let input3=document.getElementsByName("curtidas")[0];
  let regexp1 = new RegExp("^[1-9]{1}[0-9]*$");
    if(!regexp1.test(input1.value)||!regexp1.test(input2.value)||!regexp1.test(input3.value)) {
      if(!regexp1.test(input1.value)){
        input1.value = "";
        input1.focus();
        return;
      }
      if(!regexp1.test(input2.value)){
        input2.value = "";
        input2.focus();
        return;
      }
      if(!regexp1.test(input3.value)){
        input3.value = "";
        input3.focus();
        return;
      }
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
function verificar17(){
  let form=document.getElementById("grafico-form");
  let select=document.getElementsByName("grafico-paises")[0];
  let input=document.getElementsByName("grafico-meses")[0];
  let regexp = new RegExp("^[1-9]{1}[0-9]*$");
    if (!regexp.test(input.value)) {
        input.value = "";
        input.focus();
        return;
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
function verificar11(){
  let form=document.getElementById("form-grupos");
  let select=document.getElementsByName("select-grupo")[0];
  let input=document.getElementsByName("dias")[0];
  let regexp = new RegExp("^[1-9]{1}[0-9]*$");
    if (!regexp.test(input.value)) {
        input.value = "";
        input.focus();
        return;
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
function verificar12(){
  let form=document.getElementById("form-top-assuntos");
  let select=document.getElementsByName("select-top-assuntos")[0];
  let input1=document.getElementsByName("meses")[0];
  let input2=document.getElementsByName("top")[0];
  let regexp1 = new RegExp("^[1-9]{1}[0-9]*$");
    if(!regexp1.test(input1.value)||!regexp1.test(input2.value)) {
      if(!regexp1.test(input1.value)){
        input1.value = "";
        input1.focus();
        return;
      }
      if(!regexp1.test(input2.value)){
        input2.value = "";
        input2.focus();
        return;
      }
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
function verificar13(){
  let form=document.getElementById("form-top-assuntos2");
  let select=document.getElementsByName("select-top-assuntos2")[0];
  let input1=document.getElementsByName("meses2")[0];
  let input2=document.getElementsByName("top2")[0];
  let regexp1 = new RegExp("^[1-9]{1}[0-9]*$");
    if(!regexp1.test(input1.value)||!regexp1.test(input2.value)) {
      if(!regexp1.test(input1.value)){
        input1.value = "";
        input1.focus();
        return;
      }
      if(!regexp1.test(input2.value)){
        input2.value = "";
        input2.focus();
        return;
      }
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
function verificar15(){
  let form=document.getElementById("form-desativar");
  let select=document.getElementsByName("desativar-pais")[0];
  let input1=document.getElementsByName("desativar-minano")[0];
  let regexp1 = new RegExp("^[1-9]{1}[0-9]*$");
    if(!regexp1.test(input1.value)){
      
        input1.value = "";
        input1.focus();
        return;
    }
    else{
      if(select.selectedIndex!=0){
        form.submit()
      }
    }
}
</script>
</body>
</html>