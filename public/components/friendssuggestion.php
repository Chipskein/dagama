<?php
if(count($suggestFriends) > 0)
{
    echo "<div class=\"add-amigo\">";
        echo "<div class=\"add-amigo-top\">";
            echo "<p class=\"add-amigo-suggesTxt\">Sugestão de amigos:</p>";
            echo "<a class=\"add-amigo-verMais\" href=\"/verMaisSugestoes\">VER MAIS</a>";
        echo "</div>";
        echo "<div class=\"add-amigo-cards\">";
            foreach ($suggestFriends as $person) {
            echo "<div id=\"card".$person['codigo']."\" class=\"add-amigo-card\">";
                echo "<a href=navio.php?user=$person[codigo]><img class=\"add-amigo-card-icon\" src=\"".$person['img']."\" alt=\"\" srcset=\"\"></a>";
                echo "<p class=\"add-amigo-card-name\">".$person['username']."</p>";
                echo "<form action=\"/friendrequest\" method=\"POST\" >";
                    if($person['enviado'] == 'true' || (isset($_POST['sendFriendRequest']) && $_POST['sendFriendRequest'] == $person['codigo']))
                    {
                        echo "<input type=\"hidden\" name=\"unsendFriendRequest\" value=\"".$person['codigo']."\" />";
                        echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button-selected\" disabled  type=\"submit\" value=\"Enviado\" />";  
                    }
                    if($person['recebido'] != 'true' && $person['enviado'] != 'true' && (isset($_POST['sendFriendRequest']) ? $_POST['sendFriendRequest'] != $person['codigo'] : true)) 
                    {
                        echo "<input type=\"hidden\" name=\"sendFriendRequest\" value=\"".$person['codigo']."\" />";
                        echo "<input id=\"cardInput".$person['codigo']."\" class=\"add-amigo-card-button\"  type=\"submit\" onclick=\"
                        let cardInput = document.getElementById('cardInput'+".$person['codigo'].");
                        cardInput.className = 'add-amigo-card-button-selected'; cardInput.value = 'Enviado'";
                        echo "\" value=\"Adicionar\" />";
                    }
                echo "</form>";
            echo "</div>";        
            }
        echo "</div>";
    echo "</div>";
}
else
{
    echo "Sem sugestões de amigos";
}

?>