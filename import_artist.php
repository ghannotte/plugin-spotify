<?php

require_once 'import_album.php';

//Envoi de la saisie utlisateur de l'artiste dans sqlite
function import_artist()
{
    //Récupération des données de l'url
    $ = $_GET['id'];
    $nom = $_GET['nom'];
    $url = $_GET['url'];

    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $date = date('d-m-y');
    $db->exec("INSERT INTO  artist VALUES('$id', '$nom', '$url', '$date')");//Envoi des données dans la table "artist" de sqlite
    if (!isset($_GET['query_artist_other'])) {//Si dans l'url n'est pas présent "query_artist_other", nous redirigeons vers la page de l'artiste
        $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom=' . $nom . '&id=' . $id . '&url=' . $url . '&display_artist=null' ;
        echo '<script type="text/javascript">','window.location.replace("http://localhost' . $url . '");','</script>';
    } elseif (isset($_GET['query_artist_other'])) {//Si dans l'url est présent "query_artist_other", nous redirigeons vers la selection d'album ou de musique 
        shearch_album_artist($id);
    }
}

?>