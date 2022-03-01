<?php
if($postsArray){
    
    foreach ($postsArray as $post) {
      // print_r($post);
      echo "<div class=\"div-post\">";
        if($post['codPorto']){
          echo "<p class=\"compartilhado-txt\"><i>Postado no porto <a href=/porto/$post[codPorto] class=\"txt-linktoporto\">$post[nomePorto]</a></i></p>";
        }
        //Share
        $sharedPost = 0;
        if($post['isSharing']){
          $sharedPost = [];//getOriginalPost($post['codPost']);
          echo "<p class=\"compartilhado-txt\"><i>Compartilhado</i></p>";
          echo "<div class=\"div-sharing-post\">";
            // Sharing-top
            echo "<div class=\"div-sharing-post-top\">";
              echo "<a href=/navio/$sharedPost[codPerfil]><img src=\"".$sharedPost['iconPerfil']."\" alt=\"\" class=\"div-sharing-post-top-icon\"></a>";
              echo "<div class=\"div-post-top-infos\">";
              echo "<div class=\"row\">";
                echo "<p class=\"div-post-top-username\"><i>@".$sharedPost['nomePerfil']."</i>";
              echo "</div>";
                if($sharedPost['nomePais']){
                  echo " em ".$sharedPost['nomePais']." - ";
                }
                $tmpHora = explode(' ', $sharedPost['dataPost'])[1];
                $tmpData = explode(' ', $sharedPost['dataPost'])[0];
                $tmpData = explode('-', $tmpData);
                echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                $tmpArray = [];
                // print_r($post['assuntos']);
                foreach($sharedPost['assuntos'] as $elements){
                  foreach ($elements as $key => $value) {
                    if($key === 'nomeAssunto') $tmpArray[] = $value;
                  }
                }
                echo "<p class=\"div-post-top-subjects\" title=\"".implode(',',$tmpArray)."\"><b>";
                $tmpArray = implode(',',$tmpArray);
                if(strlen($tmpArray) > 30){
                  $tmpArray = substr($tmpArray, 0, 27);
                  echo $tmpArray."...";
                } else {
                  echo $tmpArray;
                }
                echo "</b></p>";
              echo "</div>";
            echo "</div>";
            // Sharing-texto
            echo "<div class=\"div-post-txt\">";
              echo "<p><i style=\"color: #7A9EFB\">@$sharedPost[nomePerfil]</i> ";
              if($sharedPost['isReaction']) {
                echo "<b><i>reagiu</i></b> com ";
                switch ($sharedPost['emote']){
                  case 'curtir':
                    echo "👌";
                    break;
                  case 'kkk':
                    echo "🤣";
                    break;
                  case 'amei':
                    echo "❤️";
                    break;
                  case 'grr':
                    echo "🤬";
                    break;
                  case 'wow':
                    echo "🤯";
                    break;
                  case 'sad':
                    echo "😭";
                    break;                  
                }
                echo ", ";
              }
              if(count($sharedPost['citacoes']) > 0) {
                $tmpCitacoes = [];
                foreach ($sharedPost['citacoes'] as $pessoa) {
                  $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                }
                
                $tmpCitacoes = implode(',',$tmpCitacoes);
                echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                if(strlen($tmpCitacoes) > 10){
                  $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                  echo $tmpCitacoes."...";
                } else {
                  echo $tmpCitacoes;
                }
                echo ", </i>";
                
              }
              echo "$sharedPost[textoPost]</p>";
            echo "</div>";
          echo "</div>";
        }
        //Top
        echo "<div class=\"div-post-top\">";
          echo "<a href=/navio/$post[codPerfil]><img src=\"".$post['iconPerfil']."\" alt=\"\" class=\"div-post-top-icon\"></a>";
          echo "<div class=\"div-post-top-infos\">";
          echo "<p class=\"div-post-top-username\"><i>@".$post['nomePerfil']."</i>";
          // echo "</div>";
            if(isset($post['nomePais'])){
              echo " em $post[nomePais] ";
            }
            $tmpHora = explode(' ', $post['dataPost'])[1];
            $tmpData = explode(' ', $post['dataPost'])[0];
            $tmpData = explode('-', $tmpData);
            echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
            $tmpArray = [];
            foreach($post['assuntos'] as $elements){
              foreach ($elements as $key => $value) {
                if($key === 'nomeAssunto') $tmpArray[] = $value;
              }
            }
            echo "<p class=\"div-post-top-subjects\" title=\"".implode(',',$tmpArray)."\"><b>";
            $tmpArray = implode(',',$tmpArray);
            if(strlen($tmpArray) > 30){
              $tmpArray = substr($tmpArray, 0, 27);
              echo $tmpArray."...";
            } else {
              echo $tmpArray;
            }
            echo "</b></p>";
          echo "</div>";
          if($post['isSharing'] && ($sharedPost['codPerfil'] == $_SESSION['userid'])){
            echo "<div class=\"div-post-top-editicons\">";
                echo "<form action=\"/delpost\" method=\"post\">";
                    echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"/imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
                echo "</form>";
            echo "</div>";
          } 
          if($post['codPerfil'] == $_SESSION['userid']) {
            echo "<div class=\"div-post-top-editicons\">";
                echo "<a href=\"/editarInteracao/$post[codInteracao]\"><img src=\"/imgs/icons/pencil.png\" class=\"div-post-top-editicons-pencil\" alt=\"\" /></a>";
                echo "<form action=\"/delpost\" method=\"post\">";
                    echo "<button type=\"submit\" name=\"deletePost\" value=\"$post[codInteracao]\"><img src=\"/imgs/icons/trash.png\" class=\"div-post-top-editicons-trash\" alt=\"\" /></button>";
                echo "</form>";
            echo "</div>";
          }
        echo "</div>";
        //Texto
        echo "<div class=\"div-post-txt\">";
          echo "<p><i style=\"color: #7A9EFB\">@$post[nomePerfil]</i> ";
          if($post['isReaction']) {
            echo "<b><i>reagiu</i></b> com ";
            switch ($post['emote']){
              case 'curtir':
                echo "👌";
                break;
              case 'kkk':
                echo "🤣";
                break;
              case 'amei':
                echo "❤️";
                break;
              case 'grr':
                echo "🤬";
                break;
              case 'wow':
                echo "🤯";
                break;
              case 'sad':
                echo "😭";
                break;                  
            }
            echo ", ";
          }
          $isMentioned = 0;
          if(count($post['citacoes']) > 0) {
            $tmpCitacoes = [];
            foreach ($post['citacoes'] as $pessoa) {
              $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
              if($pessoa['codPerfil'] == $_SESSION['userid'] && $post['codPerfil'] != $_SESSION['userid']) $isMentioned = 1;
            }
            
            $tmpCitacoes = implode(',',$tmpCitacoes);
            echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
            if(strlen($tmpCitacoes) > 10){
              $tmpCitacoes = substr($tmpCitacoes, 0, 7);
              echo $tmpCitacoes."...";
            } else {
              echo $tmpCitacoes;
            }
            echo ", </i>";
          }
          echo "$post[textoPost]</p>";
        echo "</div>";
        //Ícones
        echo "<div class=\"div-post-icons-bar\">";
          if($isMentioned) {
            echo "<form action=\"/rmcitac\" method=\"post\">";
            echo "<button type=\"submit\" name=\"removeCitacao\" class=\"interacao-remover-txt\" value=\"$post[codInteracao]\"><p>Remover sua citação</p></button>";
            echo "</form>";
          }
          echo "<div class=\"div-post-icons-bar-divs\">";
            echo "<p>$post[qtdInteracao]</p><img src=\"/imgs/icons/chat.png\" class=\"div-post-icons-bar-icons\" alt=\"\">";
          echo "</div>";
          echo "<div class=\"div-post-icons-bar-interagir\">";
            echo "<a href=\"/interagir/$post[codInteracao]\"><img src=\"$user[img]\" class=\"div-post-icons-bar-interagir-icon\" alt=\"\"><p>Interagir...</p></a>";
          echo "</div>";
        echo "</div>";
        echo "<br><br>";
        //Comentários
        if($post['comentarios'] && $post['comentarios'] != []){
          echo "<hr class=\"post-hr\">";
          foreach ($post['comentarios'] as $comentario) {
            echo "<div class=\"comment-container\">";
              echo "<div class=\"comment-container-top\">";
                echo "<a href=/navio/$comentario[codPerfil]><img src=\"".$comentario['iconPerfil']."\" alt=\"\" class=\"comment-icon\"></a>";
                echo "<p class=\"comment-txt\"><i>@".$comentario['nomePerfil']."</i> ";
                if($comentario['isReaction']) {
                  echo "<b><i>reagiu</i></b> com ";
                  switch ($comentario['emote']){
                    case 'curtir':
                      echo "👌";
                      break;
                    case 'kkk':
                      echo "🤣";
                      break;
                    case 'amei':
                      echo "❤️";
                      break;
                    case 'grr':
                      echo "🤬";
                      break;
                    case 'wow':
                      echo "🤯";
                      break;
                    case 'sad':
                      echo "😭";
                      break;                  
                  }
                  echo ", ";
                }
                $isMentioned2 = 0;
                if(count($comentario['citacoes']) > 0) {
                  $tmpCitacoes = [];
                  foreach ($comentario['citacoes'] as $pessoa) {
                    $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                    if($pessoa['codPerfil'] == $_SESSION['userid'] && $comentario['codPerfil'] != $_SESSION['userid']) $isMentioned2 = 1;
                  }
                  
                  $tmpCitacoes = implode(',',$tmpCitacoes);
                  echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                  if(strlen($tmpCitacoes) > 10){
                    $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                    echo $tmpCitacoes."...";
                  } else {
                    echo $tmpCitacoes;
                  }
                  
                  echo ", </i>";
                }
                if(count($comentario['assuntos']) > 0) {
                  $tmpAssuntos = [];
                  foreach ($comentario['assuntos'] as $assunto) {
                    $tmpAssuntos[] = $assunto['nomeAssunto'];
                  }
                  
                  $tmpAssuntos = implode(',',$tmpAssuntos);
                  echo "com os <b><i>assuntos</i></b> <i title=\"".$tmpAssuntos."\">";
                  if(strlen($tmpAssuntos) > 10){
                    $tmpAssuntos = substr($tmpAssuntos, 0, 7);
                    echo $tmpAssuntos."...";
                  } else {
                    echo $tmpAssuntos;
                  }
                  echo ", </i>";
                }
                echo ($comentario['textoPost'] ? $comentario['textoPost'] : '');
                if(isset($comentario['nomePais'])){
                  echo "em $comentario[nomePais]";
                }
                $tmpHora = explode(' ', $comentario['dataPost'])[1];
                $tmpData = explode(' ', $comentario['dataPost'])[0];
                $tmpData = explode('-', $tmpData);
                echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                echo "</p>";
              echo "</div>";
              echo "<div class=\"comment-reagir\">";
              echo "<a href=\"/interagir/$comentario[codInteracao]\">Reagir</a>";
                if($comentario['codPerfil'] == $_SESSION['userid']) {
                  echo "<a href=\"editarInteracao.php?interacao=$comentario[codInteracao]\"><p class=\"interacao-editar-txt\">- Editar -</p></a>";
                  echo "<form action=\"feed.php?user=$_SESSION[userid]\" method=\"post\">";
                  echo "<button type=\"submit\" name=\"deletePost\" value=\"$comentario[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                  echo "</form>";
                }
                if($comentario['codPerfil'] != $_SESSION['userid'] && $post['codPerfil'] == $_SESSION['userid']) {
                  echo "<form action=\"/delpost\" method=\"post\">";
                  echo "<button type=\"submit\" name=\"deletePost\" value=\"$comentario[codInteracao]\"><p class=\"interacao-remover-txt\">- Remover</p></button>";
                  echo "</form>";
                }
              echo "</div>";
              // Respostas
              if($comentario['respostas'] && $comentario['respostas'] != []){
                foreach ($comentario['respostas'] as $resposta) {
                  echo "<div class=\"comment-resp-container\">";
                    echo "<div class=\"comment-container-top\">";
                      echo "<a href=/navio.php/$resposta[codPerfil]><img src=\"".$resposta['iconPerfil']."\" alt=\"\" class=\"comment-icon\"></a>";
                      echo "<div class=\"row\">";
                        // echo "<img class=\"coment-mainuser-user-selo\" src=\"/imgs/icons/bronze-medal.png\"/>";   
                        echo "<p class=\"comment-txt\"><i>@".$resposta['nomePerfil']."</i> ";
                        if($resposta['isReaction']) {
                          echo "<b><i>reagiu</i></b> com ";
                          switch ($resposta['emote']){
                            case 'curtir':
                              echo "👌";
                              break;
                            case 'kkk':
                              echo "🤣";
                              break;
                            case 'amei':
                              echo "❤️";
                              break;
                            case 'grr':
                              echo "🤬";
                              break;
                            case 'wow':
                              echo "🤯";
                              break;
                            case 'sad':
                              echo "😭";
                              break;                  
                          }
                          echo ", ";
                        }
                        $isMentioned2 = 0;
                        if(count($resposta['citacoes']) > 0) {
                          $tmpCitacoes = [];
                          foreach ($resposta['citacoes'] as $pessoa) {
                            $tmpCitacoes[] = "@".$pessoa['nomePerfil'];
                            if($pessoa['codPerfil'] == $_SESSION['userid'] && $resposta['codPerfil'] != $_SESSION['userid']) $isMentioned2 = 1;
                          }
                          
                          $tmpCitacoes = implode(',',$tmpCitacoes);
                          echo "<b><i>marcando</i></b> <i title=\"".$tmpCitacoes."\">";
                          if(strlen($tmpCitacoes) > 10){
                            $tmpCitacoes = substr($tmpCitacoes, 0, 7);
                            echo $tmpCitacoes."...";
                          } else {
                            echo $tmpCitacoes;
                          }
                          echo ", </i>";
                        }
                        if(count($resposta['assuntos']) > 0) {
                          $tmpAssuntos = [];
                          foreach ($resposta['assuntos'] as $assunto) {
                            $tmpAssuntos[] = $assunto['nomeAssunto'];
                          }
                          
                          $tmpAssuntos = implode(', ',$tmpAssuntos);
                          echo "com os <b><i>assuntos</i></b> <i title=\"".$tmpAssuntos."\">";
                          if(strlen($tmpAssuntos) > 10){
                            $tmpAssuntos = substr($tmpAssuntos, 0, 7);
                            echo $tmpAssuntos."...";
                          } else {
                            echo $tmpAssuntos;
                          }
                          echo ", </i>";
                        }
                        echo ($resposta['textoPost'] ? $resposta['textoPost'] : '');
                        echo ", em ";
                        if($resposta['nomeCidade']){
                          echo $resposta['nomeCidade'].", ".$resposta['nomePais']." - ";
                        }
                        $tmpHora = explode(' ', $resposta['dataPost'])[1];
                        $tmpData = explode(' ', $resposta['dataPost'])[0];
                        $tmpData = explode('-', $tmpData);
                        echo " ".$tmpData[2]."/".$tmpData[1]."/".$tmpData[0]." ".$tmpHora."</p>";
                        echo "</p>";
                      echo "</div>";
                    echo "</div>";
                    echo "<div class=\"comment-reagir\">";
                      echo "<a href=\"/interagir/$resposta[codInteracao]\">Reagir</a>";
                      if($resposta['codPerfil'] == $_SESSION['userid']) {
                        echo "<a href=\"/editarInteracao/$resposta[codInteracao]\"><p class=\"interacao-editar-txt\">- Editar -</p></a>";
                        echo "<form action=\"/delpost\" method=\"post\">";
                        echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">Remover</p></button>";
                        echo "</form>";
                      }
                      if($resposta['codPerfil'] != $_SESSION['userid'] && $comentario['codPerfil'] == $_SESSION['userid']) {
                        echo "<form action=\"/delpost\" method=\"post\">";
                        echo "<button type=\"submit\" name=\"deletePost\" value=\"$resposta[codInteracao]\"><p class=\"interacao-remover-txt\">- Remover</p></button>";
                        echo "</form>";
                      }
                    echo "</div>";
                  echo "</div>";
                }
              }
              if($comentario['qtdInteracao'] > 1){
                echo "<p align=center><a href=/completeInteracao/$comentario[codInteracao]>Ver mais respostas</a></p>";
              }
            echo "</div>";
          }
        }
        if($post['qtdInteracao'] > 0){
            echo "<hr class=\"post-hr-gray\">";
            echo "<p align=center ><a href=/completeInteracao/$post[codInteracao] style=\"txt-verMaisComentarios\">Ver mais</a></p>";
        }
      echo "</div>";
    }
}
?>