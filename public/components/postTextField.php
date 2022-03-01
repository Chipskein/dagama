<?php
    echo "<div class=\"insert-interacao\">";
    echo "<div class=\"insert-interacao-user\">";
      echo "<img class=\"interaction-mainuser-user-icon\" src=\"".$user["img"]."\" alt=\"\" srcset=\"\">";
      echo "<div>";
        echo "<p class=\"insert-interacao-user-name\">".$user["username"].":</p>";
        echo "<p class=\"insert-interacao-user-assuntos\"></p>";
      echo "</div>";
    echo "</div>";
    echo "<form name=\"newPost\" action=\"feed.php?user=$_SESSION[userid]\" method=\"post\" >";
      echo "<textarea name=\"texto\" class=\"insert-interacao-input\" id=\"insert-interacao-input\" type=\"text\" placeholder=\"Escreva um post ...\" ></textarea>";
      echo "<div class=\"insert-interacao-smallBtns\">";
        echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('local')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"/imgs/icons/maps-and-flags.png\" alt=\"\" srcset=\"\">Adicionar um Local</div>";
        echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('pessoas')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"/imgs/icons/multiple-users-silhouette.png\" alt=\"\" srcset=\"\">Citar Pessoas</div>";
        echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('assuntos')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"/imgs/icons/price-tag.png\" alt=\"\" srcset=\"\">Assunto</div>";
        echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('reacoes')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"/imgs/icons/Like.png\" alt=\"\" srcset=\"\">Reação</div>";
        echo "<div class=\"insert-interacao-smallBtns-a\" onclick=\"newPostSelect('compartilhar')\"><img class=\"insert-interacao-smallBtns-icon\" src=\"/imgs/icons/send.png\" alt=\"\" srcset=\"\">Compartilhar</div>";
      echo "</div>";
      echo "<input class=\"insert-interacao-submit\" type=\"submit\" name=\"novoPost\" />";
      echo "<hr id=\"post-hr\" class=\"post-hr\" >";
      echo "<div class=\"post-divLocal\">";

        echo "<input id=\"insert-codigo-pais\" name=\"insert-codigo-pais\" type=\"hidden\" value=\"\">";
        echo "<input id=\"insert-codigo-estado\" name=\"insert-codigo-estado\" type=\"hidden\" value=\"\">";
        echo "<input id=\"insert-codigo-cidade\" name=\"insert-codigo-cidade\" type=\"hidden\" value=\"\">";
      
        echo "<select id=\"select-pais\" onchange=\"selectPais(this)\">";
          echo "<option value=\"selecionar-pais\">Selecionar Pais</option>";
          foreach ($paises as $value) {
            echo "<option id='optionPais".$value['codigo']."' value='$value[codigo]'>".$value['nome']."</option>\n";
          }
          echo "<option value=\"0\">Outro</option>";
        echo "</select>";
        foreach ($paises as $value) {
          echo "<select id=\"select-estado-pais$value[codigo]\" class=\"select-estado-pais\" style=\"display: none\" onchange=\"selectEstado(this)\">";
            echo "<option value=\"selecionar-estado\">Selecionar Estado</option>";
            foreach ($estados as $value2) {
              if($value2['pais'] == $value['codigo']){
                echo "<option id='optionEstado$value2[codigo]' value='$value2[codigo]'>$value2[nome]</option>\n";
              }
            }
            echo "<option value=\"0\">Outro</option>";
          echo "</select>";
        }
        foreach ($estados as $value) {
          echo "<select id=\"select-cidade-estado$value[codigo]\" class=\"select-cidade-estado\" style=\"display: none\" onchange=\"selectCidade(this)\">";
            echo "<option value=\"selecionar-cidade\">Selecionar Cidade</option>";
            foreach ($cidades as $value2) {
              if($value2['uf'] == $value['codigo']){
                echo "<option id='optionCidade$value2[codigo]' value='{ \"id\": \"".$value2['codigo']."\", \"name\": \"".$value2['nome']."\" }'>$value2[nome]</option>\n";
              }
            }
            echo "<option value='{ \"id\": \"0\", \"name\": \"null\" }'>Outro</option>";
          echo "</select>";
        }
        echo "<input id=\"insert-nome-pais\" name=\"insert-nome-pais\" placeholder=\"Digite o nome do novo pais\" class=hidden>";
        echo "<input id=\"insert-nome-estado\" name=\"insert-nome-estado\" placeholder=\"Digite o nome do novo estado\" class=hidden>";
        echo "<input id=\"insert-nome-cidade\" name=\"insert-nome-cidade\" placeholder=\"Digite o nome da nova cidade \" class=hidden>";
        echo "<button id=\"select-local-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addLocal()\">Confirmar</button>";
        echo "<div class=\"comment-container-top\" id=\"divCidade\"></div>";

      echo "</div>";
      echo "<div class=\"post-divPessoas\">";
        echo "<select id=\"select-pessoas\" onclick=\"unsetError(this)\">";
          foreach ($pessoasArray as $value) {
            echo "<option id='optionPessoa".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['username']."\" }'\">".$value['username']."</option>\n";
          }
        echo "</select>";
        echo "<button id=\"select-pessoa-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addPessoas()\">Confirmar</button>";
        echo "<div class=\"comment-container-top\" id=\"divPessoas\"></div>";
      echo "</div>";
      echo "<div class=\"post-divAssuntos\">";
        echo "<select id=\"select-assuntos\" onchange=\"selectAssunto(this)\">";
          foreach ($assuntosArray as $value) {
            echo "<option id='optionAssunto".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['nome']."\" }'\">".$value['nome']."</option>\n";
          }
          echo "<option value=\"0\">Outro</option>";
        echo "</select>";
        echo "<div id=\"divNewAssuntos\"></div>";
        echo "<input id=\"insert-nome-assunto\" placeholder=\"Digite o nome do novo assunto\" class=hidden>";
        echo "<button id=\"select-assunto-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addAssuntos()\">Confirmar</button>";
        echo "<div class=\"comment-container-top\" id=\"divAssuntos\"></div>";
      echo "</div>";
      echo "<div class=\"post-divReacoes\">";
        echo "<select id=\"select-reacoes\" onclick=\"unsetError(this)\">";
          $reacoesArray = [['codigo'=>'curtir', 'emoji'=>'👌'],['codigo'=>'kkk', 'emoji'=> '🤣'],['codigo'=>'amei', 'emoji'=> '❤️'],['codigo'=>'grr', 'emoji'=> '🤬'],['codigo'=>'wow', 'emoji'=> '🤯'],['codigo'=>'sad', 'emoji'=> '😭']];
          foreach ($reacoesArray as $value) {
            echo "<option id='optionReacao".$value['codigo']."' value='{ \"id\": \"".$value['codigo']."\", \"name\": \"".$value['emoji']."\" }'\">$value[emoji] $value[codigo]</option>\n";
          }
        echo "</select>";
        echo "<button id=\"select-reacao-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addReacoes()\">Confirmar</button>";
        echo "<div class=\"comment-container-top\" id=\"divReacoes\"></div>";
      echo "</div>";
      // echo "<div class=\"post-divCompart\">";
      //   echo "<select id=\"select-compartilhar\" onchange=\"changeCompartilhar(this)\">";
      //     echo "<option id='optionCompartilhar' value=''>Selecione onde vai compartilhar</option>\n";
      //     echo "<option id='optionCompartilhar' value='feed'>No feed</option>\n";
      //     echo "<option id='optionCompartilhar' value='grupo'>Em um grupo</option>\n";
      //     echo "<option id='optionCompartilhar' value='perfil'>Em um perfil</option>\n";
      //   echo "</select>";
      //   echo "<select id=\"select-compartilhar-porto\">";
      //   foreach ($portosArrayForShare as $porto) {
      //     echo "<option id='optionCompartilharPorto' value='$porto[codigo]'>$porto[nome]</option>\n";
      //   }
      //   echo "</select>";
      //   echo "<button id=\"select-reacao-button\"  class=\"confirm-type\" type=\"button\" onclick=\"addReacoes()\">Confirmar</button>";
      //   echo "<div class=\"comment-container-top\" id=\"divReacoes\"></div>";
      // echo "</div>";
    echo "</form>";
  echo "</div>";
?>