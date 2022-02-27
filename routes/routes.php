<?php
    require '../vendor/autoload.php';
    require '../database/services.php';
    use Pecee\SimpleRouter\SimpleRouter;
    $router=new SimpleRouter();
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
                            header('Location: /');
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
        }
        else
        {
            require '../public/view/feed.php';
            exit;
        }
    });
    $router->get('/createPorto', function() {
    });
    $router->post('/createPorto', function() {
    });
    
    $router->get('/porto/{id}', function() {
    });
    $router->get('/navio/{id}', function() {
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
?>