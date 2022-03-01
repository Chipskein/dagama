<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title="Feed";
  require 'components/head.php';
?>
<body>
<?php
    /*
    if(isset($_POST['buttonAssunto'])){
      $addAssunto = addAssunto("$_POST[buttonAssunto]");
      header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
    };
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
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
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
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
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
        header("refresh:0;url=feed.php?user=$_SESSION[userid]"); 
      }
    }   
    if(isset($_POST['sendFriendRequest'])){
      $erros = [];
      if(!preg_match('#^[0-9]+$#', $_POST['sendFriendRequest'])){
        $erros[] = "A pessoa precisa ser um número";
      } else {
        $response = [];//getRequestAndFriends($user['codigo'], true);
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
    }*/
  
?>
<div id=principal>
  <?php
    require 'components/header.php';
    require 'components/portosAtracadosCard.php';
    require 'components/OndasDoMomentoCard.php';
  ?>
<main class="container-center">

<?php
    echo array_key_exists('friendRequest',$errorMessage) ? $errorMessage['friendRequest'][2] : '';
    // initial insert post
    require 'components/postTextField.php';
    // friends suggestion
    require 'components/friendssuggestion.php';
    // posts
    require 'components/PostContainer.php';
    $route="/feed";
    //footer
    require 'components/FooterPage.php';
?>
  </div>
</main>
<script src="js/functions.js">
</script>
</body>
</html>
