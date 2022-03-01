<?php
    echo "<div id=\"img_perfil\" class=perfil></div>";
    echo "<form id=formPhoto action=\"/navio/$user[codigo]\" enctype=multipart/form-data method=POST>";
        echo "<input id=\"imgInp\" class=\"hidden\" type=\"file\" name=\"photo\">";
    echo "</form>";
    if($isOwner)echo "<div id=camera-icon></div>";
    echo "<div align=center class=divUsername>";
    echo "<h3 class=perfil>$user[username]</h3>";
    if($isOwner) echo"<a href=/editNavio/$user[codigo]><img class=img-pencil src=\"/imgs/icons/clarity_pencil-line.png\"</img></a>";
    echo "</div>";
  echo "</div>";
  echo "<br>";
?>