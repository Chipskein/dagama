<?php
    include $_SERVER['DOCUMENT_ROOT'].'/backend/mailer/mailer.php';
    include $_SERVER['DOCUMENT_ROOT'].'/backend/gdrive/driver.php';
    function db_connection(){
        $db=false;
        $db_type=false;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            //echo "Connect with sqlite<br>";
            $db=new SQLite3("$_SERVER[DOCUMENT_ROOT]/backend/infra/dagama.db");
            $test=$db->exec("PRAGMA FOREIGN_KEYS=ON");
            if(!$test){
                echo "<br>Um erro de conexão com banco ocorreu<br>"; 
                exit;
            }
            $db_type='sqlite';
        }
        if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
            $db=pg_connect(getenv("DATABASE_URL"));
            if (!$db) {
                echo "<br>Um erro de conexão com banco ocorreu<br>"; 
                exit;
            }
            $db_type='postgresql';
        }
        return ['db'=>$db,'db_type'=>$db_type];
    }
    
    //querys example
    function getAllPorto($offset,$limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result=$db->query("select * from porto limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db,"select * from porto limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function Login($email,$password){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $verify=$db->query("select codigo,senha as pass from perfil where perfil.email='$email'")->fetchArray();
                if(password_verify($password,$verify['pass'])) return $verify;
                else return false;
            }
            if($db_type=='postgresql'){
                $verify=pg_fetch_array(pg_query($db,"select codigo,senha as pass from perfil where perfil.email='$email'"));
                if(password_verify($password,$verify['pass'])) return $verify;
                else return false;
            }
        }
        else exit;
    }
    function Register($email, $password, $bdate, $username, $genero, $pais,$photo){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
        if($photo){
            $type=$photo['type'];
            $server_path=$photo['tmp_name'];
            $link="https://drive.google.com/uc?id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
        }
        if($db){
            if($db_type == 'sqlite'){
                $verify = $db->exec("insert into perfil (pais, email, senha, genero, username, datanasc,img) values ('".$pais."', '".$email."', '".$password."', '".$genero."', '".$username."', '".$bdate."', '".$link."'".")");
                if($verify) return $verify;
                else return false;
            }
        }
        else exit;  
    };
    
    /* QUERIES PARA VALIDAÇÃO */
    function getPaises(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo from pais")->fetchArray();
                if($response) return $response;
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_fetch_array(pg_query($db, "select codigo from pais"));
                if($response) return $response;
                else return false;
            }
        }
        else exit;
    };
    function getEmails(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email from perfil")->fetchArray();
                if($response) return $response;
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_fetch_array(pg_query($db, "select email from perfil"));
                if($response) return $response;
                else return false;
            }
        }
        else exit;
    };
    function emailExists($email){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email from perfil where email='$email'");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                //$response = pg_fetch_array(pg_query($db, "select email from perfil"));
                //if($response) return $response;
                //else return false;
            }
        }
        else exit;
    };
    function getUserInfo($id){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email,ativo,img,username from perfil where codigo='$id'");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                //$response = pg_fetch_array(pg_query($db, "select email from perfil"));
                //if($response) return $response;
                //else return false;
            }
        }
        else exit;
    };
    function getIdbyEmail($email){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo from perfil where email='$email'");
                if($response) return $response->fetchArray()['codigo'];
                else return false;
            }
        }
        else exit;
    };
?>