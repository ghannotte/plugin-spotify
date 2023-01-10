<?php
require_once 'import_album.php';
function import_artist(){;//envoie de la selection utlisateur dans sqlite pour l'artiste
$id= $_GET['id'];;//récupération des données de l'url
$nom= $_GET['nom'];
$url= $_GET['url'];


$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');

$db->exec("INSERT INTO  artist VALUES('$id', '$nom','$url')");//envoie des données dans la base artiste de sqlite
if(!isset($_GET['query_artist_other'])){///si dans l'url il n'y a pas query_artist_other je redirige vers la page de l'artiste
$url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$nom.'&id='.$id.'&url='.$url.'&display_artist=null' ;
$db->close();
echo '<script type="text/javascript">',
'window.location.replace("http://localhost'.$url.'");',
 '</script>'
;

}elseif(isset($_GET['query_artist_other'])){///si dans l'url il y a query_artist_other je redirige vers la selection d'album ou de musique 
    $db->close();
    shearch_album_artist($id);
    
}
}

?>