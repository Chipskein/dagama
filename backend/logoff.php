<?php
    session_start();
    if($_SESSION['userid']){
        unset($_SESSION['userid']);
        session_destroy();
        echo "<h2 align=center>Tchau Tchau Volte sempre</h2>";
        header("refresh:1;url=../index.php");
        die();
    }
    else {
        echo "<h2 aling=center>Você não esta logado</h2>";
        header("refresh:1;url=../index.php");
        die();
    }
?>