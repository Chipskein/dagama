<?php
 use Dagama\Database;
class Login_RegisterController{

    public static function Login($email,$password){
        $db=new Database();
        $con=$db->get_connection();
        if($con){
            $verify=mysqli_query($con,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetch_array();
            if(password_verify($password,$verify['pass'])) {
                $db->close();
                return $verify;
            }
            else {
                $db->close();
                return false;
            }
            
        }
        else exit;
    }
    public static function Login2($email,$password){
        $db=new Database();
        $con=$db->get_connection();
        if($con){
            $verify=mysqli_query($con,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetch_array();
            if("$password"=="$verify[pass]") {
                $db->close();
                return $verify;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    public static function Register($email, $password, $bdate, $username, $genero, $pais,$photo){
        $db=new Database();
        $con=$db->get_connection();
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
        
        if($photo){
            $type=$photo['type'];
            $server_path=$photo['tmp_name'];
            $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
        }
        if($con){
            $verify = mysqli_query($con,"insert into perfil (pais, email, senha, genero, username, datanasc,img) values ('".$pais."', '".$email."', '".$password."', '".$genero."', '".$username."', '".$bdate."', '".$link."'".")");
            if($verify) {
                $db->close();
                return $verify;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;  
    }
    public static function getEmails(){ //remover
        $db=new Database();
        $con=$db->get_connection();
        if($con){
            $response = mysqli_query($con,"select email from perfil")->fetch_array();
            if($response) {
                $db->close();
                return $response;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    public static function emailExists($email){ //substitui getEmails na validação
        $db=new Database();
        $con=$db->get_connection();
        if($con){
            $response = mysqli_query($con,"select email from perfil where email='$email'");
            if($response) {
                $response = mysqli_fetch_array($response);
                $db->close();
                return $response;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
}
?>