<?php
require_once 'import_album.php';
include_once 'shearch_artist.php';
function import_track(){;//envoie de la selection utlisateur dans sqlite pour l'artiste
$id_album= $_GET['id'];;//récupération des données de l'url
$nom_track= $_GET['nom_track'];
$nom_album= $_GET['nom'];
$id_artist= $_GET['id_artist'];
$url=$_GET['url'];

$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
$date = date('d-m-y');
$db->exec("INSERT INTO  album VALUES('$id_artist','$id_album', '$nom_album','$url','$date')");

shearch_artist_back($id_artist); ///j'en profite pour insérer l'atiste s'il ne l'a pas déja été
shearch_track_album($id); ///j'en profite pour insérer les titres de l'album

display_track_page($nom_track,$id_artist);

}

?>