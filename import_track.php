<?php
require_once 'import_album.php';
require_once 'track_page.php';
function import_track(){;//envoie de la selection utlisateur dans sqlite pour l'artiste
$id_album= $_GET['id'];;//récupération des données de l'url
$nom_track= $_GET['nom_track'];
$nom_album= $_GET['nom'];
$id_artist= $_GET['id_artist'];
$url=$_GET['url'];


shearch_artist_back($id_artist); ///j'en profite pour insérer l'atiste s'il ne l'a pas déja été
shearch_track_album($id_album); ///j'en profite pour insérer les titres de l'album

display_track_page($nom_track,$id_artist);

}

?>