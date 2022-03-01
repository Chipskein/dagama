<!DOCTYPE html>
<html lang="pt-BR">
<?php
  $title="Navio";
  require 'components/head.php';
?>
<body class=perfil>
<?php
  /*
  if(isset($_POST['novoPost'])){
    $texto = ''.$_POST['texto'];
    $reacao = isset($_POST['reacao']) ? $_POST['reacao'] : 0;
    $isReaction = isset($_POST['reacao']) ? 1 : 0;
    $assuntos = [];
    $citacoes = [];
    
    // Local
    $local = $user['pais'];
    $codPais = $_POST['insert-codigo-pais'];
    $novoPaisNome = $_POST['insert-nome-pais'];
    $codEstado = $_POST['insert-codigo-estado'];
    $novoEstadoNome = $_POST['insert-nome-estado'];
    $codCidade = $_POST['insert-codigo-cidade'];
    $novoCidadeNome = $_POST['insert-nome-cidade'];
    $newAssuntos = [];
    for($c = 1; $c <= 5 ; $c++){
      if(isset($_POST['insert-new-assunto'.$c])){
        $newAssuntos[] = $_POST['insert-new-assunto'.$c];
      }
    }
    if(count($newAssuntos) > 0){
      foreach ($newAssuntos as $value) {
        $assuntos[] = AssuntoController::addAssunto($value);
      }
    }

    $qtdAssuntos = count(AssuntoController::getAssuntos());
    for($c = 1; $c <= $qtdAssuntos; $c++){
        if(isset($_POST["assunto$c"])){
            $assuntos[] = $_POST["assunto$c"];
        }
    }
    $qtdPessoas = count(UserController::getPessoas());
    for($c = 1; $c <= $qtdPessoas; $c++){
        if(isset($_POST["pessoa$c"])){
            $citacoes[] = $_POST["pessoa$c"];
        }
    }
    $response =PostController::addInteracao($_SESSION['userid'], $texto, $_GET['user'], 0, 0, 0, 0, $isReaction, $reacao, $local);
    if($response) {
      if(count($assuntos) > 0){
        foreach ($assuntos as $value) {
          PostController::addAssuntoInteracao($response, $value);
        }
      }
      if(count($citacoes) > 0){
        foreach ($citacoes as $value) {
          PostController::addCitacaoInteracao($value, $response);
        }
      }
      //header("refresh:0;url=navio.php?user=$_GET[user]"); 
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
      PostController::delInteracao($post);
      //header("refresh:0;url=navio.php?user=$_GET[user]"); 
    }
  }
  if(isset($_POST['removeCitacao'])){
    $post = $_POST['removeCitacao'];
    $user = $_SESSION['userid'];
    $erros = [];
    // Validação
    // ...
    if($erros == []){
      PostController::delCitacao($post, $user);
      //header("refresh:0;url=navio.php?user=$_GET[user]"); 
    }
  }
  
  if(isset($_FILES["photo"])){
    $photo=$_FILES["photo"];
    $oldphoto=$_SESSION['userimg'];//link
    $oldphotoid=substr($oldphoto,47);
    $newimg=UserController::updateImg($_SESSION['userid'],$photo,$oldphotoid);
    if($newimg){
      $_SESSION['userimg']="$newimg";
      //header("Refresh:0");
    }
  }
  */
?>
<div id=principal> 
  <?php
    require 'components/header.php';
    require 'components/portosAtracadosCard.php';
    require 'components/FriendsCard.php';
  ?>
  <main>
    <div align=center>
      <br>
      <?php
          require 'components/UserPhoto.php';
          require 'components/qt_friend_ports.php';
      ?>
    <br>
    <div class="container-center">
    <?php
      if(!$user['ativo']){
        echo "Este perfil está desativado";
      } 
      else {
        
        require "components/postTextField.php";
        require "components/PostContainer.php";
      }
    ?>
    </div>
<?php 
    $route="/navio/$user[codigo]";
    require 'components/FooterPage.php';
    echo "<script>img_perfil.style.backgroundImage=\"url($user[img])\"</script>";
  ?>
</main>
</div>

<script src="/js/functions.js"></script>
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
