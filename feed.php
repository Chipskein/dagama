<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Feed</title>
</head>
<body>
<?php
  include './backend/infra/connection.php';
  session_start();
  if(isset($_SESSION['userid'])){
    $user = getUserInfo("$_SESSION[userid]");
    $limit = 10;
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $feedArray = getFeed($offset,$limit);
    $locaisArray = getLocais();
    $assuntosArray = getAssuntos();
    $pessoasArray = getPessoas();
    $paises=getPaises();
    $estados=getStates();
    $cidades=getCities();
    echo "<script>";
    echo "let states=[];";
    echo "let cities=[];";
    echo "let paises=[];";
    foreach($paises as $pais){
      echo "pais={";
      echo "codigo:$pais[codigo],";
      echo "nome:\"$pais[nome]\"";
      echo "};";
      echo "paises.push(pais);";
    }
    foreach($estados as $estado){
      echo "estado={";
      echo "pais:$estado[pais],";
      echo "codigo:$estado[codigo],";
      echo "nome:\"$estado[nome]\"";
      echo "};";
      echo "states.push(estado);";
    }
    foreach($cidades as $cidade){
      echo "cidade={";
      echo "uf:$cidade[uf],";
      echo "codigo:$cidade[codigo],";
      echo "nome:\"$cidade[nome]\"";
      echo "};";
      echo "cities.push(cidade);";
    }
    echo "estado=null;";
    echo "cidade=null;";
  echo "</script>";
    $suggestFriends = suggestFriends($_SESSION['userid'], 4, 0);
    $postsArray = getPosts($_SESSION['userid'], 0, 30);
    $portosArray = getAllPorto($_SESSION['userid'], true, 0, 3);
    $errorMessage = [];
    if(isset($_POST['novoPost'])){
      // var_dump($_POST);
      $texto = ''.$_POST['texto'];
      $local = isset($_POST['local']) ? $_POST['local'] : 0;
      $assuntos = [];
      $citacoes = [];
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
      $response = addInteracao($_SESSION['userid'], $texto, 0, 0, 0, 0, 0, 0, $local);
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

    // var_dump($_POST);
    // echo "<br>";
    // print_r($locaisArray);
    // echo "<br>";
    // print_r($assuntosArray);
    // echo "<br>";
    // print_r($pessoasArray);
    // echo "<br>";
    // print_r($postsArray);
    // echo "<br>";
    // print_r($portosArray);
    // echo "<br>";

    // sendFriendRequest para enviar solicitacao
    if(isset($_POST['sendFriendRequest'])){
      $erros = [];
      if(!preg_match('#^[0-9]+$#', $_POST['sendFriendRequest'])){
        $erros[] = "A pessoa precisa ser um número";
      } else {
        $response = getRequestAndFriends($user['codigo'], true);
        for($c = 0; $c < count($response); $c++) {
          if($response[$c]['amigo'] == $_POST['sendFriendRequest'] && $response[$c]['ativo'] == 1){
            $erros[] = "Solicitação já enviada";
          }
          if(($response[$c]['otherPerfil'] != $user['codigo'] ? $response[$c]['otherPerfil'] : $response[$c]['otherAmigo']) == $_POST['sendFriendRequest'] && $response[$c]['otherAtivo'] == 1){
            $erros[] = "Você já é amigo deste usuário";
          }
        }
      }
      if($erros == []){
        sendFriendRequest($user['codigo'], $_POST['sendFriendRequest']);
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
      } else {
        $errorMessage['friendRequest'] = ['sendFriendRequest', $_POST['sendFriendRequest'], implode(', ', $erros)];
      }
    } 
?>
<div id=principal>
  <header class="header-main">
    <img class="header-icon" src="imgs/icon.png" alt="">
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a a-selected\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <aside id=direita>
    <div align=center class=background2>
      <p class=portosAtracados>Portos atracados:</p>
      <?php
      if($portosArray){
        foreach ($portosArray as $value) {
          echo "<div class=\"row porto-feed-container\">
            <div class=\"portos-img\" style=\"background-image: url($value[img])\"></div>
            <a class=nomePort href=porto.php?porto=$value[codigo]>$value[nome]</a>
          </div>";
        }
        echo "<br><a class=portosAtracadosMais href=portosUser.php>Ver todos</a>";
      } else {
        echo "<p>Você não está em nenhum porto ainda</p>";
      }
      ?>
    </div>
  </aside>
  <aside id=esquerda>
  <div align=center class=background2>
        <p class=portosAtracados>Ondas do momento:</p>
      <div align=start>
        <p class=trending>1ª Elon musk</p>
        <p class=trending>1ª Elon musk</p>
        <p class=trending>1ª Elon musk</p>
      </div>
    </div>
  </aside>
  <main class="container-center">

<?php
    echo array_key_exists('friendRequest',$errorMessage) ? $errorMessage['friendRequest'][2] : '';
    // modal pros erros
    // echo "<div id=\"abrirModal\" class=\"modal\">";
    // echo "<div onclick=\"closeModal('abrirModal')\" class=\"fechar\">x</div>";
    // echo "<h2>".$errorMessage['friendRequest']."</h2>";
    // echo "<p>Err message: ".$errorMessage['friendRequest'][2]."</p>";
    // echo "</div>";

    // initial insert post
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
        echo "</div>";
        echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"novoPost\" />";
        echo "<hr id=\"post-hr\" class=\"post-hr\" >";
        echo "<div class=\"post-divLocal\">";
          echo "<select id=\"select-local\" onclick=\"unsetError(this)\">";
            foreach ($locaisArray as $value) {
              echo "<option id='optionLocal".$value['codCidade']."' value='{ \"id\": \"".$value['codCidade']."\", \"name\": \"".$value['nomeCidade']."\" }'\">".$value['nomeCidade']."</option>\n";
            }
            echo "<option value=\"0\">Outro</option>";
          echo "</select>";
          echo "<input id=\"value-localId\" name=\"select-local-value\" class=hidden>";
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
      echo "</form>";
    echo "</div>";

    // add friends FIXME:
    if(count($suggestFriends) > 0){
      echo "<div class=\"add-amigo\">";
      echo "<div class=\"add-amigo-top\">";
      echo "<p class=\"add-amigo-suggesTxt\">Sugestão de amigos:</p>";
      echo "<p class=\"add-amigo-verMais\">VER MAIS</p>";
      echo "</div>";
      echo "<div class=\"add-amigo-cards\">";
      foreach ($suggestFriends as $person) {
        echo "<div id=\"card".$person['codigo']."\" class=\"add-amigo-card\">";
        echo "<a href=navio.php?user=$person[codigo]><img class=\"add-amigo-card-icon\" src=\"".$person['img']."\" alt=\"\" srcset=\"\"></a>";
        echo "<p class=\"add-amigo-card-name\">".$person['username']."</p>";
        echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\" >";
        if($person['enviado'] == 'true' || (isset($_POST['sendFriendRequest']) && $_POST['sendFriendRequest'] == $person['codigo'])){
          echo "<input type=\"hidden\" name=\"unsendFriendRequest\" value=\"".$person['codigo']."\" />";
          echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button-selected\" disabled  type=\"submit\" value=\"Enviado\" />";  
        }
        if($person['recebido'] != 'true' && $person['enviado'] != 'true' && (isset($_POST['sendFriendRequest']) ? $_POST['sendFriendRequest'] != $person['codigo'] : true)) {
          echo "<input type=\"hidden\" name=\"sendFriendRequest\" value=\"".$person['codigo']."\" />";
          echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button\"  type=\"submit\" onclick=\"
            let cardInput = document.getElementById('cardInput'+".$person['codigo'].");
            cardInput.className = 'add-amigo-card-button-selected'; cardInput.value = 'Enviado'";
          echo "\" value=\"Adicionar\" />";
        }
        echo "</form>";
        echo "</div>";        
      }
      echo "</div>";
      echo "</div>";
    }

    // posts
    if($postsArray){
      foreach ($postsArray as $post) {
        echo "<div class=\"div-post\">";
          if($post['codPorto']){
            echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=porto.php?porto=$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
          }
          //Share
          if($post['isSharing']){
            $sharedPost = getOriginalPost($post['codPost']);
            echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
            echo "<div class=\"div-sharing-post\">";
              // Sharing-top
              echo "<div class=\"div-sharing-post-top\">";
                echo "<a href=navio.php?user=$sharedPost[codPerfil]><img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\"></a>";
                echo "<div class=\"div-post-top-infos\">";
                  echo "<p class=\"div-post-top-username\"><i>@".$sharedPost['nomePerfil']."</i>";
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
              echo "<p class=\"div-post-top-username\"><i>@".$post['nomePerfil']."</i>";
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
            if(count($post['citacoes']) > 0) {
              $tmpCitacoes = [];
              foreach ($post['citacoes'] as $pessoa) {
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
            echo "$post[textoPost]</p>";
          echo "</div>";
          //Ícones
          echo "<div class=\"div-post-icons-bar\">";
            // echo "<div class=\"div-post-icons-bar-divs\">";
            //   echo "<p>12</p><img src=\"imgs/icons/Like.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            // echo "</div>";
            echo "<div class=\"div-post-icons-bar-divs\">";
              echo "<p>5</p><img src=\"imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            echo "</div>";
            // echo "<div class=\"div-post-icons-bar-divs\">";
            //   echo "<p>2</p><img src=\"imgs/icons/send.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
            // echo "</div>";
            echo "<div class=\"div-post-icons-bar-interagir\">";
              echo "<a href=navio.php?user=$user[codigo]><img src=\"$user[img]\" class=\"div-post-icons-bar-interagir-icon\" alt=\"\"></a><p>Interagir...</p>";
            echo "</div>";
          echo "</div>";
          echo "<br><br>";
          //Comentários
          if($post['comentarios'] && $post['comentarios'] != []){
            echo "<hr class=\"post-hr\">";
            foreach ($post['comentarios'] as $elem) {
              echo "<div class=\"comment-container\">";
                echo "<div class=\"comment-container-top\">";
                  echo "<a href=navio.php?user=$elem[codPerfil]><img src=\"".$elem['iconPerfil']."\" alt=\"\" class=\"comment-icon\"></a>";
                  echo "<p class=\"comment-txt\"><i>@".$elem['nomePerfil']."</i> ";
                  echo ($elem['textoPost'] ? $elem['textoPost'] : '');
                  echo ", em ".$elem['dataPost'];
                  echo "</p>";
                echo "</div>";
                echo "<div class=\"comment-reagir\"><p>Reagir</p></div>";
              echo "</div>";
            }
          }
        echo "</div>";
      }
    }
    echo "</main>";    
  }
  else {
    echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
    header("refresh:1;url=index.php");
    die();
  }
  ?>
  </div>
  <footer>
          <<  
          <?php
            //provisório
            for ($page = 0; $page < ceil((count($portosArray))/$limit); $page++) {
              echo (($offset == $page*$limit) ? ($page+1) : "<a class=page-link href=\"".url("offset", $page*$limit)."\">".($page+1)."</a>")." \n";
            }
          ?>
          >>
</footer>  
<!-- <div onclick="openModal('abrirModal')" ><p>Open Modal</p></div> -->
<script src="functions.js">
</script>
<script>
var tmpLocal = 0;
function addLocal(){
    var local = document.getElementById('select-local').value;
    local = JSON.parse(local);
    var div = document.getElementById('divCidade');
    if(local !== 0){
    var option = document.getElementById('optionLocal'+local.id);
    option.remove();
    tmpLocal = local.id;
    const p = document.createElement('p')
    p.id='local'+local.id
    p.innerHTML += `${local.name} <button type="button" onclick="removeLocal('${local.id}', '${local.name}')">❌</button>`;
    div.append(p)
    }else{
      const buttonAddCidade = document.createElement('button')
      buttonAddCidade.textContent='adicioar cidade';
      buttonAddCidade.id = 'buttonCidade';
      buttonAddCidade.type = 'button';
      // button.onclick = () => {  }
      const inputCidade = document.createElement('input')
    inputCidade.id='InputCidade'
    inputCidade.className='StylesInputs'
    inputCidade.placeholder='cidade'
    const inputEstado = document.createElement('input')
    inputEstado.id='InputEstado'
    inputEstado.className='StylesInputs'
    inputEstado.placeholder='estado'
    const selectPais = document.createElement('select')
    selectPais.id='Inputpais'
    selectPais.className='StylesInputs'
    for(c=0;c<paises.length;c++){
      const options = document.createElement('option')
      options.value = paises[c].codigo
      options.innerHTML = paises[c].nome
      selectPais.append(options)
    }
    div.append(inputCidade)
    div.append(inputEstado)
    div.append(selectPais)
    div.append(buttonAddCidade)
    }
    document.getElementById('select-local').disabled = true;
    document.getElementById('select-local-button').disabled = true;
}
function removeLocal(id, name){
    var div = document.getElementById('divCidade');
    var p = document.getElementById('local'+id);
    var select = document.getElementById('select-local');
    select.innerHTML += `<option id='optionLocal${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    tmpLocal = 0;
    document.getElementById('select-local').disabled = false;
    document.getElementById('select-local-button').disabled = false;
}

var pessoas = [];
function addPessoas(){
    var pessoa = document.getElementById('select-pessoas').value;
    pessoa = JSON.parse(pessoa);
    var div = document.getElementById('divPessoas');
    var option = document.getElementById('optionPessoa'+pessoa.id);
    option.remove();
    pessoas.push(pessoa.id);
    const p = document.createElement('p')
    p.id='pessoas'+pessoa.id
    p.innerHTML += `${pessoa.name} <button type="button" onclick="removePessoas('${pessoa.id}', '${pessoa.name}')">❌</button>`;
    div.append(p)
}
function removePessoas(id, name){
    var div = document.getElementById('divPessoas');
    var p = document.getElementById('pessoas'+id);
    var select = document.getElementById('select-pessoas');
    select.innerHTML += `<option id='optionPessoa${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    for(var i = 0; i < pessoas.length; i++){ 
        if ( pessoas[i] == id) {
            pessoas.splice(i, 1); 
        }    
    }
}

var assuntos = [];
function addAssuntos(){
    var assunto = document.getElementById('select-assuntos').value;
    assunto = JSON.parse(assunto);
    var div = document.getElementById('divAssuntos');
    if(assunto !== 0){
    var option = document.getElementById('optionAssunto'+assunto.id);
    option.remove();
    assuntos.push(assunto.id);
    const p = document.createElement('p')
    p.id='assunto'+assunto.id
    p.innerHTML += `${assunto.name} <button type="button" onclick="removeAssuntos('${assunto.id}', '${assunto.name}')">❌</button>`;
    div.append(p)
    } else{
      const buttonAddAssuntos = document.createElement('button')
      buttonAddAssuntos.textContent='adicioar assunto';
      buttonAddAssuntos.id = 'buttonAssunto';
      buttonAddAssuntos.type = 'button';
      // button.onclick = () => {  }
      const inputAssunto = document.createElement('input')
    inputAssunto.id='InputCidade'
    inputAssunto.className='StylesInputs'
    inputAssunto.placeholder='adicione o assunto'
    div.append(inputAssunto)
    div.append(buttonAddAssuntos)
    document.getElementById('select-assunto').disabled = true;
    document.getElementById('select-assunto-button').disabled = true;
    }
  }
function removeAssuntos(id, name){
    var div = document.getElementById('divAssuntos');
    var p = document.getElementById('assunto'+id);
    var select = document.getElementById('select-assuntos');
    select.innerHTML += `<option id='optionAssunto${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    for(var i = 0; i < assuntos.length; i++){ 
        if ( assuntos[i] == id) {
            assuntos.splice(i, 1); 
        }    
    }
}
function unsetError(){
  console.log('rosca direta')
}

</script>
</body>
</html>
