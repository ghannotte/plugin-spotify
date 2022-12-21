<?php
include_once 'shearch_artist.php';
function import_album(){;//envoie de la selection utlisateur dans sqlite pour l'album
$id= $_GET['id'];;//récupération des données de l'url
$nom= $_GET['nom'];
$url= $_GET['url'];
$id_artist= $_GET['id_artist'];


$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');

$db->exec("INSERT INTO  album VALUES('$id_artist','$id', '$nom','$url')");//envoie des données dans la base artiste de sqlite

$url=$_SERVER['PHP_SELF'].'?page=my-plugin&id_artist='.$id_artist.'&nom='.$nom.'&id='.$id.'&url='.$url.'&display_album=null' ;
//echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//redirection vers la page de l'artiste

shearch_artist_back($id_artist); ///j'en profite pour insérer l'atiste s'il ne l'a pas déja été
shearch_track_album($id); ///j'en profite pour insérer les titres de l'album
echo '<script type="text/javascript">',
'window.location.replace("http://localhost'.$url.'");',
 '</script>'
;
}

?>