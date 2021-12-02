<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../imgs/icon.png" type="image/jpg">
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../responsive.css" media="screen and (max-width: 1680px)"/>
  <title>Dagama | Navio</title>
</head>
<body class=perfil>
<?php
  include './backend/infra/connection.php';
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
    if(isset($_GET['user'])){
        $postsArray = getPostsOnUser($_GET['user'], 0, 10);
        $user=getUserInfo("$_GET[user]");
        if(!$user){
          echo "Usuario inválido";
          header("refresh:1;url=mar.php");
          die();
        }
        $isOwner= "$_GET[user]"=="$_SESSION[userid]" ? true:false;
        $portosArray = getAllPorto($_GET['user'], true, 0, 3);
        $portosUser = getUserPortoQtd($_GET['user']);
        $amigosUser = getFriends($_GET['user'], 0, 3);
        $locaisArray = getLocais();
        $assuntosArray = getAssuntos();
        $pessoasArray = getPessoas();
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
          $response = addInteracao($_SESSION['userid'], $texto, $_GET['user'], 0, 0, 0, 0, 0, $local);
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
      }
      else{
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
    <div class="header-searchBar">
      <img class="header-searchBar-icon" src="imgs/icons/search.png" alt="" srcset="">
      <input class="header-searchBar-input" type="text" placeholder="Faça sua pesquisa ..." />
    </div>
    <div class="header-links">
    <?php 
      echo "<a class=\"header-links-a\" href=feed.php>Feed</a> ";
      echo "<a class=\"header-links-a\" href=mar.php>Mar</a> ";
      echo "<a class=\"header-links-a a-selected\" href=navio.php?user=$_SESSION[userid]>Navio</a> ";
      echo "<a class=\"header-links-a\" href=backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <aside id=direita>
    <div align=center class=background>
      <p class=portosAtracados>Portos atracados:</p>
      <?php
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
      ?>
    </div>
  </aside>
  <main>
    <div align=center>
    <!--Add onlick change-->
      <br>
      <div id="img_perfil" class=perfil></div>
      <form id=formPhoto action=<?php echo "navio.php?user=$_SESSION[userid]"?> enctype=multipart/form-data method="POST">
          <input id="imgInp" class="hidden" type="file" name="photo">
      </form>
      <?php 
        if($isOwner)echo "<div id=camera-icon></div>";
      ?>
      <?php echo "<h3 class=perfil>$user[username]</h3>";?>
    </div>
    <br>
    <div align=center>
        <div class=perfil-amigos>
          <?php
            echo "<a href=amigos.php?user=$_GET[user] class=amigos> Amigos: ".($amigosUser ? $amigosUser[0]['qtdAmigos'] : 0)."</a>";
            if($isOwner) echo "<h3><a href=portosUser.php?owner class=amigos >Seus Portos: $portosUser</a></h3>";
          ?>
        </div>
    </div>
    <br>
    <div class="container-center">
    <?php
      echo "<div class=\"center\">";
        // initial insert post
        echo "<div class=\"insert-interacao\">";
          echo "<div class=\"insert-interacao-user\">";
            echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
            echo "<div>";
              echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
              echo "<p class=\"insert-interacao-user-assuntos\"></p>";
            echo "</div>";
          echo "</div>";
          echo "<form name=\"newPost\" action=\"navio.php?user=$_GET[user]\" method=\"post\" >";
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
              echo "<table id=\"tableLocal\" border=\"1\" name=\"tableLocal\"></table>";
            echo "</div>";
            echo "<div class=\"post-divPessoas\">";
              echo "<select id=\"select-pessoas\" onclick=\"unsetError(this)\">";
                foreach ($pessoasArray as $value) {
                  echo "<option id='optionPessoa".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['username']."\" }'\">".$value['username']."</option>\n";
                }
              echo "</select>";
              echo "<button id=\"select-pessoa-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addPessoas()\">Confirmar</button>";
              echo "<table id=\"tablePessoas\" border=\"1\" name=\"tablePessoas\"></table>";
            echo "</div>";
            echo "<div class=\"post-divAssuntos\">";
              echo "<select id=\"select-assuntos\" onclick=\"unsetError(this)\">";
                foreach ($assuntosArray as $value) {
                  echo "<option id='optionAssunto".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['nome']."\" }'\">".$value['nome']."</option>\n";
                }
                echo "<option value=\"0\">Outro</option>";
              echo "</select>";
              echo "<button id=\"select-assunto-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addAssuntos()\">Confirmar</button>";
              echo "<table id=\"tableAssuntos\" border=\"1\" name=\"tableAssuntos\"></table>";
            echo "</div>";
          echo "</form>";
        echo "</div>";
      echo "</div>";

    // posts
    if($postsArray){
      foreach ($postsArray as $post) {
        echo "<div class=\"div-post\">";
          // if($post['codPorto']){
          //   echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=porto.php?porto=$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
          // }
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
    ?>
    </div>
</main>
<aside id=esquerda>
    <div align=center class=background>
      <div>
        <p class=SeusAmigos> <?php echo $isOwner ? "Seus amigos":"Amigos de $user[username]" ?> </p>
      </div>
      <?php
        if(count($amigosUser) > 0){
          foreach ($amigosUser as $amigo) {
            echo "<a href=navio.php?user=$amigo[amigoCod]><div><img src=$amigo[imgAmigo] class=div-amigo-image><p class=nomeAmigo>$amigo[nameAmigo]</p></div></a>";
          }
        } else {
          echo $isOwner ? "<p>Você ainda não tem nenhum amigo</p>":"<p>Sem amigos</p>";
        }
        
        echo "<a class=portosAtracadosMais href=amigos.php?user=$_GET[user]>Ver mais</a>"
      ?>
    </div>
</aside>
<?php 
  echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
?>
</div>
<!-- <footer>
          <<  
          >>
</footer>   -->
<script src="functions.js"></script>
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

var tmpLocal = 0;
function addLocal(){
    var local = document.getElementById('select-local').value;
    local = JSON.parse(local);
    var table = document.getElementById('tableLocal');
    var option = document.getElementById('optionLocal'+local.id);
    option.remove();
    tmpLocal = local.id;
    table.innerHTML += `<tr id="row${local.id}"><td>${local.name}<input type="hidden" value="${local.id}" name="local" /></td><td><button onclick="removeLocal('${local.id}', '${local.name}')">❌</button></td></tr>`;
    document.getElementById('select-local').disabled = true;
    document.getElementById('select-local-button').disabled = true;
}
function removeLocal(id, name){
    var table = document.getElementById('tableLocal');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-local');
    select.innerHTML += `<option id='optionLocal${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
    tmpLocal = 0;
    document.getElementById('select-local').disabled = false;
    document.getElementById('select-local-button').disabled = false;
}

var pessoas = [];
function addPessoas(){
    var pessoa = document.getElementById('select-pessoas').value;
    pessoa = JSON.parse(pessoa);
    var table = document.getElementById('tablePessoas');
    var option = document.getElementById('optionPessoa'+pessoa.id);
    option.remove();
    pessoas.push(pessoa.id);
    table.innerHTML += `<tr id="row${pessoa.id}"><td>${pessoa.name}<input type="hidden" value="${pessoa.id}" name="pessoa${pessoa.id}" /></td><td><button onclick="removePessoas('${pessoa.id}', '${pessoa.name}')">❌</button></td></tr>`;
}
function removePessoas(id, name){
    var table = document.getElementById('tablePessoas');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-pessoas');
    select.innerHTML += `<option id='optionPessoa${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
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
    var table = document.getElementById('tableAssuntos');
    var option = document.getElementById('optionAssunto'+assunto.id);
    option.remove();
    assuntos.push(assunto.id);
    table.innerHTML += `<tr id="row${assunto.id}"><td>${assunto.name}<input type="hidden" value="${assunto.id}" name="assunto${assunto.id}" /></td><td><button onclick="removeAssuntos('${assunto.id}', '${assunto.name}')">❌</button></td></tr>`;
}
function removeAssuntos(id, name){
    var table = document.getElementById('tableAssuntos');
    var row = document.getElementById('row'+id);
    var select = document.getElementById('select-assuntos');
    select.innerHTML += `<option id='optionAssunto${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    row.remove();
    for(var i = 0; i < assuntos.length; i++){ 
        if ( assuntos[i] == id) {
            assuntos.splice(i, 1); 
        }    
    }
}
</script>
</body>
</html>
