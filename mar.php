<?php
    $db=new SQLite3("face.db");
    $test=$db->query("select * from pessoa")->fetchArray();
    while($row=$test){
        var_dump($row);
    }

?>