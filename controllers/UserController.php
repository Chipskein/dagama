<?php
     use Dagama\Database;
    class UserController{
        public static function getAllUserInfo($offset,$limit,$where=''){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                if($where !== ''){
                    echo $where;
                    $response = mysqli_query($con,"select * from perfil where username like '%$where%' limit $limit offset $offset");
                }else {
                    $response = mysqli_query($con,"select * from perfil limit $limit offset $offset");
                }
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    $db->close();
                    return $results; 
                }
                else {
                    $db->close();
                    return false;
                }
                
            }
            else exit;
        }
        public static function countAllUsers(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];

                $response = mysqli_query($con,"select count(*)as total from perfil where ativo=1");
                if($response) {
                    $response = mysqli_fetch_array($response)['total'];
                    $db->close();
                    return $response;
                }
                else {
                    $db->close();
                    return false;
                }
                
            }
            else exit;
        }
        public static function getUserInfoRegister($id){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"select codigo, email, ativo, img, username, pais,senha as password from perfil where codigo=$id");
                if($response) return mysqli_fetch_array($response);
                else return false;
                
            }
            else exit;
        }
        public static function getUserInfo($id){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"select codigo, email, ativo, img, username, pais from perfil where codigo='$id' and ativo=1 ");
                if($response) return mysqli_fetch_array($response);
                else return false;
                
            }
            else exit;
        }
        public static function getIdbyEmail($email){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"select codigo from perfil where email='$email'");
                if($response) {
                    $response = mysqli_fetch_array($response)['codigo'];
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            
            }
            else exit;
        }
        public static function activateUser($id){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                
                $response = mysqli_query($con,"update perfil set ativo='1' where codigo=$id");
                if($response) {
                    $res=mysqli_query($con,"select email,senha as password from perfil where codigo='$id'");
                    if($res) {
                        $res = mysqli_fetch_array($res);
                        $db->close();
                        return $res;
                    } else {
                        $db->close();
                        return false;
                    }
                } else {
                    $db->close();
                    return false;
                }
                
            }
            else exit;
        }
        public static function deactivateUser($user){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update perfil set ativo='0' where codigo=$user");
                $db->close();
                return true;
            }
            else exit;
        }
        public static function changeUserName($id,$name){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update perfil set username='$name' where codigo=$id");
                $db->close();
                return true;
                
            }
            else exit;
        }
        public static function changeUserEmail($id,$email){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update perfil set email='$email' where codigo=$id");
                $db->close();
                return true;
                
            }
            else exit;
        }
        public static function changeUserSenha($id,$senha){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update perfil set\\ senha='$senha' where codigo=$id");
                $db->close();
                return true;
                
            }
        }
        public static function updateImg($id,$img,$oldimgid){
            $db=new Database();
            $con=$db->get_connection();
            $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
            if($con){
                if($img){
                    $type=$img['type'];
                    $server_path=$img['tmp_name'];
                    $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
                    rmFile($oldimgid);
                    $response = mysqli_query($con,"update perfil set img='$link' where codigo=$id");
                    if($response){
                        $response2=mysqli_query($con,"select img from perfil where codigo=$id")->fetch_array()['img'];
                        if($response2) {
                            $db->close();
                            return $response2;
                        } else {
                            $db->close();
                            return false;
                        }
                    } else {
                        $db->close();
                        return false;
                    }
                } else {
                    $db->close();
                    return false;
                }                
                
            }
            else exit;
        }
        public static function getPessoas(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                $response = mysqli_query($con,"select codigo, username, img from perfil where ativo = 1");
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    $db->close();
                    return $results; 
                }
                else { 
                    $db->close();
                    return false;
                }
                
            }
            else exit;
        }
    }
?>