<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Navio</title>
</head>
<body class=perfil>
<?php
  include '../backend/infra/services.php';
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
    function url($campo, $valor) {
      $result = array();
      // if (isset($_GET["sabor"])) $result["sabor"] = "sabor=".$_GET["sabor"];
      // if (isset($_GET["tipo"])) $result["tipo"] = "tipo=".$_GET["tipo"];
      // if (isset($_GET["ingrediente"])) $result["ingrediente"] = "ingrediente=".$_GET["ingrediente"];
      if (isset($_GET["orderby"])) $result["orderby"] = "orderby=".$_GET["orderby"];
      if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
      $result[$campo] = $campo."=".$valor;
      return("navio.php?user=$_GET[user]&".strtr(implode("", $result), " ", "+"));
  }
  function pages($campo, $valor){
      $result = array();
      if (isset($_GET["page"])) $result["page"] = "page=".$_GET["page"];
      $result[$campo] = $campo."=".$valor;
      return '&'.(strtr(implode("&",$result), " ", "+"));
  }
    if(isset($_GET['user'])){
      $orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "tmp1.data desc";
      $user=getUserInfo("$_GET[user]");
      $userSelf=getUserInfo("$_SESSION[userid]");
      if(!$user){
        echo "Usuario inválido";
        header("refresh:1;url=mar.php");
        die();
      }
      $isOwner= "$_GET[user]"=="$_SESSION[userid]" ? true:false;
      $limit = 5;
      $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
      $orderby = (isset($_GET["orderby"])) ? $_GET["offset"] : "tmp1.data desc";
      $getAllPosts = [];//getAllPosts($_GET['user']);
      $postsArray = [];//getPosts($_GET['user'], $offset, $limit, $orderby);
      $amigosUser =[]; //getFriends($_GET['user'], 0, 3,'');
      $portosArray = getAllPorto($_GET['user'], true, 0, 3, null);
      $portosUser = getUserPortoQtd($_GET['user']);
      $locaisArray = [];
      $assuntosArray = getAssuntos();
      $pessoasArray = getPessoas();
      $paises=getPaises();
      $estados=[];
      $cidades=[];
      if(isset($_POST['novoPost'])){
        $texto = ''.$_POST['texto'];
        $reacao = isset($_POST['reacao']) ? $_POST['reacao'] : 0;
        $isReaction = isset($_POST['reacao']) ? 1 : 0;
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
        $response = addInteracao($_SESSION['userid'], $texto, $_GET['user'], 0, 0, 0, 0, $isReaction, $reacao, $local);
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
          header("refresh:0;url=navio.php?user=$_GET[user]"); 
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
          header("refresh:0;url=navio.php?user=$_GET[user]"); 
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
          header("refresh:0;url=navio.php?user=$_GET[user]"); 
        }
      }
    } else{
      echo "Usuario inválido";
      header("refresh:1;url=mar.php");
      die();
    }
  }
  if(isset($_FILES["photo"])){
    $photo=$_FILES["photo"];
    $oldphoto=$_SESSION['userimg'];//link
    $oldphotoid=substr($oldphoto,47);
    $newimg=updateImg($_SESSION['userid'],$photo,$oldphotoid);
    if($newimg){
      $_SESSION['userimg']="$newimg";
      header("Refresh:0");
    }
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
      echo "<a class=\"header-links-a\" href=mar.php>Portos</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Meu navio</a> ";
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <?php
  if($user['ativo']){
    echo "<aside id=direita>";
      echo "<div align=center class=background>";
        echo "<p class=portosAtracados>Portos atracados:</p>";
        if($portosArray){
          foreach ($portosArray as $value) {
            echo "<div class=\"row porto-feed-container\">
              <div class=\"portos-img\" style=\"background-image: url($value[img])\"></div>
              <a class=nomePort href=porto.php?porto=$value[codigo]>$value[nome]</a>
            </div>";
          }
          echo "<br><a class=portosAtracadosMais href=portosUser.php?user=$_GET[user]>Ver todos</a>";
        } else {
          echo "<p>Sem portos ainda</p>";
        }
      echo "</div>";
    echo "</aside>";
  }
  ?>
  <main>
    <div align=center>
    <!--Add onlick change-->
      <br>
      <div id="img_perfil" class=perfil></div>
      <form id=formPhoto action=<?php echo "navio.php?user=$_GET[user]"?> enctype=multipart/form-data method="POST">
          <input id="imgInp" class="hidden" type="file" name="photo">
      </form>
      <?php 
        if($isOwner)echo "<div id=camera-icon></div>";
      ?>
      <?php echo "<div align=center class=divUsername>";
            echo "<h3 class=perfil>$user[username]</h3>";
            if($isOwner) echo"<a href=editNavio.php?user=$_GET[user]><img class=img-pencil src=\"imgs/icons/clarity_pencil-line.png\"</img></a>";
            echo "</div>";
      ?>
    </div>
    <br>
    <?php
      echo "<div align=center>";
        echo "<div class=perfil-amigos>";
            if($user['ativo']){
              echo "<a href=amigos.php?user=$_GET[user] class=amigos> Amigos: ".($amigosUser ? $amigosUser[0]['qtdAmigos'] : 0)."</a>";
            }
            if($isOwner) echo "<h3><a href=portosUser.php?owner&user=$_GET[user] class=amigos>Meus Portos: $portosUser</a></h3>";
        echo "</div>";
      echo "</div>";
    ?>
    <br>
    <div class="container-center">
    <?php
    if(!$user['ativo']){
      echo "Este perfil está desativado";
    } else {
      echo "<div class=\"center\">";
        // initial insert post
        echo "<div class=\"insert-interacao\">";
          echo "<div class=\"insert-interacao-user\">";
            echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$userSelf["img"]."\" alt=\"\" srcset=\"\">";
            echo "<div>";
              echo "<p class=\"insert-interacao-user-name\">".$userSelf["username"].":</p>";
              echo "<p class=\"insert-interacao-user-assuntos\"></p>";
            echo "</div>";
          echo "</div>";
          echo "<form name=\"newPost\" action=\"navio.php?user=$_GET[user]\" method=\"post\" >";
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
      echo "</div>";

      // posts
    if($postsArray){
      echo "<form action=\"navio.php?user=$_GET[user]\" id=\"formOrderby\" method=\"get\">";
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
          if($post['codPorto']){
            echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=porto.php?porto=$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
          }
          //Share
          $sharedPost = 0;
          if($post['isSharing']){
            $sharedPost = [];//getOriginalPost($post['codPost']);
            echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
            echo "<div class=\"div-sharing-post\">";
              // Sharing-top
              echo "<div class=\"div-sharing-post-top\">";
                echo "<a href=navio.php?user=$sharedPost[codPerfil]><img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\"></a>";
                echo "<div class=\"div-post-top-infos\">";
                echo "<div class=\"row\">";
                if($selo==3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
                if($selo==2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
                if($selo==1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
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
            // echo "<div class=\"row\">";
            // if($selo==3)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/bronze-medal.png\"/>";
            // if($selo==2)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/silver-medal.png\"/>";
            // if($selo==1)echo "<img class=\"coment-mainuser-user-selo\" src=\"./imgs/icons/gold-medal.png\"/>";
            echo "<p class=\"div-post-top-username\"><i>@".$post['nomePerfil']."</i>";
            // echo "</div>";
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
              echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"./imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
              echo "</form>";
              echo "</div>";
            } 
            if($post['codPerfil'] == $_SESSION['userid']) {
              echo "<div class=\"div-post-top-editicons\">";
              echo "<a href=\"editarInteracao.php?interacao=$post[codInteracao]\"><img src=\"./imgs/icons/pencil.png\" class=\"div-post-top-editicons-pencil\" alt=\"\" /></a>";
              echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
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
              echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
              echo "<button type=\"submit\" name=\"removeCitacao\" class=\"interacao-remover-txt\" value=\"$post[codInteracao]\"><p>Remover sua citação</p></button>";
              echo "</form>";
            }
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>$post[qtdInteracao]</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            echo "<div class=\"div-post-icons-bar-interagir\">";
              echo "<a href=\"interagirInteracao.php?interacao=$post[codInteracao]\"><img src=\"$userSelf[img]\" class=\"div-post-icons-bar-interagir-icon\" alt=\"\"><p>Interagir...</p></a>";
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
                    echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
                    echo "<button type=\"submit\" name=\"deletePost\" value=\"$comentario[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                    echo "</form>";
                  }
                  if($comentario['codPerfil'] != $_SESSION['userid'] && $post['codPerfil'] == $_SESSION['userid']) {
                    echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
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
                          echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
                          echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                          echo "</form>";
                        }
                        if($resposta['codPerfil'] != $_SESSION['userid'] && $comentario['codPerfil'] == $_SESSION['userid']) {
                          echo "<form action=\"navio.php?user=$_GET[user]\" method=\"post\">";
                          echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">- Remover</p></button>";
                          echo "</form>";
                        }
                      echo "</div>";
                    echo "</div>";
                  }
                }
                if($comentario['qtdInteracao'] > 1){
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
  }
  ?>
    </div>
  <?php 
   echo "<footer style=\"padding-top:20px; padding-bottom:20px\" align=center>";
   $links = 4;
   $page = isset($_GET["page"]) ? strtr($_GET["page"], " ", "%") : 0;
   echo "<div style=\"row\">";
   echo "<a class=\"paginacaoNumber\" href=\"".url("offset",0*$limit).pages("page", 1)."\">primeira </a>";
   for($pag_inf = $page - $links ;$pag_inf <= $page - 1;$pag_inf++){
       if($pag_inf >= 1 ){
           echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_inf-1)*$limit).pages("page", $pag_inf)."\"> ".($pag_inf)." </a>";
       }
   };
   if($page != 0 ){
       echo "<a class=\"paginacaoNumber\" style=color:yellow;>$page</a>";
   };
   for($pag_sub = $page+1;$pag_sub <= $page + $links;$pag_sub++){
       if($pag_sub <= ceil(count($getAllPosts)/$limit)){
           echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_sub-1)*$limit).pages("page", $pag_sub)."\"> ".($pag_sub)." </a>";
       }
   }
   echo "<a class=\"paginacaoNumber\" href=\"".url("offset",ceil(count($getAllPosts)/$limit)*$limit/$limit).pages("page", ceil(count($getAllPosts)/$limit))."\"> ultima</a>";
   echo "</div>";
   echo "</footer>";
    echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
  ?>
