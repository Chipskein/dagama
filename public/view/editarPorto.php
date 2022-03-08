
<!DOCTYPE html>
<html lang="pt-br">
<?php
  $title="Editar Porto";
  require 'components/head.php';
?>
<body>
<?php
 
  if(isset($_SESSION['userid'])){
    if((isset($_POST["porto"])&&isset($_POST["oldimg"])&&isset($_POST["oldnome"])&&isset($_POST["olddescr"])&&isset($_POST['owner']))||isset($_POST['confirmar'])){
        if(isset($_POST["porto"])&&isset($_POST["oldimg"])&&isset($_POST["oldnome"])&&isset($_POST["olddescr"])&&isset($_POST['owner'])){
            if($_POST['owner']!=$_SESSION['userid']) header("Location:/mar");
            echo "<script>let oldlink='$_POST[oldimg]';let oldname='$_POST[oldnome]';let olddescr='$_POST[olddescr]';</script>";
        }
        // var_dump($_POST);
        // var_dump($_FILES);
        if(isset($_POST['confirmar'])){
            $erros = [];
            if(!isset($_POST['nome']) || !isset($_POST['descricao'])){
                $erros[] = "Campos faltando";
            } else {
                $regex = "/^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ,.\-_:]+)?)+$/";
                if(!preg_match($regex, $_POST['descricao'])){
                $erros[] = "Descricão inválida = ".preg_match($regex, $_POST['descricao']);
                }
                if(strlen($_POST['descricao']) > 250){
                $erros[] = "Descrição grande demais";
                }
                if(!preg_match($regex, $_POST['nome'])){
                $erros[] = "Nome inválido = ".preg_match($regex, $_POST['nome']);
                }
                if(strlen($_POST['nome']) > 250){
                $erros[] = "Nome grande demais";
                }
                if(trim($_POST['nome']) == ""){
                $erros[] = "Nome não pode ser em branco";
                }
                if(isset($_FILES['photo'])){
                if($_FILES['photo']['name'] != ""){
                    if($_FILES['photo']['type'] == ""){
                    $erros[] = "Tipo da imagem inválido";
                    }
                    if($_FILES['photo']['size'] == ""){
                    $erros[] = "Tamanho da imagem inválido";
                    }
                    if($_FILES['photo']['error'] !== 0 ){
                    $erros[] = "Ocorreu um erro ao fazer upload dessa imagem";
                    }
                }
                }
            }

            if($erros == []){
                $porto = $_POST['porto'];
                $nome = $_POST['nome'];
                $descr = $_POST['descricao'];
                $img = is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo'] : null;
                $oldphotoid= isset($_POST['oldimglink']) ? substr("$_POST[oldimglink]",47):null;;
                $id=false;
                $id = PortoController::editarPorto($porto, $nome, $descr, $img,$oldphotoid);
                if($id) {
                  header("Location:/porto/$porto");
                  die();
                } 
                else {
                  echo "Erro no update!";
                }
            } else {
                echo "Erro: ".implode(', ',$erros);
            }
        }
    }
  }
 
?>
  <?php
    require 'components/header.php';
  ?>
  <main class="container-center">
    <div class="addporto-form-container">
      <form action="/editarPorto" method="post" id="formAddPorto" name="formAddPorto" enctype="multipart/form-data">
        <?php
          if(isset($_POST['oldimg'])){
            if(preg_match("/drive.google.com/","$_POST[oldimg]")) echo "<input type=\"hidden\" name=\"oldimglink\" value=\"$_POST[oldimg]\"/>";
          }
          echo "<input type=\"hidden\" name=\"porto\" value=\"$_POST[porto]\"/>";
        ?>
        <div id="porto_img_banner" class="addporto-img"></div>
        <input id="imgInp" type="file"  name="photo">
        <p class="addporto-main-txt">Editar de Porto</p>
        <div class="addporto-inputs">
          <div class="addporto-input-container">
            <p class="addporto-input-label">Nome: </p><input id="inputname" class="inputs" name="nome" type="text" required>
          </div>
          <div class="addporto-input-container">
            <p class="addporto-input-label">Descrição: </p><textarea id="inputdescr" class="addporto-input-txtarea" name="descricao" required></textarea>
          </div>
        </div>
        <input type="submit" name="confirmar" value="Alterar" class="addporto-confirm">
      </form>
    </div>

  </main>

  <script>
      const img_perfil=document.getElementById("porto_img_banner");
      const inputname=document.getElementById("inputname");
      const inputdescr=document.getElementById("inputdescr");
      img_perfil.style.backgroundImage=`url(${oldlink})`;
      inputname.value=oldname;
      inputdescr.value=olddescr
      imgInp.onchange = evt => {
          const [file] = imgInp.files
          if (file) {
              img_perfil.style.backgroundImage=`url(${URL.createObjectURL(file)})`;
          }
          else{
              img_perfil.style.backgroundImage=`url(${oldlink})`;
          }
      }
  </script>
</body>
</html>