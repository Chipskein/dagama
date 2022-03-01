<?php
     echo "<aside id=esquerda>";
     echo "<div align=center class=background2>";
         echo "<p class=portosAtracados>Ondas do momento:</p>";
         echo "<div align=start>";
           $c = 1;
           if($topAssuntos!=false&& count($topAssuntos) > 0){
             foreach ($topAssuntos as $topassunto){
               echo "<p class=trending>$c º - $topassunto[nome]</p>";
               $c++;
             }
           } else {
             echo "<p align=center>Sem assuntos na cidade...</p>";
           }
 
         echo "</div>";
    echo "</div>";
   echo "</aside>";
?>