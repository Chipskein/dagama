<?php
    //.env exists connect with local database
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) {
        $dotenv_dir='../';
        if (file_exists($dotenv_dir.'.env')) {
            $dotenv = Dotenv\Dotenv::createImmutable($dotenv_dir, '.env');
            $dotenv->load();
        }
    }
    require '../config/gdrive/driver.php';
    require '../config/mailer/mailer.php';
    require '../config/redis/redis.php';
    require '../database/database.php';
    require '../controllers/AssuntoController.php';
    require '../controllers/FriendController.php';
    require '../controllers/LocalController.php';
    require '../controllers/Login_RegisterController.php';
    require '../controllers/PortoController.php';
    require '../controllers/PostController.php';
    require '../controllers/UserController.php';
    
    function url($campo, $valor,$route) {
        $result = array();
        if (isset($_GET["orderby"])) $result["orderby"] = "orderby=".$_GET["orderby"];
        if (isset($_GET["offset"])) $result["offset"] = "offset=".$_GET["offset"];
        $result[$campo] = $campo."=".$valor;
        return("$route?".strtr(implode("&", $result), " ", "+"));
      }
    function pages($campo, $valor){
        $result = array();
        if (isset($_GET["page"])) $result["page"] = "page=".$_GET["page"];
        $result[$campo] = $campo."=".$valor;
        return '&'.(strtr(implode("&",$result), " ", "+"));
    }

?>