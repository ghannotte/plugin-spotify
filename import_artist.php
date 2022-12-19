<?php
require_once 'import_album.php';
function import_artist(){;//envoie de la selection utlisateur dans sqlite pour l'artiste
$id= $_GET['id'];;//récupération des données de l'url
$nom= $_GET['nom'];
$url= $_GET['url'];


$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');

$db->exec("INSERT INTO  artist VALUES('$id', '$nom','$url')");//envoie des données dans la base artiste de sqlite
if((!isset($_GET['import_album']))&&(!isset($_GET['query_artist_other']))){
$url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$nom.'&id='.$id.'&url='.$url.'&display_artist=null' ;
echo '<script type="text/javascript">',
'window.location.replace("http://localhost'.$url.'");',
 '</script>'
;
///echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//redirection vers la page de l'artiste
}elseif((!isset($_GET['import_album']))&&(isset($_GET['query_artist_other']))){
    shearch_album_artist($id);
}
}

?>