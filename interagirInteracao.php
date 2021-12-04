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
  include './backend/infra/connection.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    $user = getUserInfo("$_SESSION[userid]");
    $post = getOriginalPost($_GET['interacao']);
    $locaisArray = getLocais();
    $assuntosArray = getAssuntos();
    $pessoasArray = getPessoas();
    $paises=getPaises();
    $estados=getStates();
    $cidades=getCities();
    if(isset($_POST['novoPost'])){
      $texto = ''.$_POST['texto'];
      $reacao = isset($_POST['reacao']) ? $_POST['reacao'] : 0;
      $isReaction = isset($_POST['reacao']) ? 1 : 0;
      $local = isset($_POST['local']) ? $_POST['local'] : 0;
      $assuntos = [];
      $citacoes = [];
      // if(isset($_POST['addEstado'])){
      //   $pais = $_POST['SelectedPais'];
      //   $estado = "$_POST[addEstado]";
      //  $addEstado = addEstado($estado, $pais);
      // };
      // if(isset($_POST['addCidade'])){
      //   // $estado = lastInsertRowID();
      //   $cidade = "$_POST[addCidade]";
      //   $addCidade = addCidade($cidade, $estado);
      // };
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
      $response = addInteracao($_SESSION['userid'], $texto, 0, 0, 0, 0, 0, $isReaction, $reacao, $local);
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
        // header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
      }
      else return false;
    }
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
?>
    <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a a-selected\" href=feed.php?user=$_SESSION[userid]>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php?user=$_SESSION[userid]>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <main class="container-center">
    <?php
   if($post){
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
            // if($isMentioned) {
            //   echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\">";
            //   echo "<button type=\"submit\" name=\"removeCitacao\" class=\"interacao-remover-txt\" value=\"$post[codInteracao]\"><p>Remover sua citação</p></button>";
            //   echo "</form>";
            // }
            // echo "<div class=\"div-post-icons-bar-divs\">";
            //   echo "<p>$post[qtdInteracao]</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            // echo "</div>";
          echo "</div>";
          echo "<br><br>";
          
          echo "</div>";
          //Interar
        echo "<br> <br>";
        echo "<div class=\"insert-interacao\">";
        echo "<div class=\"insert-interacao-user\">";
          echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
          echo "<div>";
            echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
            echo "<p class=\"insert-interacao-user-assuntos\"></p>";
          echo "</div>";
        echo "</div>";
        echo "<form name=\"newPost\" action=\"feed.php?user=$_SESSION[userid]\" method=\"post\" >";
          echo "<textarea name=\"texto\" class=\"insert-interacao-input\" id=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
          echo "<div class=\"insert-interacao-smallBtns\">";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('reacoes')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\">Reação</div>";
          echo "</div>";
          echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"novoPost\" />";
          echo "<hr id=\"post-hr\" class=\"post-hr\" >";
          echo "<div class=\"post-divLocal\">";
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
                echo "<option value=\"0\">Outro</option>";
              echo "</select>";
            }
            echo "<input id=\"insert-nome-pais\" name=\"insert-nome-pais\" class=hidden>";
            echo "<input id=\"insert-nome-estado\" name=\"insert-nome-estado\" class=hidden>";
            echo "<input id=\"insert-nome-cidade\" name=\"insert-nome-cidade\" class=hidden>";
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
            echo "<select id=\"select-assuntos\" onclick=\"unsetError(this)\">";
              foreach ($assuntosArray as $value) {
                echo "<option id='optionAssunto".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['nome']."\" }'\">".$value['nome']."</option>\n";
              }
              echo "<option value=\"0\">Outro</option>";
            echo "</select>";
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