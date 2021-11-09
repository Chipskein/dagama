<?php
    //connect with SQLit
    if(!preg_match("/localhost/","$_SERVER[HTTP_HOST]")&&!preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
        echo "<h2>Inválido</h2>";
    }
    else{
        //$db=false;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            echo "Connect with sqlite<br>";
            $db=new SQLite3("dagama.db");
            $db->exec("PRAGMA FOREIGN_KEYS=ON");
            exit;            
        }
        if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
            echo "Connect with postgresql<br>";
            $db=pg_connect(getenv("DATABASE_URL"));
            if (!$db) {
                echo "An error occurred1.\n";
                exit;
              }
              
              $result = false;//pg_query($db, "SELECT * FROM teste");
              if (!$result) {
                echo "An error occurred.\n";
                exit;
              }
              
              while ($row = pg_fetch_row($result)) {
                var_dump($row);
                echo "<br />\n";
              }
        }
    }
    
?>