<?php

include_once 'shearch_artist.php';

function import_album()
{
    $id = $_GET['id'];
    $nom = $_GET['nom'];
    $url = $_GET['url'];
    $id_artist = $_GET['id_artist'];

    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $date = date('d-m-y');
    $db->exec("INSERT INTO  album VALUES('$id_artist','$id', '$nom','$url','$date')");//envoie des données dans la base artiste de sqlite

    $url=$_SERVER['PHP_SELF'].'?page=my-plugin&id_artist=' . $id_artist . '&nom=' . $nom . '&id=' . $id . '&url=' . $url . '&display_album=null';
    //echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//redirection vers la page de l'artiste

    shearch_artist_back($id_artist);//On insère l'artiste si il n'est pas présent dans la BDD
    shearch_track_album($id);       //On insère les titres de l'album si il ne sont pas présents dans la BDD

    echo '<script type="text/javascript">','window.location.replace("http://localhost' . $url . '");','</script>';
}

?>