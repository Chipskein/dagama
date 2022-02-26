<?php
function load_dotenv(){
    $dotenv_dir='/app/backend/';
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $dotenv_dir='./backend';
    var_dump($dotenv_dir);
    if (file_exists($dotenv_dir.'.env')) {
        //.env exists connect with local database
        $dotenv = Dotenv\Dotenv::createImmutable($dotenv_dir, '.env');
        $dotenv->load();
    }
}
function db_connection(){
    $db=false;
    $db_type=false;
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
        //echo "Connect with sqlite<br>";
        $db=new SQLite3("/app/backend/infra/dagama.db");
        $test=$db->exec("PRAGMA FOREIGN_KEYS=ON");
        if(!$test){
            echo "<br>Um erro de conexão com banco ocorreu<br>"; 
            exit;
        }
        $db_type='sqlite';
    }
    return ['db'=>$db,'db_type'=>$db_type];
}
$DATABASE_URL=NULL;
$DB_DBNAME_LOCAL=NULL;
$DB_USER_LOCAL=NULL;
$DB_PASSWORD_LOCAL=NULL;
$DB_HOST_LOCAL=NULL;
$DB_PORT_LOCAL=NULL;
load_dotenv();
?>