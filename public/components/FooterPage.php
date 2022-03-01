<?php
echo "<footer style=\"padding-top:20px; padding-bottom:20px\" align=center>";
    $links = 4;
    $page = isset($_GET["page"]) ? strtr($_GET["page"], " ", "%") : 0;
    echo "<div style=\"row\">";
    echo "<a class=\"paginacaoNumber\" href=\"".url("offset",0*$limit,$route).pages("page", 1)."\">primeira </a>";
    for($pag_inf = $page - $links ;$pag_inf <= $page - 1;$pag_inf++){
        if($pag_inf >= 1 ){
            echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_inf-1)*$limit,$route).pages("page", $pag_inf)."\"> ".($pag_inf)." </a>";
        }
    };
    if($page != 0 ){
        echo "<a class=\"paginacaoNumber\" style=color:yellow;>$page</a>";
    };
    for($pag_sub = $page+1;$pag_sub <= $page + $links;$pag_sub++){
        if($pag_sub <= ceil($total/$limit)){
            echo "<a class=\"paginacaoNumber\" href=\"".url("offset",($pag_sub-1)*$limit,$route).pages("page", $pag_sub)."\"> ".($pag_sub)." </a>";
        }
    }
    echo "<a class=\"paginacaoNumber\" href=\"".url("offset",ceil($total/$limit)*$limit/$limit,$route).pages("page", ceil($total/$limit))."\"> ultima</a>";
    echo "</div>";
echo "</footer>";
?>