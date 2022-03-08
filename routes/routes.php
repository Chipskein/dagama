<?php
    require '../vendor/autoload.php';
    require '../database/services.php';

    use Pecee\SimpleRouter\SimpleRouter;

    $router=new SimpleRouter();
    
    /*
    $router->error(function($er){
        echo "Um Erro ocorreu";
        var_dump($er);
        exit;
    });
    */
    $router->get("/search",function(){
        if(!isset($_SESSION))session_start();
        if(isset($_GET['searchTerm'])&&trim($_GET['searchTerm'])!=''){
            $campo = $_GET['searchTerm'];
            $limit=5;
            $offset= isset($_GET['offset']) ? $_GET['offset']:0;
            $order = null;
            $portos = PortoController::getAllPortos($offset, $limit, $order, $campo);
            $users=UserController::getAllUserInfo($offset,$limit,$campo);
            $total=UserController::countAllUsers();
            require '../public/view/search.php';
            exit;
        }
        else
        {
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit;
        }
    });
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
    $router->get('/auth',function(){
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid'])&&isset($_SESSION["tmp_mail"])&&isset($_SESSION["tmp_p"]))
        {
            $tmp_mail=$_SESSION["tmp_mail"];
            $tmp_p=$_SESSION["tmp_p"];

            unset($_SESSION["tmp_mail"]);
            unset($_SESSION["tmp_p"]);
            
            session_destroy();

            $regex_email="/^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$/";
            if(preg_match($regex_email,$tmp_mail))
            {
                $email=$tmp_mail;
                $pass=$tmp_p;
                $passed=Login_RegisterController::Login2("$email","$pass");
                var_dump($passed);
                if($passed){
                    if($passed['ativo']=='1'||$passed['ativo']==='t'){
                        session_start();
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
            else
            {
                header('Location: /');
                exit;
            }
        
        }
        else
        {
            header("Location: /");
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
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid'])){
            $user=UserController::getUserInfoRegister($id);
            if($user){
                if(!$user['ativo']||$user['ativo']=='f'){
                    $email="$user[email]";
                    $userid="$user[codigo]";
                    $size=strlen($userid);
                    $start_link='';
                    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $start_link="http://";
                    if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")) $start_link="https://";
                    $urlid=uniqid("$userid",true);
                    $urlid=str_replace(".","",$urlid);
                    $uniqid=substr($urlid,$size);
                    $redis=new Redis();
                    $redis->setKey($email,$uniqid);

                    $link=$start_link."$_SERVER[HTTP_HOST]/validateacc/$urlid?size=$size";
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
                    send_mail($email,"Dagama | Validar conta ",$html);
                    header("Location: /validateEmail/$userid");
                    exit;
                }
                else{
                    header("Location: /");
                    exit;
                }
            };
        }
    });
    $router->get("/validateacc/{urlid}",function($urlid){
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid'])&&isset($_GET['size'])){
            $size=$_GET["size"];
            $id=substr($urlid,0,$size);
            $uniqid=substr($urlid,$size);
            $user=UserController::getUserInfoRegister($id);
            $redis=new Redis();
            if($user){
                $redis_uniqid=$redis->getKey($user["email"]);
                if($redis_uniqid&&$redis_uniqid==$uniqid)
                {
                    echo "entrou aq";
                    if($user["ativo"]==0){
                        $activated=UserController::activateUser($id);
                        if($activated)
                        {
                            $_SESSION["tmp_mail"]=$user["email"];
                            $_SESSION["tmp_p"]=$user["password"];
                            header("Location: /auth");
                            exit;
                        }
                    }
                }
                else
                {
                    header("Location: /");
                    exit;
                }
            }
        }
        else
        {
            header("Location: /");
        }
    });
    $router->get("/amigos/{id}",function($id){
        if(!isset($_SESSION)) session_start();
        if(isset($_GET['username'])){
            $where = $_GET['username'];
          } 
        else{
            $where = '';
        }
        if(isset($id)){
            $offset=0;
            $total = 5;
            $limit = 5;
            $amigosUser =[];//getFriends($id, $offset, 10,$where);
            if($_SESSION['userid'] == $id){
            $amigos=[];//getRequestAndFriends($_SESSION["userid"],false);
            if(isset($_POST['desfazerAmizade'])){
                $response = FriendController::delFriend($_SESSION['userid'], $_POST['amigo']);
                if($response) header("refresh:1;url=amigos.php");
                else echo "Erro ao desfazer amizade...";
            }
            }
        } 



        require '../public/view/amigos.php';
    });
    $router->get("/portosUser/{id}",function($id){
        if(!isset($_SESSION)) session_start(); 
        if(isset($_SESSION['userid']))
        {
            $limit=10;//mudar pra 10 dps
            $offset= isset($_GET['offset']) ? $_GET['offset']:0;
            $IsOwner=false;
            
            if($_SESSION['userid'] == $id) $IsOwner=true;
            $portos = PortoController::getAllPorto($id, $IsOwner, $offset, $limit, 0);          
            $total=count(PortoController::getUserPorto($id, null, null));
            require '../public/view/portosUser.php';
            exit;
        }
    });
    $router->get('/createPorto', function() {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            require '../public/view/createPorto.php';
        }
    });
    $router->post('/createPorto', function() {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION['userid']))
        {
            if(isset($_POST['descr']) && isset($_POST['nome']))
            {
                
                $perfil = "$_SESSION[userid]";
                $nome = "$_POST[nome]";
                $descr = "$_POST[descr]";
                $img= isset($_FILES['photo'])&&is_uploaded_file($_FILES['photo']['tmp_name']) ? $_FILES['photo']:null;
                $registered = PortoController::addPorto($perfil,$nome,$descr,$img);
                if($registered){
                    header("Location: /porto/$registered");
                    exit;
                } 
            }
            else
            {
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit;
            }
        }
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
            $total=count($getAllPosts);
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
            $limit=5;//mudar pra 10 dps
            $offset= isset($_GET['offset']) ? $_GET['offset']:0;
            $orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "data desc";
            $portos=PortoController::getAllPorto($_SESSION['userid'], false, $offset, $limit, $orderby);
            $total=PortoController::getTotalPorto();
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
            $user = UserController::getUserInfo("$_SESSION[userid]");
            $limit = 5;
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
            $orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "tmp1.data desc";
            $locaisArray = [];
            $assuntosArray = AssuntoController::getAssuntos();
            $pessoasArray = UserController::getPessoas();
            $topAssuntos=[];//OndasDoMomento(3,$user['pais']);
            $paises=LocalController::getPaises();
            $estados=[];
            $cidades=[];
            $suggestFriends = [];//suggestFriends($_SESSION['userid'], 4, 0);
            $where = 'vi';
            $postsArray = PostController::getPosts($_SESSION['userid'], $offset, $limit, $orderby);
            $getAllPosts = PostController::getAllPosts($_SESSION['userid']);
            $total=count($getAllPosts);
            $portosArray = PortoController::getAllPorto($_SESSION['userid'], true, 0, 3, null, );
            $portosArrayForShare = PortoController::getAllPorto($_SESSION['userid'], true, 0, 0,null);
            $errorMessage = [];
            require '../public/view/feed.php';
            exit;
        }
        else
        {
            header("Location: /");
            exit;
        }
    });
    $router->get('/editNavio',function(){
        if(!isset($_SESSION)) session_start();
        if(!isset($_SESSION['userid'])){
            echo "<h2 align=center>Para ver este conteudo faça um cadastro no dagama!!!</h2>";
            header("refresh:1;url=index.php");
            exit;
        }
        $user=UserController::getUserInfo("$_SESSION[userid]");
        require '../public/view/editNavio.php';
    });
    $router->post('/delporto',function(){
        if(!isset($_SESSION)) session_start();
        if(isset($_POST['excluirPorto'])&&isset($_POST['PortoCod'])){
            $response = PortoController::delPorto($_POST['PortoCod']);
            if($response)
            {
                header("Location:/mar");
            } 
            header("Location:/mar");
            exit;
        }
    });
    $router->get('/entrarPorto/{id}', function($id) {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION["userid"]))
        {
            $response = PortoController::entrarPorto($_SESSION['userid'],$id);
            if(!$response){
              echo "Erro ao entrar no porto";
            } 
            else {
              header("Location:/porto/$id"); 
            }
        }

    });
    $router->get('/sairPorto/{id}', function($id) {
        if(!isset($_SESSION)) session_start();
        if(isset($_SESSION["userid"]))
        {
            $response = PortoController::sairPorto($_SESSION['userid'],$id);
            if(!$response)
            {
              echo "Erro ao sair do porto";
            } 
            else 
            {
              header("Location:/mar");
            }
        }
    });
    $router->post('/editarPorto',function(){
        if(!isset($_SESSION)) session_start(); 
        require '../public/view/editarPorto.php';
    });







    $router->post('/addassunto',function(){});
    $router->post('/newpost',function(){});
    $router->post('/delpost',function(){});
    $router->post('/rmcitac',function(){});
    $router->post('/friendrequest',function(){});
    $router->get('/completeInteracao/$post',function($post){});
 
?>