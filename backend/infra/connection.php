<?php
    //connect with SQLite
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
        echo "Connect with sqlite<br>";
    }
    if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
        echo "Connect with postgresql<br>";
    }
    
?>