<?php
$dotenv_dir='/app/backend/';
if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $dotenv_dir=$_SERVER['DOCUMENT_ROOT'].'/backend/';
if (file_exists($dotenv_dir.'.env')) {
    //.env exists connect with local database
    $dotenv = Dotenv\Dotenv::createImmutable($dotenv_dir, '.env');
    $dotenv->load();
}
function db_connection(){
    $db=false;
    $db_type=false;
    $DB_DBNAME_LOCAL=$_ENV['DB_DBNAME_LOCAL'];
    $DB_USER_LOCAL=$_ENV['DB_USER_LOCAL'];
    $DB_PASSWORD_LOCAL=$_ENV['DB_PASSWORD_LOCAL'];
    $DB_HOST_LOCAL=$_ENV['DB_HOST_LOCAL'];
    $DB_PORT_LOCAL=$_ENV['DB_PORT_LOCAL'];
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
        $db=mysqli_connect($DB_HOST_LOCAL,$DB_USER_LOCAL,$DB_PASSWORD_LOCAL,$DB_DBNAME_LOCAL,$DB_PORT_LOCAL);
        $db_type='mysql-local';
    }
    else{
        $url = parse_url($_ENV["CLEARDB_DATABASE_URL"]);
        $host = $url["host"];
        $user = $url["user"];
        $pass = $url["pass"];
        $dbname = substr($url["path"], 1);
        $db=mysqli_connect($host,$user,$pass,$dbname);
        $db_type='mysql';
    }
    return ['db'=>$db,'db_type'=>$db_type];
}

?>