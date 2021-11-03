<?php
    //connect with SQLite
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
        echo "Connect with sqlite<br>";
        $db=new SQLite3("");
        $db->exec("");
    }
    if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
        echo "Connect with postgresql<br>";
        echo getenv("$DATABASE_URL") ? "$url":"ERRO";
        //$db=pg_connect("");
    }
    if(!preg_match("/localhost/","$_SERVER[HTTP_HOST]")&&!preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
        echo "<h2>Inválido</h2>";
    }
    
?>