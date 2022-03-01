<?php
echo "<header class=header-main>";
    echo "<img class=\"header-icon\" src=\"/imgs/icon.png\">";
    echo "<form class=\"header-searchBar\" name=\"search\" action=\"/search\" method=\"GET\">";
        echo "<input class=\"header-searchBar-input\" name=\"searchTerm\" type=\"text\" placeholder=\"Pesquisar portos e usuarios\" />";
        echo "<button type='submit'><img class=\"header-searchBar-icon\" src=\"/imgs/icons/search.png\" ></button>";
    echo "</form>";
    echo "<div class=\"header-links\">";
    echo "<a class=\"header-links-a\" href=/feed >Mar</a> ";
    echo "<a class=\"header-links-a\" href=/mar >Portos</a> ";
    echo "<a class=\"header-links-a\" href=/navio/$_SESSION[userid] >Meu navio</a> ";
    echo "<a class=\"header-links-a\" href=/logoff >Sair </a><img class=\"header-links-icon\" src=\"/imgs/icons/sair.png\" alt=\"\">";
    echo "</div>";
echo "</header>";

?>