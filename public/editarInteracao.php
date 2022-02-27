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
  include '../backend/infra/services.php';
  if(!isset($_SESSION)) { 
    session_start(); 
  }
  if(isset($_SESSION['userid'])){
    // var_dump($_POST);
    $user = getUserInfo("$_SESSION[userid]");
    $post = [];//getOriginalPost($_GET['interacao']);
    $originalPost = [];
    if($post['isSharing']){
      $originalPost = [];//getOriginalPost($post['codPost']);
    }
    $postPai = $post['postPai'] ? $post['postPai'] : $_GET['interacao'];
    $locaisArray = [];
    $assuntosArray = getAssuntos();
    $pessoasArray = getPessoas();
    $paises=getPaises();
    $estados=[];
    $cidades=[];
    $errorMessage = [];
    if(isset($_POST['editarPost'])){
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
      $response = ediInteracao($post['codInteracao'], $texto, $isReaction, $reacao, $local);
      if($response) {
        $citacoesAntigas = $post['citacoes'];
        foreach ($citacoesAntigas as $value) {
          delCitacao($post['codInteracao'], $value['codPerfil']);
        }
        $assuntosAntigos = $post['assuntos'];
        foreach ($assuntosAntigos as $value) {
          delAssuntoInteracao($post['codInteracao'], $value['codAssunto']);
        }
        if(count($assuntos) > 0){
          foreach ($assuntos as $value) {
            addAssuntoInteracao($response, $value);
          }
        }
        if(count($citacoes) > 0){
          foreach ($citacoes as $value) {
            addCitacaoInteracao($value, $post['codInteracao']);
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
      echo "<a class=\"header-links-a\" href=../backend/logoff.php>Sair </a><img class=\"header-links-icon\" src=\"imgs/icons/sair.png\" alt=\"\">";
    ?>
    </div>
  </header>
  <?php
    echo "<main class=\"container-center\">";
      // initial insert post
      echo "<div class=\"insert-interacao\">";
        echo "<div class=\"insert-interacao-user\">";
          echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
          echo "<div>";
            echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
            echo "<p class=\"insert-interacao-user-assuntos\"></p>";
          echo "</div>";
        echo "</div>";
        echo "<form name=\"newPost\" action=\"editarInteracao.php?interacao=$_GET[interacao]\" method=\"post\" >";
          echo "<textarea name=\"texto\" class=\"insert-interacao-input\" id=\"insert-interacao-input\" type=\"text\" >$post[textoPost]</textarea>";
          echo "<div class=\"insert-interacao-smallBtns\">";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
            echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('reacoes')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/Like.png\" alt=\"\" srcset=\"\">Reação</div>";
            // echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('compartilhar')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"imgs/icons/send.png\" alt=\"\" srcset=\"\">Compartilhar</div>";
          echo "</div>";
          echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"editarPost\" />";
          echo "<hr id=\"post-hr\" class=\"post-hr\" >";
          
          // Local
          echo "<div class=\"post-divLocal\">";

            echo "<input id=\"insert-codigo-pais\" name=\"insert-codigo-pais\" type=\"hidden\" value=\"$post[codPais]\">";
            echo "<input id=\"insert-codigo-estado\" name=\"insert-codigo-estado\" type=\"hidden\" value=\"$post[codUF]\">";
            echo "<input id=\"insert-codigo-cidade\" name=\"insert-codigo-cidade\" type=\"hidden\" value=\"$post[codCidade]\">";
          
            echo "<select id=\"select-pais\" onchange=\"selectPais(this)\" class=hidden >";
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
                  if($value2['uf'] == $value['codigo'] && $value2['codigo'] != $post['codCidade']){
                    echo "<option id='optionCidade$value2[codigo]' value='{ \"id\": \"".$value2['codigo']."\", \"name\": \"".$value2['nome']."\" }'>$value2[nome]</option>\n";
                  }
                }
                echo "<option value='{ \"id\": \"0\", \"name\": \"null\" }'>Outro</option>";
              echo "</select>";
            }
            echo "<input id=\"insert-nome-pais\" name=\"insert-nome-pais\" placeholder=\"Digite o nome do novo pais\" class=hidden>";
            echo "<input id=\"insert-nome-estado\" name=\"insert-nome-estado\" placeholder=\"Digite o nome do novo estado\" class=hidden>";
            echo "<input id=\"insert-nome-cidade\" name=\"insert-nome-cidade\" placeholder=\"Digite o nome da nova cidade \" class=hidden>";
            echo "<button id=\"select-local-button\"  class=\"confirm-type hidden\" type=\"button\" onclick=\"addLocal()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divCidade\">";
            echo "<p id=\"cidade$post[codCidade]\">$post[nomeCidade] <button type=\"button\" onclick=\"removeLocal('$post[codCidade]', '$post[nomeCidade]')\">❌</button></p>";
            echo "<input type=\"hidden\" id=\"cidadeInput$post[codCidade]\" name=\"cidade\" value=\"$post[codCidade]\">";
            echo "</div>";
          echo "</div>";
          
          // Citações
          echo "<div class=\"post-divPessoas\">";
            $tmp1 = [];
            foreach ($post['citacoes'] as $pessoa){
              $tmp1[] = $pessoa['codPerfil'];
            }
            echo "<select id=\"select-pessoas\" onclick=\"unsetError(this)\">";
              foreach ($pessoasArray as $value) {
                if(!in_array($value['codigo'], $tmp1)){
                  echo "<option id='optionPessoa".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['username']."\" }'\">".$value['username']."</option>\n";
                }
              }
            echo "</select>";
            echo "<button id=\"select-pessoa-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addPessoas()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divPessoas\">";
            foreach ($post['citacoes'] as $pessoa){
                echo "<p id=\"pessoas$pessoa[codPerfil]\">$pessoa[nomePerfil] <button type=\"button\" onclick=\"removePessoas('$pessoa[codPerfil]', '$pessoa[nomePerfil]')\">❌</button></p>";
                echo "<input type=\"hidden\" id=\"pessoaInput$pessoa[codPerfil]\" name=\"pessoa$pessoa[codPerfil]\" value=\"$pessoa[codPerfil]\">";
              }
            echo "</div>";
          echo "</div>";
          
          // Assuntos
          echo "<div class=\"post-divAssuntos\">";
              $tmp2 = [];
              foreach ($post['assuntos'] as $assunto) {
                $tmp2[] = $assunto['codAssunto'];
              }
            echo "<select id=\"select-assuntos\" onchange=\"selectAssunto(this)\">";
              foreach ($assuntosArray as $value) {
                if(!in_array($value['codigo'], $tmp2)){
                  echo "<option id='optionAssunto".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['nome']."\" }'\">".$value['nome']."</option>\n";
                }
              }
              echo "<option value=\"0\">Outro</option>";
            echo "</select>";
            echo "<div id=\"divNewAssuntos\"></div>";
            echo "<input id=\"insert-nome-assunto\" placeholder=\"Digite o nome do novo assunto\" class=hidden>";
            echo "<button id=\"select-assunto-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addAssuntos()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divAssuntos\">";
              foreach ($post['assuntos'] as $assunto) {
                echo "<p id=\"assunto$assunto[codAssunto]\">$assunto[nomeAssunto] <button type=\"button\" onclick=\"removeAssuntos('$assunto[codAssunto]', '$assunto[nomeAssunto]')\">❌</button></p>";
                echo "<input type=\"hidden\" id=\"assuntoInput$assunto[codAssunto]\" name=\"assunto$assunto[codAssunto]\" value=\"$assunto[codAssunto]\">";
              }
            echo "</div>";
          echo "</div>";
          
          // Reações
          echo "<div class=\"post-divReacoes\">";
            echo "<select id=\"select-reacoes\" onclick=\"unsetError(this)\" ".($post['isReaction'] ? "class=hidden" : "").">";
              $reacoesArray = [['codigo'=>'curtir', 'emoji'=>'👌'],['codigo'=>'kkk', 'emoji'=> '🤣'],['codigo'=>'amei', 'emoji'=> '❤️'],['codigo'=>'grr', 'emoji'=> '🤬'],['codigo'=>'wow', 'emoji'=> '🤯'],['codigo'=>'sad', 'emoji'=> '😭']];
              foreach ($reacoesArray as $value) {
                echo "<option id='optionReacao".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['emoji']."\" }'\">$value[emoji] $value[codigo]</option>\n";
              }
            echo "</select>";
            echo "<button id=\"select-reacao-button\"  class=\"confirm-type".($post['isReaction'] ? " hidden" : "")."\"  type=\"button\" onclick=\"addReacoes()\">Confirmar</button>";
            echo "<div class=\"comment-container-top\" id=\"divReacoes\">";
              if($post['isReaction']){
                $emojis = ['curtir'=>'👌','kkk'=> '🤣','amei'=>'❤️','grr'=>'🤬','wow'=>'🤯','sad'=> '😭'];
                echo "<p id=\"reacao$post[emote]\">".$emojis[$post['emote']]." $post[emote] <button type=\"button\" onclick=\"removeReacoes('$post[emote]', '".$emojis[$post['emote']]."')\">❌</button></p>";
                echo "<input type=\"hidden\" id=\"reacaoInput$post[emote]\" name=\"reacao\" value=\"$post[emote]\">";
              }
            echo "</div>";
          echo "</div>";
          
          // Compartilhar
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
    echo "</main>"; 
    }   
    else {
      echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
      header("refresh:1;url=index.php");
      die();
    }
  ?>

<script src='./js/functions.js'>
    
</script>
</body>
</html>