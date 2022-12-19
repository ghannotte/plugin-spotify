<?php
function display_artist_page(){ /// cette fonction affiche les informations de lartiste selon les informatons de l'url
$id= $_GET['id'];//récupération des données de l'url
$nom= $_GET['nom'];
$url= $_GET['url'];
echo $nom .' ' ;
$image = base64_encode(file_get_contents($url));//récupération récupétation de l'image de l'artiste
echo '<img width="50" src="data:image/jpeg;base64,'.$image.'">';//affichage de l'image
}

?>