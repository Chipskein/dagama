<?php
    require '../vendor/autoload.php';
    require '../database/services.php';
    use Pecee\SimpleRouter\SimpleRouter;
    $router=new SimpleRouter();
    
    /*
    $router->error(function(){
        echo "ERROR";
        exit;
    });
    */
    
    $router->get('/', function() {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            header("Location: /feed");
            exit;
        }
        else
        {
            $paises=LocalController::getPaises();
            require '../public/view/index.php';
            exit;
        }
    });
    $router->post('/login', function() {
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid']))
        {
            if(isset($_POST['email'])&&isset($_POST['password']))
            {
                $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
                if(preg_match($regex_email,$_POST['email'])){
                    $email="$_POST[email]";
                    $pass="$_POST[password]";
                    $passed=Login_RegisterController::Login("$email","$pass");
                    if($passed){
                        if($passed['ativo']=='1'||$passed['ativo']==='t'){
                            $USERID=$passed['codigo'];
                            $USERIMG=$passed['img'];
                            $USERNAME=$passed['username'];
                            $_SESSION["userid"] = $USERID;
                            $_SESSION["userimg"] = $USERIMG;
                            $_SESSION["username"] = $USERNAME;

                            header('Location: /feed');
                            exit;
                        }
                        else{
                            header("Location: /validateEmail/$passed[codigo]");
                            exit;
                        }
                    }
                    else{
                        header('Location: /');
                        exit;
                    }
                }
                else{
                    header('Location: /');
                    exit;
                }
            }
        }
        else
        {
            header('Location: /feed');
            exit;
        }
    });
    $router->get('/logoff', function() {
        if(!isset($_SESSION)) session_start(); 
        if(isset($_SESSION['userid']))
        {
            unset($_SESSION['userid']);
            unset($_SESSION['userimg']);
            unset($_SESSION['username']);
            session_destroy();
            header("Location: /");
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
        
    });
    $router->post('/register', function() {
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid']))
        {
            $email = "$_POST[email]";
            $username = "$_POST[username]";
            $password = password_hash("$_POST[password]", PASSWORD_DEFAULT);
            $bdate = "$_POST[bdate]";//converter bdate to yyyy/mm/dd
            $pais=$_POST["pais"];
            $genero = "$_POST[genero]";
            $photo = null;
            if($_FILES['photo']['tmp_name']) $photo = is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo'] : null;
            $registered = Login_RegisterController::Register($email, $password, $bdate, $username, $genero, $pais, $photo);
            if($registered){
                $id=UserController::getIdbyEmail($email);
                header("Location: /validateEmail/$id");
                exit;                
            } 
        }
        else
        {
            require '../public/view/feed.php';
            exit;
        }
    });
    $router->get("/validateEmail/{id}",function ($id){
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid']))
        {
            $user=UserController::getUserInfoRegister($id);
            if($user){
               require "../public/view/validarEmail.php";
               exit;
            }
            else exit;
        }
        else
        {
            require '../public/view/feed.php';
            exit;
        }
    });
    $router->get("/sendmail/{id}",function($id){
        $user=UserController::getUserInfoRegister($id);
        if($user){
            if(!$user['ativo']||$user['ativo']=='f'){
                $email="$user[email]";
                $start_link='';
                if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $start_link="http://";
                if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")) $start_link="https://";
                echo uniqid("",true);
                $link=$start_link."TESTANDo";//"$_SERVER[HTTP_HOST]/backend/validate_acc.php?id="."$_GET[id]";
                $html="
                    <html lang=pt-BR>
                    <head>
                        <meta charset=UTF-8>
                        <meta http-equiv=X-UA-Compatibl content=IE=edge>
                        <meta name=viewport content=width=device-width, initial-scale=1.0>
                    </head>
                    <body>
                        <a href=$link>link</a>
                    </body>
                    </html>
                ";
                //send_mail($email,"Dagama | Validar conta ",$html);
                exit;
            }
            else{
                header("Location: /");
                exit;
            }
        };
    });
    


    $router->get('/createPorto', function() {
    });
    $router->post('/createPorto', function() {
    });
    
    $router->get('/porto/{id}', function($id) {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            $user =UserController::getUserInfo("$_SESSION[userid]");
            $postsArray =PortoController::getPostsOnPorto($id, 0, 10);
            $participantesPorto = [];//PortoController::getPortoParticipants($id, 0, 5);
            $allParticipantesPorto = PortoController::getAllPortoParticipants($id);
            $locaisArray = [];
            $assuntosArray = AssuntoController::getAssuntos();
            $pessoasArray = UserController::getPessoas();
            $paises=LocalController::getPaises();
            $estados=[];
            $cidades=[];
            $portoInfo = PortoController::getPortInfo($id, $_SESSION['userid']);
            require '../public/view/porto.php';
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
    });
    $router->get('/navio/{id}', function($id) {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            $orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "tmp1.data desc";
            $user=UserController::getUserInfo("$id");
            $userSelf=UserController::getUserInfo("$_SESSION[userid]");
            $isOwner= "$id"=="$_SESSION[userid]" ? true:false;
            $limit = 5;
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
            $orderby = (isset($_GET["orderby"])) ? $_GET["offset"] : "tmp1.data desc";
            $getAllPosts =PostController::getAllPosts($id);
            $postsArray = PostController::getPosts($id, $offset, $limit, $orderby);
            $amigosUser =[]; //getFriends($id, 0, 3,'');
            $portosArray =PortoController::getAllPorto($id, true, 0, 3, null);
            $portosUser = PortoController::getUserPortoQtd($id);
            $locaisArray = [];
            $assuntosArray =AssuntoController::getAssuntos();
            $pessoasArray = UserController::getPessoas();
            $paises=LocalController::getPaises();
            $estados=[];
            $cidades=[];

            require '../public/view/navio.php';
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
    });
    $router->get('/mar', function() {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            require '../public/view/mar.php';
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
    });
    $router->get('/feed', function() {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            require '../public/view/feed.php';
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
    });









    $router->post('/novoPost', function() {
    });
    $router->post('/deletePost', function() {
    });
    $router->post('/removeCitacao', function() {
    });
    $router->post('/sendFriendRequest', function() {
    });



    $router->post('/entrarPorto', function() {
    });
    $router->post('/sairPorto', function() {
    });

    $router->get('/about', function() {
        require '../LICENSE';
    });

    
?>