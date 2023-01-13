<?php

include_once 'vendor/autoload.php';
include_once 'refreshtoken.php';
include_once 'artist_page.php';

function getIdArtist($name)
{
    // Cette fonction permet de rechercher un artiste sur Spotify, afficher les résultats retournés par Spotify et proposer la sélection à l'utilisateur.
    $token = getToken();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    echo "Recherche dans Spotify";

    $name = preg_replace('/\s+/', '+', $name);
    $artist = $api->searchArtist($name); //Recherche de l'artiste via l'API. La fonction searchArtist n'était pas présente dans la bibliothèque, nous l'avons développée
    $first = $artist->artists->items; //Récupération de la liste des artistes
    for ($i = 0; $i < sizeof($first); $i++) { //Pour chaque artiste dans la liste
        $id = $first[$i]->id; //Récupération l'id de l'artiste
        $nom = $first[$i]->name; //Récupération du nom de l'artiste
        $images = $api->getArtist($id); //Récupération de l'artiste via son id en utilisant l'API pour récupérer son image (la requête $api->searchArtist ne retourne pas d'image)
        $url = $images->images[0]->url; //Récupération de l'url de l'image
        echo $nom . ' ';
        if ($url) { //Test pour vérifier si l'artiste a une image
            $image = base64_encode(file_get_contents($url)); //Récupération l'image derrière l'url
            if (isset($_GET['query_artist_other'])) { //Redirection vers la page pour importer l'artiste. Detection de si 'query_artist_other' est présent dans l'url afin de l'ajouter à la redirection
                echo '<a href="' . $_SERVER['PHP_SELF'] . '?page=my-plugin&nom=' . $nom . '&id=' . $id . '&url=' . $url . '&import_artist=null&query_artist_other=null"><img width="50" src="data:image/jpeg;base64,' . $image . '"></a>';
            } else {
            echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom='. $nom .'&id='. $id .'&url='. $url .'&import_artiste=null"><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';}
            }//Affichage du résultat, l'utilisateur pourra valider le résultat grâce au lien'
        }
}

function searchArtist($name, $method, $type)
{

    //Cette fonction recherche la présence d'un artiste dans la BDD
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $db->exec('PRAGMA foreign_keys = ON;');
    $db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT,fraicheur DATE)');

    $sql = $db->query("SELECT * FROM artist WHERE nom_artist= '$name'"); //Recherche d'un artiste dans la table
    $result = $sql->fetchArray(SQLITE3_NUM);
    //Direct signifit que l'utilisateur a cherché directement un artiste
    //Indrect signifit que l'utilisateur a cherché un album ou une musique avec le nom d'un artiste
    if (($result) && ($method == 'direct')) { //Si on trouve et direct
        echo "Searching in SQLite";
        $url = $_SERVER['PHP_SELF'] . '?page=my-plugin&nom=' . $result[1] . '&id=' . $result[0] . '&url=' . $result[2] . '&display_artist=null'; //Remplacement de la page pour faire une recherche d'artiste
        echo '<script type="text/javascript">',
            'window.location.replace("http://localhost' . $url . '");',
            '</script>';
    }
    if ((!$result) && ($method == 'direct')) { //Si on ne trouve pas et direct
        //Remplacement de la page pour faire une recherche d'artiste
        $url = $_SERVER['PHP_SELF'] . '?page=my-plugin&nom=' . $name . '&query_artist=null';
        echo '<script type="text/javascript">',
            'window.location.replace("http://localhost' . $url . '");',
            '</script>';
    }
    if ((!$result) && ($method == 'indirect')) { //Si on ne trouve pas et indirect
        echo 'ok';
        $url = $_SERVER['PHP_SELF'] . '?page=my-plugin&nom=' . $name . '&query_artist=null&query_artist_other=' . $type . '';
        //Remplacement de la page pour faire une recherche d'artiste et ajouter 
        //&query_artist_other dans l'url pour faire un recherche d'album ou de titre
        echo '<script type="text/javascript">',
            'window.location.replace("http://localhost' . $url . '");',
            '</script>';
    }
    if (($result) && ($method == 'indirect')) { //Si on trouve et indirect
        if ($type == "album") {
            searchAlbumArtist($result[0]);
        } elseif ($type == "track") {
            searchTrackArtist($result[0]); //Redirection vers l'album de l'artiste       
        }
    }
}

function searchArtistBack($id)
{
    //Cette fonction recherche la présence d'un artiste dans la base de données et l'insère directement s'il n'est pas trouvé.
    //Cette fonction est exécutée uniquement lorsque l'utilisateur selectionne un album ou une musique et non directement un artiste.
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT, fraicheur DATE)');
    //Si l'utilisateur sélectionne directement un album ou une musique lors de sa première utilisation, il voudra ensuite insérer dans la table artiste, donc la table est créée si elle n'existe pas.

    $sql = $db->query("SELECT * FROM artist WHERE id_artist= '$id'"); // Recherche de l'artiste
    $result = $sql->fetchArray(SQLITE3_NUM);

    if (!$result) {
        //Si non trouvé
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $token = getToken();
        $api->setAccessToken($token);
        $artist = $api->getArtist($id);
        $name = $artist->name;
        $url = $artist->images[0]->url;
        if (!$url) {
            //Il peut arriver que l'artiste n'ait pas d'image, dans ce cas, on met la variable url à "null"
            $url = 'NULL';
        }
        $date = date('d-m-y');
        $db->exec("INSERT INTO artist VALUES('$id', '$name','$url','$date')");
        return $id;
    }

    return $result[0];
}