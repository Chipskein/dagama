<?php
    //connect with SQLit
    if(!preg_match("/localhost/","$_SERVER[HTTP_HOST]")&&!preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
        echo "<h2>Inválido</h2>";
    }
    else{
        $db=false;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            echo "Connect with sqlite<br>";
            //$db=new SQLite3("");
            //$db->exec("");
        }
        if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
            echo "Connect with postgresql<br>";
            $db_connect=pg_connect(getenv("DATABASE_URL"));
            if (!$db_connect) {
                echo "An error occurred1.\n";
                exit;
              }
              
              $result = pg_query($db_connect, "SELECT * FROM teste");
              if (!$result) {
                echo "An error occurred.\n";
                exit;
              }
              
              while ($row = pg_fetch_row($result)) {
                echo $row['teste'];
                echo "<br />\n";
              }
        }
    }
    
?>