</main>
<aside id=esquerda>
  <?php
    if($user['ativo']){
      echo "<div align=center class=background>";
        echo "<div>";
          echo "<p class=SeusAmigos>".($isOwner ? "Seus amigos" : "Amigos de $user[username]")."</p>";
        echo "</div>";
          if(count($amigosUser) > 0){
            foreach ($amigosUser as $amigo) {
              echo "<a href=navio.php?user=$amigo[amigoCod]><div><img src=$amigo[imgAmigo] class=div-amigo-image><p class=nomeAmigo>$amigo[nameAmigo]</p></div></a>";
            }
          } else {
            echo $isOwner ? "<p>Você ainda não tem nenhum amigo</p>":"<p>Sem amigos</p>";
          }          
          echo "<a class=portosAtracadosMais href=amigos.php?user=$_GET[user]>Ver mais</a>";
      echo "</div>";
    }
  ?>
</aside>
</div>
<!-- <footer>
          <<  
          >>
</footer>   -->
<script src="js/functions.js"></script>
<script>
const camera=document.getElementById("camera-icon");
const img_perfil=document.getElementById("img_perfil");
const file=document.getElementById("imgInp");
camera.addEventListener('click', () =>{
  file.click()
});
file.addEventListener('change', (event) =>{
  let reader = new FileReader();

  reader.onload = () => {
    //img_perfil.style.backgroundImage=`url(${reader.result})`;
    if(window.confirm("Você deseja alterar sua foto?")) formPhoto.submit();
  }
  reader.readAsDataURL(file.files[0])
})

</script>
</body>
</html>
