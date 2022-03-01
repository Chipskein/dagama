<?php
echo "<div align=center>";
    echo "<div class=perfil-amigos>";
        if($user['ativo']){
            echo "<a href=/amigos/$user[codigo] class=amigos> Amigos: ".($amigosUser ? $amigosUser[0]['qtdAmigos'] : 0)."</a>";
        }
        if($isOwner) echo "<h3><a href=/portosUser/$user[codigo] class=amigos>Meus Portos: $portosUser</a></h3>";
    echo "</div>";
echo "</div>";
?>