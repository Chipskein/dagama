<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Porto</title>
</head>
<body>
<?php
  include '../backend/infra/services.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    $user = getUserInfo("$_SESSION[userid]");
    //validar porto
    if(isset($_GET['porto'])){
      $postsArray = [];//getPostsOnPorto($_GET['porto'], 0, 10);
      $participantesPorto = getPortoParticipants($_GET['porto'], 0, 5);
      $allParticipantesPorto = getAllPortoParticipants($_GET['porto']);
      $dateTest = getDate()['wday'];
      date_default_timezone_set('America/Sao_Paulo');
      if(date('l') == "Sunday" && date('h:i:s') == '00:00:00'){
        foreach ($allParticipantesPorto as $participante) {
          upsertSelo($participante['codPart'], $_GET['porto']);
        }
      }
      $locaisArray = [];
      $assuntosArray = getAssuntos();
      $pessoasArray = getPessoas();
      $paises=getPaises();
      $estados=[];
      $cidades=[];
      // $selo = 0;
      if(isset($_POST['entrarPorto'])){
        $response = entrarPorto($_SESSION['userid'], $_GET['porto']);
        if(!$response){
          echo "Erro ao entrar no porto ".$response;
        } else {
          header("refresh:0;url=porto.php?porto=$_GET[porto]"); 
        }
      }
      if(isset($_POST['sairPorto'])){
        $response = sairPorto($_SESSION['userid'], $_GET['porto']);
        if(!$response){
          echo "Erro ao sair do porto";
        } else {
          header("refresh:0;url=porto.php?porto=$_GET[porto]");
        }
      }
      if(isset($_POST['excluirPorto'])){
        $response =  delPorto($_GET['porto']);
        if(!$response){
          echo "Erro ao sair do porto";
        } else {
          header("refresh:0;url=mar.php");
        }
      }

      $portoInfo = getPortInfo($_GET['porto'], $_SESSION['userid']);
      if($portoInfo){
        // var_dump($portoInfo);
      }
      else{
        if(isset($_POST['excluirPorto'])){
          echo "<h2 align=center>Porto Excluído com Sucesso</h2>";
        } else {
          echo "<h2 align=center>Porto Inválido</h2>";
        }
        header("refresh:1;url=mar.php");
        die();
      }

      if(isset($_POST['novoPost'])){
        // var_dump($_POST);
        $texto = ''.$_POST['texto'];
        $reacao = isset($_POST['reacao']) ? $_POST['reacao'] : 0;
        $isReaction = isset($_POST['reacao']) ? 1 : 0;
        $assuntos = [];
        $citacoes = [];
        $porto = $_GET['porto'];

        // Local
        $local = $user['cidade'];
        $codPais = $_POST['insert-codigo-pais'];
        $novoPaisNome = $_POST['insert-nome-pais'];
        $codEstado = $_POST['insert-codigo-estado'];
        $novoEstadoNome = $_POST['insert-nome-estado'];
        $codCidade = $_POST['insert-codigo-cidade'];
        $novoCidadeNome = $_POST['insert-nome-cidade'];

        if(isset($codPais) && isset($codEstado) && isset($codCidade)){
          if($codPais != "" && $codEstado != "" && $codCidade != ""){
            if($codPais == 0){
              // cria novo pais, estado e cidade
              $pais = [];//addPais($novoPaisNome);
              $estado = [];//addEstado($novoEstadoNome, $pais);
              $local = [];//addCidade($novoCidadeNome, $estado);
            }
            if($codPais != 0 && preg_match('#^[0-9]{1,}$#', $codPais)){  
              if($codEstado == 0){
                // cria novo estado e cidade
                $estado = [];//addEstado($novoEstadoNome, $codPais);
                $local = [];//addCidade($novoCidadeNome, $estado);
              }
            if($codEstado != 0 && preg_match('#^[0-9]{1,}$#', $codEstado)){
              if($codCidade == 0){
                  // cria nova cidade
                  $local = [];//addCidade($novoCidadeNome, $codEstado);
                }
                if($codCidade != 0 && preg_match('#^[0-9]{1,}$#', $codCidade)){
                  $local = $codCidade;
                }
              }
            }
          }
        }
        $newAssuntos = [];
        for($c = 1; $c <= 5 ; $c++){
          if(isset($_POST['insert-new-assunto'.$c])){
            $newAssuntos[] = $_POST['insert-new-assunto'.$c];
          }
        }
        if(count($newAssuntos) > 0){
          foreach ($newAssuntos as $value) {
            $assuntos[] = addAssunto($value);
          }
        }

        $qtdAssuntos = count(getAssuntos());
        for($c = 1; $c <= $qtdAssuntos; $c++){
            if(isset($_POST["assunto$c"])){
                $assuntos[] = $_POST["assunto$c"];
            }
        }
        $qtdPessoas = count(getPessoas());
        for($c = 1; $c <= $qtdPessoas; $c++){
            if(isset($_POST["pessoa$c"])){
                $citacoes[] = $_POST["pessoa$c"];
            }
        }
        $response = addInteracao($_SESSION['userid'], $texto, 0, $porto, 0, 0, 0, $isReaction, $reacao, $local);
        if($response) {
          if(count($assuntos) > 0){
            foreach ($assuntos as $value) {
              addAssuntoInteracao($response, $value);
            }
          }
          if(count($citacoes) > 0){
            foreach ($citacoes as $value) {
              addCitacaoInteracao($value, $response);
            }
          }
          header("refresh:0;url=porto.php?porto=$_GET[porto]"); 
        }
        else return false;
      }
      if(isset($_POST['deletePost'])){
        $post = $_POST['deletePost'];
        $user = $_SESSION['userid'];
        $erros = [];
        // Validação
        // ...
        if($erros == []){
          delInteracao($post);
          header("refresh:0;url=porto.php?porto=$_GET[porto]"); 
        }
      }
      if(isset($_POST['removeCitacao'])){
        $post = $_POST['removeCitacao'];
        $user = $_SESSION['userid'];
        $erros = [];
        // Validação
        // ...
        if($erros == []){
          delCitacao($post, $user);
          header("refresh:0;url=porto.php?porto=$_GET[porto]"); 
        }
      }
    }
    else {
      echo "<h2 align=center>Porto Inválido</h2>";
      header("refresh:1;url=mar.php");
      die();
    }
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
<div id=principal> 
  <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <form class="header-searchBar" name="search" action="usuarios.php" method="get">
      <select id="select-filtro" name="select-filtro">
        <option value="perfil">Perfil</option>
        <option value="porto">Porto</option>
      </select>
      <input class="header-searchBar-input" name="username" type="text" placeholder="Faça sua pesquisa ..." />
      <button type='submit'><img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset=""></button>

  </form>
    <div class="header-links">
      <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
      ?>
    </div>
  </header>
  <aside id=direita align=center>
  <div class="container-aside-porto" >
    <?php 
      echo "<div class=\"porto-img\" style=\"background-image: url($portoInfo[img])\"></div>";
      echo "<p class=portoTitle>$portoInfo[nome]</p>";
      echo "<div class=\"portoDesc\"><p>$portoInfo[descr]</p></div>";
      echo "<form action=\"porto.php?porto=$portoInfo[codigo]\" name=\"porto-form\" method=\"post\" >";
      if($portoInfo['participa'] && !$portoInfo['owner']){
        echo "<button class=\"porto-sair-btn\"><p class=\"porto-entrar-btn-txt\">Sair</p></button>";
        echo "<input type=\"hidden\" name=\"sairPorto\" value=\"sair\"/>";
      }
      if(!$portoInfo['participa'] && !$portoInfo['owner']){
        echo "<button class=\"porto-entrar-btn\">Entrar</button>";
        echo "<input type=\"hidden\" name=\"entrarPorto\" value=\"entrar\"/>";
      }
      echo "</form>";
      if($portoInfo['owner']){
        echo "<form id=formEditar action=editarPorto.php method=POST >";
        echo "<button class=\"porto-sair-btn\"> <p class=\"porto-entrar-btn-txt\">Editar porto</p></button>";
        echo "<input type=\"hidden\" name=\"porto\" value=\"$portoInfo[codigo]\"/>";
        echo "<input type=\"hidden\" name=\"oldimg\" value=\"$portoInfo[img]\"/>";
        echo "<input type=\"hidden\" name=\"oldnome\" value=\"$portoInfo[nome]\"/>";
        echo "<input type=\"hidden\" name=\"olddescr\" value=\"$portoInfo[descr]\"/>";
        echo "<input type=\"hidden\" name=\"owner\" value=\"$portoInfo[codAdm]\"/>";
        echo "</form>";
      }
    ?>
  </div>
      
  </aside>
  <aside id=esquerda>
      <div align=center class="aside-porto">
        <div id="ademirTxt">
          <p class=portoAsidePartText>Ademir: </p>
        </div>
        <?php
          echo "<a href=navio.php?user=".$portoInfo['codAdm']."><div><img src=\"".$portoInfo['imgAdm']."\" class=div-amigo-image><p class=nomeAmigo>👑 ".$portoInfo['nomeAdm']."</p></div></a>";
        ?>
        <div>
          <p class=portoAsidePartText>Participantes: </p>
        </div>
        <?php
          if(count($participantesPorto) > 0){
            foreach ($participantesPorto as $part) {
              echo "<a href=navio.php?user=$part[codPart]><div><img src=$part[imgPart] class=div-amigo-image><p class=nomeAmigo>";
              if($part['codSelo'] == 3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
              if($part['codSelo'] == 2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
              if($part['codSelo'] == 1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
              echo $part['nomePart']."</p></div></a>";
            }
            echo "<a class=portosAtracadosMais href=participantesPorto.php?porto=".$_GET['porto'].">Ver mais</a>";
          } else {
            echo "<p style=\"font-size: 14px\" >Este porto não têm participantes</p>";
          }
        ?>
      </div>
  </aside>
  <main class="container-main-porto">
    <div class="container-center">
    <?php
    // initial insert post
    // if usuário ta no grupo.
    if($portoInfo['participa']||$portoInfo['owner']){
      echo "<div class=\"insert-interacao\">";
        echo "<div class=\"insert-interacao-user\">";
          echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
          echo "<div>";
            echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
            echo "<p class=\"insert-interacao-user-assuntos\"></p>";
          echo "</div>";
        echo "</div>";
        echo "<form name=\"newPost\" action=\"porto.php?porto=$_GET[porto]\" method=\"post\" >";
          echo "<textarea name=\"texto\" class=\"insert-interacao-input\" id=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
          echo "<div class=\"insert-interacao-smallBtns\">";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('reacoes')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\">Reação</div>";
            // echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('compartilhar')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/send.png\" alt=\"\" srcset=\"\">Compartilhar</div>";
          echo "</div>";
          echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"novoPost\" />";
          echo "<hr id=\"post-hr\" class=\"post-hr\" >";
          echo "<div class=\"post-divLocal\">";

            echo "<input id=\"insert-codigo-pais\" name=\"insert-codigo-pais\" type=\"hidden\" value=\"\">";
            echo "<input id=\"insert-codigo-estado\" name=\"insert-codigo-estado\" type=\"hidden\" value=\"\">";
            echo "<input id=\"insert-codigo-cidade\" name=\"insert-codigo-cidade\" type=\"hidden\" value=\"\">";
          
            echo "<select id=\"select-pais\" onchange=\"selectPais(this)\">";
              echo "<option value=\"selecionar-pais\">Selecionar Pais</option>";
              foreach ($paises as $value) {
                echo "<option id='optionPais".$value['codigo']."' value='$value[codigo]'>".$value['nome']."</option>\n";
              }
              echo "<option value=\"0\">Outro</option>";
            echo "</select>";
            foreach ($paises as $value) {
              echo "<select id=\"select-estado-pais$value[codigo]\" class=\"select-estado-pais\" style=\"display: none\" onchange=\"selectEstado(this)\">";
                echo "<option value=\"selecionar-estado\">Selecionar Estado</option>";
                foreach ($estados as $value2) {
                  if($value2['pais'] == $value['codigo']){
                    echo "<option id='optionEstado$value2[codigo]' value='$value2[codigo]'>$value2[nome]</option>\n";
                  }
                }
                echo "<option value=\"0\">Outro</option>";
              echo "</select>";
            }
            foreach ($estados as $value) {
              echo "<select id=\"select-cidade-estado$value[codigo]\" class=\"select-cidade-estado\" style=\"display: none\" onchange=\"selectCidade(this)\">";
                echo "<option value=\"selecionar-cidade\">Selecionar Cidade</option>";
                foreach ($cidades as $value2) {
                  if($value2['uf'] == $value['codigo']){
                    echo "<option id='optionCidade$value2[codigo]' value='{ \"id\": \"".$value2['codigo']."\", \"name\": \"".$value2['nome']."\" }'>$value2[nome]</option>\n";
                  }
                }
                echo "<option value='{ \"id\": \"0\", \"name\": \"null\" }'>Outro</option>";
              echo "</select>";
            }
            echo "<input id=\"insert-nome-pais\" name=\"insert-nome-pais\" placeholder=\"Digite o nome do novo pais\" class=hidden>";
            echo "<input id=\"insert-nome-estado\" name=\"insert-nome-estado\" placeholder=\"Digite o nome do novo estado\" class=hidden>";
            echo "<input id=\"insert-nome-cidade\" name=\"insert-nome-cidade\" placeholder=\"Digite o nome da nova cidade \" class=hidden>";
            echo "<button id=\"select-local-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addLocal()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divCidade\"></div>";

          echo "</div>";
          echo "<div class=\"post-divPessoas\">";
            echo "<select id=\"select-pessoas\" onclick=\"unsetError(this)\">";
              foreach ($pessoasArray as $value) {
                echo "<option id='optionPessoa".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['username']."\" }'\">".$value['username']."</option>\n";
              }
            echo "</select>";
            echo "<button id=\"select-pessoa-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addPessoas()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divPessoas\"></div>";
          echo "</div>";
          echo "<div class=\"post-divAssuntos\">";
            echo "<select id=\"select-assuntos\" onchange=\"selectAssunto(this)\">";
              foreach ($assuntosArray as $value) {
                echo "<option id='optionAssunto".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['nome']."\" }'\">".$value['nome']."</option>\n";
              }
              echo "<option value=\"0\">Outro</option>";
            echo "</select>";
            echo "<div id=\"divNewAssuntos\"></div>";
            echo "<input id=\"insert-nome-assunto\" placeholder=\"Digite o nome do novo assunto\" class=hidden>";
            echo "<button id=\"select-assunto-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addAssuntos()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divAssuntos\"></div>";
          echo "</div>";
          echo "<div class=\"post-divReacoes\">";
            echo "<select id=\"select-reacoes\" onclick=\"unsetError(this)\">";
              $reacoesArray = [['codigo'=>'curtir', 'emoji'=>'👌'],['codigo'=>'kkk', 'emoji'=> '🤣'],['codigo'=>'amei', 'emoji'=> '❤️'],['codigo'=>'grr', 'emoji'=> '🤬'],['codigo'=>'wow', 'emoji'=> '🤯'],['codigo'=>'sad', 'emoji'=> '😭']];
              foreach ($reacoesArray as $value) {
                echo "<option id='optionReacao".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['emoji']."\" }'\">$value[emoji] $value[codigo]</option>\n";
              }
            echo "</select>";
            echo "<button id=\"select-reacao-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addReacoes()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divReacoes\"></div>";
          echo "</div>";
          // echo "<div class=\"post-divCompart\">";
          //   echo "<select id=\"select-compartilhar\" onchange=\"changeCompartilhar(this)\">";
          //     echo "<option id='optionCompartilhar' value=''>Selecione onde vai compartilhar</option>\n";
          //     echo "<option id='optionCompartilhar' value='feed'>No feed</option>\n";
          //     echo "<option id='optionCompartilhar' value='grupo'>Em um grupo</option>\n";
          //     echo "<option id='optionCompartilhar' value='perfil'>Em um perfil</option>\n";
          //   echo "</select>";
          //   echo "<select id=\"select-compartilhar-porto\">";
          //   foreach ($portosArrayForShare as $porto) {
          //     echo "<option id='optionCompartilharPorto' value='$porto[codigo]'>$porto[nome]</option>\n";
          //   }
          //   echo "</select>";
          //   echo "<button id=\"select-reacao-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addReacoes()\">Confirmar</button>";
          //   echo "<div class=\"comment-container-top\" id=\"divReacoes\"></div>";
          // echo "</div>";
        echo "</form>";
      echo "</div>";
    }
    
    // posts
    if($postsArray){
      echo "<form action=\"feed.php?user=$_SESSION[userid]\" id=\"formOrderby\" method=\"get\">";
      echo "<div class=\"order-btn\">";
      echo "<p>Ordene por </p>";
      echo "<select onchange=\"document.getElementById('formOrderby').submit();\" id=\"select-ordenar\" name=\"orderby\">";
      echo "<option value=\"tmp1.data desc\" ".($_GET['orderby'] == "tmp1.data desc" ? "selected" : "").">Data descrecente</option>";
        echo "<option value=\"tmp1.data asc\" ".($_GET['orderby'] == "tmp1.data asc" ? "selected" : "").">Data crescente</option>";
        echo "<option value=\"tmpQtd.qtd desc\" ".($_GET['orderby'] == "tmpQtd.qtd desc" ? "selected" : "").">Popularidade descrescente</option>";
        echo "<option value=\"tmpQtd.qtd asc\" ".($_GET['orderby'] == "tmpQtd.qtd asc" ? "selected" : "").">Popularidade crescente</option>";
      echo "</select>";
      echo "</div>";
      echo "</form>";
      foreach ($postsArray as $post) {
        // print_r($post);
        echo "<div class=\"div-post\">";
          if($post['codPorto'] != $_GET['porto']){
            echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=porto.php?porto=$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
          }
          //Share
          $sharedPost = 0;
          if($post['isSharing']){
            $sharedPost = [];//getOriginalPostOnPorto($post['codPost'], $_GET['porto']);
            echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
            echo "<div class=\"div-sharing-post\">";
              // Sharing-top
              echo "<div class=\"div-sharing-post-top\">";
                echo "<a href=navio.php?user=$sharedPost[codPerfil]><img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\"></a>";
                echo "<div class=\"div-post-top-infos\">";
                echo "<div class=\"row\">";
                if($sharedPost['codSelo'] == 3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
                if($sharedPost['codSelo'] == 2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
                if($sharedPost['codSelo'] == 1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
                echo "<p class=\"div-post-top-username\"><i>@".$sharedPost['nomePerfil']."</i>";
                echo "</div>";
                  if($sharedPost['nomeCidade']){
                    echo " em ".$sharedPost['nomeCidade'].", ".$sharedPost['nomePais']." - ";
                  }
                  $tmpHora = explode(' ', $sharedPost['dataPost'])[1];
                  $tmpData = explode(' ', $sharedPost['dataPost'])[0];
                  $tmpData = explode('-', $tmpData);
                  echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                  $tmpArray = [];
                  foreach($sharedPost['assuntos'] as $elements){
                    foreach ($elements as $key => $value) {
                      if($key === 'nomeAssunto') $tmpArray[] = $value;
                    }
                  }
                  echo "<p class=\"div-post-top-subjects\" title=\"".implode(', ',$tmpArray)."\"><b>";
                  $tmpArray = implode(', ',$tmpArray);
                  if(strlen($tmpArray) > 30){
                    $tmpArray = substr($tmpArray, 0, 27);
                    echo $tmpArray."...";
                  } else {
                    echo $tmpArray;
                  }
                  echo "</b></p>";
                echo "</div>";
              echo "</div>";
              // Sharing-texto
              echo "<div class=\"div-post-txt\">";
                echo "<p><i style=\"color: #7A9EFB\">@$sharedPost[nomePerfil]</i> ";
                if($sharedPost['isReaction']) {
                  echo "<b><i>reagiu</i></b> com ";
                  switch ($sharedPost['emote']){
                    case 'curtir':
                      echo "👌";
                      break;
                    case 'kkk':
                      echo "🤣";
                      break;
                    case 'amei':
                      echo "❤️";
                      break;
                    case 'grr':
                      echo "🤬";
                      break;
                    case 'wow':
                      echo "🤯";
                      break;
                    case 'sad':
                      echo "😭";
                      break;                  
                  }
                  echo ", ";
                }
                if(count($sharedPost['citacoes']) > 0) {
                  $tmpCitacoes = [];
                  foreach ($sharedPost['citacoes'] as $pessoa) {
                    $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                  }
                  $tmpCitacoes = implode(', ',$tmpCitacoes);
                  echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                  if(strlen($tmpCitacoes) > 10){
                    $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                    echo $tmpCitacoes."...";
                  } else {
                    echo $tmpCitacoes;
                  }
                  echo ", </i>";
                  
                }
                echo "$sharedPost[textoPost]</p>";
              echo "</div>";
            echo "</div>";
          }
          //Top
          echo "<div class=\"div-post-top\">";
            echo "<a href=navio.php?user=$post[codPerfil]><img src=\"".$post['iconPerfil']."\" alt=\"\" class=\"div-post-top-icon\"></a>";
            echo "<div class=\"div-post-top-infos\">";
            echo "<div class=\"row\">";
              if($post['codSelo'] == 3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
              if($post['codSelo'] == 2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
              if($post['codSelo'] == 1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
              echo "<p class=\"div-post-top-username\"><i>@".$post['nomePerfil']."</i>";
            echo "</div>";
              if($post['nomeCidade']){
                echo " em ".$post['nomeCidade'].", ".$post['nomePais']." - ";
              }
              $tmpHora = explode(' ', $post['dataPost'])[1];
              $tmpData = explode(' ', $post['dataPost'])[0];
              $tmpData = explode('-', $tmpData);
              echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
              $tmpArray = [];
              // print_r($post['assuntos']);
              foreach($post['assuntos'] as $elements){
                foreach ($elements as $key => $value) {
                  if($key === 'nomeAssunto') $tmpArray[] = $value;
                }
              }
              echo "<p class=\"div-post-top-subjects\" title=\"".implode(', ',$tmpArray)."\"><b>";
              $tmpArray = implode(', ',$tmpArray);
              if(strlen($tmpArray) > 30){
                $tmpArray = substr($tmpArray, 0, 27);
                echo $tmpArray."...";
              } else {
                echo $tmpArray;
              }
              echo "</b></p>";
            echo "</div>";
            if($post['isSharing'] && ($sharedPost['codPerfil'] == $_SESSION['userid'])){
              echo "<div class=\"div-post-top-editicons\">";
              echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"./imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
              echo "</form>";
              echo "</div>";
            } 
            if($post['codPerfil'] == $_SESSION['userid']) {
              echo "<div class=\"div-post-top-editicons\">";
              echo "<a href=\"editarInteracao.php?interacao=$post[codInteracao]\"><img src=\"./imgs/icons/pencil.png\" class=\"div-post-top-editicons-pencil\" alt=\"\" /></a>";
              echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"./imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
              echo "</form>";
              echo "</div>";
            }
          echo "</div>";
          //Texto
          echo "<div class=\"div-post-txt\">";
            echo "<p><i style=\"color: #7A9EFB\">@$post[nomePerfil]</i> ";
            if($post['isReaction']) {
              echo "<b><i>reagiu</i></b> com ";
              switch ($post['emote']){
                case 'curtir':
                  echo "👌";
                  break;
                case 'kkk':
                  echo "🤣";
                  break;
                case 'amei':
                  echo "❤️";
                  break;
                case 'grr':
                  echo "🤬";
                  break;
                case 'wow':
                  echo "🤯";
                  break;
                case 'sad':
                  echo "😭";
                  break;                  
              }
              echo ", ";
            }
            $isMentioned = 0;
            if(count($post['citacoes']) > 0) {
              $tmpCitacoes = [];
              foreach ($post['citacoes'] as $pessoa) {
                $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                if($pessoa['codPerfil'] == $_SESSION['userid'] && $post['codPerfil'] != $_SESSION['userid']) $isMentioned = 1;
              }
              $tmpCitacoes = implode(', ',$tmpCitacoes);
              echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
              if(strlen($tmpCitacoes) > 10){
                $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                echo $tmpCitacoes."...";
              } else {
                echo $tmpCitacoes;
              }
              echo ", </i>";
            }
            echo "$post[textoPost]</p>";
          echo "</div>";
          //Ícones
          echo "<div class=\"div-post-icons-bar\">";
            if($isMentioned) {
              echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"removeCitacao\" class=\"interacao-remover-txt\" value=\"$post[codInteracao]\"><p>Remover sua citação</p></button>";
              echo "</form>";
            }
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>$post[qtdInteracao]</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            echo "<div class=\"div-post-icons-bar-interagir\">";
              echo "<a href=\"interagirInteracao.php?interacao=$post[codInteracao]\"><img src=\"$user[img]\" class=\"div-post-icons-bar-interagir-icon\" alt=\"\"><p>Interagir...</p></a>";
            echo "</div>";
          echo "</div>";
          echo "<br><br>";
          //Comentários
          if($post['comentarios'] && $post['comentarios'] != []){
            echo "<hr class=\"post-hr\">";
            foreach ($post['comentarios'] as $comentario) {
              echo "<div class=\"comment-container\">";
                echo "<div class=\"comment-container-top\">";
                  echo "<a href=navio.php?user=$comentario[codPerfil]><img src=\"".$comentario['iconPerfil']."\" alt=\"\" class=\"comment-icon\"></a>";
                  echo "<p class=\"comment-txt\"><i>@".$comentario['nomePerfil']."</i> ";
                  if($comentario['isReaction']) {
                    echo "<b><i>reagiu</i></b> com ";
                    switch ($comentario['emote']){
                      case 'curtir':
                        echo "👌";
                        break;
                      case 'kkk':
                        echo "🤣";
                        break;
                      case 'amei':
                        echo "❤️";
                        break;
                      case 'grr':
                        echo "🤬";
                        break;
                      case 'wow':
                        echo "🤯";
                        break;
                      case 'sad':
                        echo "😭";
                        break;                  
                    }
                    echo ", ";
                  }
                  $isMentioned2 = 0;
                  if(count($comentario['citacoes']) > 0) {
                    $tmpCitacoes = [];
                    foreach ($comentario['citacoes'] as $pessoa) {
                      $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                      if($pessoa['codPerfil'] == $_SESSION['userid'] && $comentario['codPerfil'] != $_SESSION['userid']) $isMentioned2 = 1;
                    }
                    $tmpCitacoes = implode(', ',$tmpCitacoes);
                    echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                    if(strlen($tmpCitacoes) > 10){
                      $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                      echo $tmpCitacoes."...";
                    } else {
                      echo $tmpCitacoes;
                    }
                    echo ", </i>";
                  }
                  if(count($comentario['assuntos']) > 0) {
                    $tmpAssuntos = [];
                    foreach ($comentario['assuntos'] as $assunto) {
                      $tmpAssuntos[] = $assunto['nomeAssunto'];
                    }
                    $tmpAssuntos = implode(', ',$tmpAssuntos);
                    echo "com os <b><i>assuntos</i></b> <i title=\"".$tmpAssuntos."\">";
                    if(strlen($tmpAssuntos) > 10){
                      $tmpAssuntos = substr($tmpAssuntos, 0, 7);
                      echo $tmpAssuntos."...";
                    } else {
                      echo $tmpAssuntos;
                    }
                    echo ", </i>";
                  }
                  echo ($comentario['textoPost'] ? $comentario['textoPost'] : '');
                  echo ", em ";
                  if($comentario['nomeCidade']){
                    echo $comentario['nomeCidade'].", ".$comentario['nomePais']." - ";
                  }
                  $tmpHora = explode(' ', $comentario['dataPost'])[1];
                  $tmpData = explode(' ', $comentario['dataPost'])[0];
                  $tmpData = explode('-', $tmpData);
                  echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                  echo "</p>";
                echo "</div>";
                echo "<div class=\"comment-reagir\">";
                echo "<a href=\"interagirInteracao.php?interacao=$comentario[codInteracao]\">Reagir</a>";
                  if($comentario['codPerfil'] == $_SESSION['userid']) {
                    echo "<a href=\"editarInteracao.php?interacao=$comentario[codInteracao]\"><p class=\"interacao-editar-txt\">- Editar -</p></a>";
                    echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
                    echo "<button type=\"submit\" name=\"deletePost\" value=\"$comentario[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                    echo "</form>";
                  }
                  if($comentario['codPerfil'] != $_SESSION['userid'] && $post['codPerfil'] == $_SESSION['userid']) {
                    echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
                    echo "<button type=\"submit\" name=\"deletePost\" value=\"$comentario[codInteracao]\"><p class=\"interacao-remover-txt\">- Remover</p></button>";
                    echo "</form>";
                  }
                echo "</div>";
                // Respostas
                if($comentario['respostas'] && $comentario['respostas'] != []){
                  foreach ($comentario['respostas'] as $resposta) {
                    echo "<div class=\"comment-resp-container\">";
                      echo "<div class=\"comment-container-top\">";
                        echo "<a href=navio.php?user=$resposta[codPerfil]><img src=\"".$resposta['iconPerfil']."\" alt=\"\" class=\"comment-icon\"></a>";
                        echo "<div class=\"row\">";
                          // echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";   
                          echo "<p class=\"comment-txt\"><i>@".$resposta['nomePerfil']."</i> ";
                          if($resposta['isReaction']) {
                            echo "<b><i>reagiu</i></b> com ";
                            switch ($resposta['emote']){
                              case 'curtir':
                                echo "👌";
                                break;
                              case 'kkk':
                                echo "🤣";
                                break;
                              case 'amei':
                                echo "❤️";
                                break;
                              case 'grr':
                                echo "🤬";
                                break;
                              case 'wow':
                                echo "🤯";
                                break;
                              case 'sad':
                                echo "😭";
                                break;                  
                            }
                            echo ", ";
                          }
                          $isMentioned2 = 0;
                          if(count($resposta['citacoes']) > 0) {
                            $tmpCitacoes = [];
                            foreach ($resposta['citacoes'] as $pessoa) {
                              $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                              if($pessoa['codPerfil'] == $_SESSION['userid'] && $resposta['codPerfil'] != $_SESSION['userid']) $isMentioned2 = 1;
                            }
                            $tmpCitacoes = implode(', ',$tmpCitacoes);
                            echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                            if(strlen($tmpCitacoes) > 10){
                              $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                              echo $tmpCitacoes."...";
                            } else {
                              echo $tmpCitacoes;
                            }
                            echo ", </i>";
                          }
                          if(count($resposta['assuntos']) > 0) {
                            $tmpAssuntos = [];
                            foreach ($resposta['assuntos'] as $assunto) {
                              $tmpAssuntos[] = $assunto['nomeAssunto'];
                            }
                            $tmpAssuntos = implode(', ',$tmpAssuntos);
                            echo "com os <b><i>assuntos</i></b> <i title=\"".$tmpAssuntos."\">";
                            if(strlen($tmpAssuntos) > 10){
                              $tmpAssuntos = substr($tmpAssuntos, 0, 7);
                              echo $tmpAssuntos."...";
                            } else {
                              echo $tmpAssuntos;
                            }
                            echo ", </i>";
                          }
                          echo ($resposta['textoPost'] ? $resposta['textoPost'] : '');
                          echo ", em ";
                          if($resposta['nomeCidade']){
                            echo $resposta['nomeCidade'].", ".$resposta['nomePais']." - ";
                          }
                          $tmpHora = explode(' ', $resposta['dataPost'])[1];
                          $tmpData = explode(' ', $resposta['dataPost'])[0];
                          $tmpData = explode('-', $tmpData);
                          echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                          echo "</p>";
                        echo "</div>";
                      echo "</div>";
                      echo "<div class=\"comment-reagir\">";
                      echo "<a href=\"interagirInteracao.php?interacao=$resposta[codInteracao]\">Reagir</a>";
                        if($resposta['codPerfil'] == $_SESSION['userid']) {
                          echo "<a href=\"editarInteracao.php?interacao=$resposta[codInteracao]\"><p class=\"interacao-editar-txt\">- Editar -</p></a>";
                          echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
                          echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                          echo "</form>";
                        }
                        if($resposta['codPerfil'] != $_SESSION['userid'] && $comentario['codPerfil'] == $_SESSION['userid']) {
                          echo "<form action=\"porto.php?porto=$_GET[porto]\" method=\"post\">";
                          echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">- Remover</p></button>";
                          echo "</form>";
                        }
                      echo "</div>";
                    echo "</div>";
                  }
                }
                if($resposta['qtdInteracao'] > 1){
                  echo "<p align=center><a href=completeInteracao.php?interacao=$comentario[codInteracao]>Ver mais respostas</a></p>";
                }
              echo "</div>";
            }
          }
          if($post['qtdInteracao'] > 0){
            // echo "<hr class=\"post-hr-gray\">";
            // echo "<p align=center ><a href=completeInteracao.php?interacao=$post[codInteracao] style=\"txt-verMaisComentarios\">Ver mais</a></p>";
          }
        echo "</div>";
      }
    }
    ?>
    </div>
  </main>
</div>
<!-- <footer class="container-bottom" ><p align="center"><< 1 2 3 >></p></footer> -->
<?php 
  // echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>

<script src="js/functions.js"></script>
</body>
</html>
