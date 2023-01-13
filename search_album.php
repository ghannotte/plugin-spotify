<?php

include_once 'search_artist.php';
include_once 'vendor/autoload.php';
include_once 'refreshtoken.php';
include_once 'album_page.php';

function searchAlbumSansArtist($album)
{
    //Permet de recherche un album dans spotify, afficher les résultats retournés par spotify
    //et proposer la selection à l'utilisateur
    $token = getToken();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $name = preg_replace('/\s+/', '+', $album);
    echo $name;
    $album = $api->searchAlbum($name); //Recherche de l'album via l'API. La fonction n'était pas présente dans la librairie
    $first = $album->albums->items; //Récupération de la liste des albums
    for ($i = 0; $i < sizeof($first); $i++) { //Pour chaque album de la liste
        $id = $first[$i]->id; //Récupération de l'id de l'album
        $nom = $first[$i]->name; //Récupération du nom de l'album
        $images = $api->getAlbum($id); //Récupération d'infos complémentaires avec cette fonction car la fonction $api->searchAlbum ne retourne pas l'image de l'album
        $url = $images->images[0]->url; //Récupération de l'url de l'image
        $id_artist = $images->artists[0]->id; //Récupération de l'id de l'image
        $name_artist = $images->artists[0]->name; //Récupération du nom de l'artiste
        echo 'nom artist:' . $name_artist . ' ';
        if ($url) { //Test pour vérifier si l'artiste possède une image
            $image = base64_encode(file_get_contents($url)); //Récupération de l'image
            echo '
            <a href="' . $_SERVER['PHP_SELF'] . '?page=my-plugin&id_artist=' . $id_artist . 
            '&nom=' . $nom . '&id=' . $id . '&url=' . $url . '&import_album=null.">
                <img width="50" src="data:image/jpeg;base64,' . $image . '">
            </a>';
        }
    }
}

function searchAlbum($album)
{
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT * FROM album WHERE nom_album='$album'");
    $result = $sql->fetchArray(SQLITE3_NUM);
    if ($result) {
        $url = $_SERVER['PHP_SELF'] . '?page=my-plugin&nom=' . $result[2] . '&id=' . $result[1] . '&url=' . $result[3] . '&display_album=null&id_artist=' . $result[0];
        echo '
        <script type="text/javascript">',
            'window.location.replace("http://localhost'.$url.'");',
        '</script>';
    }
    if (!$result) {
        echo "Recherche dans spotify";
        searchAlbumSansArtist($album);
    }
}
 
function searchAlbumArtist($id)
{
    //Cette fonction est utilisée quand l'utilisateur veut chercher un album par artiste
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT * FROM album WHERE id_artist='$id'");
    $result = $sql->fetchArray(SQLITE3_NUM);
    if ($result) {
        displayAlbumAllPage($id); //Lancement de la fonction d'affichage si l'artiste est dans la table album
    } else {
        searchAlbumArtistOnSpotify($id); //Sinon on propose à l'utilisateur d'aller la chercher
    }
}

function searchAlbumAndArtist($artist, $album)
{
    //Cette fonction est utilisée quand l'utilisateur veut chercher un album par artiste
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT * FROM artist WHERE nom_artist='$artist'");
    $result = $sql->fetchArray(SQLITE3_NUM);
 
    if ($result) {
        $sql2 = $db->query("SELECT * FROM album WHERE nom_album='$album'");
        $result2 = $sql2->fetchArray(SQLITE3_NUM);
        if ($result2) {
            echo 'ko';
            $url = $_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$result2[2].'&id='.$result2[1].'&url='.$result2[3].'&display_album=null&id_artist='.$result2[0];
            echo '
            <script type="text/javascript">',
                'window.location.replace("http://localhost'.$url.'");',
            '</script>';
        } else {
            
            searchAlbumArtistOnSpotify($result[0]); //Si l'artiste est dans la table album, lancement de la fonction d'affichage de la liste
        }
    } else {
        searchArtist($artist, 'indirect', 'album'); //Sinon on propose à l'utilisateur d'aller la chercher
    }
}

function searchAlbumArtistOnSpotify($id)
{
    $token = getToken();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    $album = $api->getArtistAlbums($id); //Recherche des albums d'un artiste via l'API.
    $first = $album->items;
    for ($i = 0; $i < sizeof($first); $i++) { //Pour chaque album de la liste
        $id = $first[$i]->id; //Récupération de l'id de l'album
        $nom = $first[$i]->name; //Récupération du nom de l'album
        $images = $api->getAlbum($id); //Récupération d'infos complémentaires comme l'image avec cette fonction car la fonction $api->searchAlbum ne retourne pas l'image de l'album
        $url = $images->images[0]->url; //Récupération de l'url de l'image
        $id_artist = $images->artists[0]->id; //Récupération de l'id de l'artiste
        $name_artist = $images->artists[0]->name;
        echo '<p>Nom de l\'artiste : '. $name_artist . ' </p>';
        echo '<p>Nom de l\'album : '. $nom . ' </p>';
        if ($url) { //Test si l'image existe
            $image = base64_encode(file_get_contents($url));
            echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&id_artist='. $id_artist .'&nom='. $nom .'&id='. $id .'&url='. $url .'&import_album=null."><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
            //Affichage du résultat avec possibilité de cliquer sur la pochette de l'album
        }
    }
}