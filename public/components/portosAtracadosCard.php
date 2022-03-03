<?php
echo "<aside id=direita>";
echo "<div align=center class=background2>";
    echo "<p class=portosAtracados>Portos atracados:</p>";
    
    if($portosArray){
    foreach ($portosArray as $value) {
        echo "<div class=\"row porto-feed-container\">
        <div class=\"portos-img\" style=\"background-image: url($value[img])\"></div>
        <a class=nomePort href=/porto/$value[codigo] >$value[nome]</a>
        </div>";
    }
    echo "<br><a class=portosAtracadosMais href=/portosUser/$user[codigo] >Ver todos</a>";
    } else {
    echo "<p>Você não está em nenhum porto ainda</p>";
    }
    
echo "</div>";
echo "</aside>";
?>