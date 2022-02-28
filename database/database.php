<?php
namespace Dagama;
class Database{
    private $db_connection;

    function __construct()
    {
        $db=false;
        $DB_DBNAME_LOCAL= isset($_ENV['DB_DBNAME_LOCAL']) ? $_ENV['DB_DBNAME_LOCAL']:NULL;
        $DB_USER_LOCAL= isset($_ENV['DB_USER_LOCAL']) ? $_ENV['DB_USER_LOCAL']:NULL;
        $DB_PASSWORD_LOCAL= isset($_ENV['DB_PASSWORD_LOCAL']) ? $_ENV['DB_PASSWORD_LOCAL']:NULL;
        $DB_HOST_LOCAL= isset($_ENV['DB_HOST_LOCAL']) ? $_ENV['DB_HOST_LOCAL']:NULL;
        $DB_PORT_LOCAL= isset($_ENV['DB_PORT_LOCAL']) ? $_ENV['DB_PORT_LOCAL']:NULL;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            $db=mysqli_connect($DB_HOST_LOCAL,$DB_USER_LOCAL,$DB_PASSWORD_LOCAL,$DB_DBNAME_LOCAL,$DB_PORT_LOCAL);
        }
        else{
            $url = parse_url($_ENV["JAWSDB_MARIA_URL"]);
            $host = $url["host"];
            $user = $url["user"];
            $pass = $url["pass"];
            $dbname = substr($url["path"], 1);
            $db=mysqli_connect($host,$user,$pass,$dbname);
        }
        $this->db_connection=$db;
    }
    public function get_connection()
    {
        return $this->db_connection;
    }
    public function close()
    {
        if($this->db_connection)
        {
            $this->db_connection->close();
        }
    }
}

?>