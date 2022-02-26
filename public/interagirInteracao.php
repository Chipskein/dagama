<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
    <title>Dagama | Interagir</title>
</head>
<body>
<?php
  include '../backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    $user = getUserInfo("$_SESSION[userid]");
    $post = getOriginalPost($_GET['interacao']);
    $postPai = $post['postPai'] ? $post['postPai'] : $_GET['interacao'];
    $locaisArray = getLocais();
    $assuntosArray = getAssuntos();
    $pessoasArray = getPessoas();
    $qtdPortos = getTotalPorto();
    $portosArrayForShare = getAllPorto($_SESSION['userid'], true, 0, $qtdPortos, null);
    $qtdAmigos = getFriends($_SESSION['userid'], 0, 1,'');
    $friends = [];
    if(count($qtdAmigos) != 0){
      $qtdAmigos = $qtdAmigos[0]['qtdAmigos'];            
      $friends = getFriends($_SESSION['userid'], 0, $qtdAmigos,'');
    } 
    $paises=getPaises();
    $estados=getStates();
    $cidades=getCities();
    $errorMessage = [];
    if(isset($_POST['buttonAssunto'])){
      $addAssunto = addAssunto("$_POST[buttonAssunto]");
      header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
    };
    if(isset($_POST['novoPost'])){
      // var_dump($_POST);
      $texto = ''.$_POST['texto'];
      $reacao = isset($_POST['reacao']) ? $_POST['reacao'] : 0;
      $isReaction = isset($_POST['reacao']) ? 1 : 0;
      $isSharing = 0;
      $porto = 0;
      $perfil_posting = 0;
      $assuntos = [];
      $citacoes = [];
      
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
            $pais = addPais($novoPaisNome);
            $estado = addEstado($novoEstadoNome, $pais);
            $local = addCidade($novoCidadeNome, $estado);
          }
          if($codPais != 0 && preg_match('#^[0-9]{1,}$#', $codPais)){  
            if($codEstado == 0){
              // cria novo estado e cidade
              $estado = addEstado($novoEstadoNome, $codPais);
              $local = addCidade($novoCidadeNome, $estado);
            }
          if($codEstado != 0 && preg_match('#^[0-9]{1,}$#', $codEstado)){
            if($codCidade == 0){
                // cria nova cidade
                $local = addCidade($novoCidadeNome, $codEstado);
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
      $postPai = $post['postPai'] ? $post['postPai'] : $post['codInteracao'];
      if(isset($_POST['compartilhar-feed'])){
        $isSharing = 1;
      }
      if(isset($_POST['compartilhar-porto'])){
        $isSharing = 1;
        $porto = $_POST['compartilhar-porto'];
      }
      if(isset($_POST['compartilhar-perfil'])){
        $isSharing = 1;
        $perfil_posting = $_POST['compartilhar-perfil'];
      }
      // print_r([$_SESSION['userid'], $texto, $perfil_posting, $porto, $isSharing, $post['codInteracao'], $postPai, $isReaction, $reacao, $local]);

      $response = addInteracao($_SESSION['userid'], $texto, $perfil_posting, $porto, $isSharing, $post['codInteracao'], $postPai, $isReaction, $reacao, $local);
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
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
      }
      else return false;
    }
?>
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
      echo "<a class=\"header-links-a a-selected\" href=feed.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main class="container-center">
    <?php
        echo "<div class=\"div-post\">";
          if($post['codPorto']){
            echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=porto.php?porto=$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
          }
          //Share
          $sharedPost = 0;
          if($post['isSharing']){
            $sharedPost = getOriginalPost($post['codPost']);
            echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
            echo "<div class=\"div-sharing-post\">";
              // Sharing-top
              echo "<div class=\"div-sharing-post-top\">";
                echo "<a href=navio.php?user=$sharedPost[codPerfil]><img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\"></a>";
                echo "<div class=\"div-post-top-infos\">";
                echo "<div class=\"row\">";
                // if($selo==3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
                // if($selo==2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
                // if($selo==1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
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
                  // print_r($post['assuntos']);
                  foreach($sharedPost['assuntos'] as $elements){
                    foreach ($elements as $key => $value) {
                      if($key === 'nomeAssunto') $tmpArray[] = $value;
                    }
                  }
                  echo "<p class=\"div-post-top-subjects\" title=\"".implode($tmpArray, ', ')."\"><b>";
                  $tmpArray = implode($tmpArray, ', ');
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
                  $tmpCitacoes = implode($tmpCitacoes, ', ');
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
            // if($selo==3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
            // if($selo==2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
            // if($selo==1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
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
              echo "<p class=\"div-post-top-subjects\" title=\"".implode($tmpArray, ', ')."\"><b>";
              $tmpArray = implode($tmpArray, ', ');
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
              echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"./imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
              echo "</form>";
              echo "</div>";
            } 
            if($post['codPerfil'] == $_SESSION['userid']) {
              echo "<div class=\"div-post-top-editicons\">";
              echo "<a href=\"editarInteracao.php?interacao=$post[codInteracao]\"><img src=\"./imgs/icons/pencil.png\" class=\"div-post-top-editicons-pencil\" alt=\"\" /></a>";
              echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\">";
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
                if($pessoa['codPerfil'] == $_SESSION['userid']) $isMentioned = 1;
              }
              $tmpCitacoes = implode($tmpCitacoes, ', ');
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
          echo "</div>";
          echo "<br><br>";
          
          echo "</div>";
        
      //Interagir
      echo "<br> <br>";
      echo "<div class=\"insert-interacao\">";
        echo "<div class=\"insert-interacao-user\">";
          echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
          echo "<div>";
            echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
            echo "<p class=\"insert-interacao-user-assuntos\"></p>";
          echo "</div>";
        echo "</div>";
        echo "<form name=\"newPost\" action=\"interagirInteracao.php?interacao=$_GET[interacao]".(isset($_GET['porto']) ? "&".$_GET['porto'] : "" )."\" method=\"post\" >";
          echo "<textarea name=\"texto\" class=\"insert-interacao-input\" id=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
          echo "<div class=\"insert-interacao-smallBtns\">";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('reacoes')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\">Reação</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('compartilhar')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/send.png\" alt=\"\" srcset=\"\">Compartilhar</div>";
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
          echo "<div class=\"post-divCompart\">";
            echo "<select id=\"select-compartilhar\" onchange=\"selectCompartilhar(this)\">";
              echo "<option id='optionCompartilhar' selected value=''>Selecione onde vai compartilhar</option>\n";
              echo "<option id='optionCompartilhar' value='feed'>No feed</option>\n";
              echo "<option id='optionCompartilhar' value='grupo'>Em um grupo</option>\n";
              echo "<option id='optionCompartilhar' value='perfil'>Em um perfil</option>\n";
            echo "</select>";
            echo "<select id=\"select-compartilhar-porto\" class=hidden>";
            foreach ($portosArrayForShare as $porto) {
              echo "<option id='optionCompartilharPorto$porto[codigo]' value='$porto[codigo]'>$porto[nome]</option>\n";
            }
            echo "</select>";
            echo "<select id=\"select-compartilhar-amigo\" class=hidden>";
            echo "<option id='optionCompartilharAmigo$_SESSION[userid]' value='$_SESSION[userid]'>No seu perfil</option>\n";
            foreach ($friends as $perfil) {
              echo "<option id='optionCompartilharAmigo$perfil[amigoCod]' value='$perfil[amigoCod]'>$perfil[nameAmigo]</option>\n";
            }
            echo "</select>";
            echo "<div id=\"divCompart\"></div>";
            echo "<button id=\"select-compartilhar-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addCompartilhar()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divReacoes\">";
            echo "</div>";
          echo "</div>";
        echo "</form>";
      echo "</div>";
    echo "</main>";    
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  ?>
  </main>

<script src='./functions.js'>
    
</script>
</body>
</html>