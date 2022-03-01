<?php
echo "<aside id=esquerda>";
  if($user['ativo']){
    echo "<div align=center class=background>";
      echo "<div>";
        echo "<p class=SeusAmigos>".($isOwner ? "Seus amigos" : "Amigos de $user[username]")."</p>";
      echo "</div>";
        if(count($amigosUser) > 0){
          foreach ($amigosUser as $amigo) {
            echo "<a href=/navio/$amigo[amigoCod]><div><img src=$amigo[imgAmigo] class=div-amigo-image><p class=nomeAmigo>$amigo[nameAmigo]</p></div></a>";
          }
        } else {
          echo $isOwner ? "<p>Você ainda não tem nenhum amigo</p>":"<p>Sem amigos</p>";
        }          
        echo "<a class=portosAtracadosMais href=/amigos/$user[codigo]>Ver mais</a>";
    echo "</div>";
  }
echo "</aside>";
?>