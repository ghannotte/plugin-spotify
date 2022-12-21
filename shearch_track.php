<?php
include_once 'vendor/autoload.php';
include_once 'refreshtoken.php';
include_once 'artist_page.php';


function shearch_track_album($id){
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); 
    $sql = $db->query("SELECT * FROM track WHERE id_album='$id'");
    $result = $sql->fetchArray(SQLITE3_NUM);
        if ($result) {
            $sql = $db->query("SELECT * FROM album WHERE id_album= '$id'");
            $result = $sql->fetchArray(SQLITE3_NUM);
            $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$result[2].'&id='.$result[1].'&url='.$result[3].'&display_album=null&id_artist='.$result[0].'' ;
            echo '<script type="text/javascript">',
            'window.location.replace("http://localhost'.$url.'");',
             '</script>'
            ;
        }
        elseif (!$result) { 
            echo "recherche dans spotify";
            shearch_album_track_spotify($id);
        }

    } ///getAlbum


function shearch_album_track_spotify($id){
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    echo"Recherche dans spotify ";
    $tracks = $api->getAlbumTracks($id);
    //$first= $track->items;  //récupération de la liste des album
    foreach ($tracks->items as $track) {
        echo '<b>' . $track->name . '</b> <br>';
        $nom_track=$track->name;
        $id_track=$track->id;
        $re= $db->query("SELECT * FROM album WHERE id_album='$id'");
        $artist = $re->fetchArray(SQLITE3_NUM);
        $db->exec("INSERT INTO  track VALUES('$artist[0]','$id', '$id_track','$nom_track')");
    }

}
function shearch_track_artist($id_artist){

    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); 
    //$sql = $db->query("SELECT nom_track, FROM track as T INNER JOIN album as A ON A.id_album = T.id_album WHERE A.id_artist = '$id_artist' ");
    $sql = $db->query("SELECT * FROM track WHERE id_artist = '$id_artist' ");
    $result = $sql->fetchArray(SQLITE3_NUM);
    if($result){

        while ($row = $sql->fetchArray()) {
            echo '<p>'.$row['nom_track'].'</p>';
            
        }

    }elseif(!$result){
        echo 'ko';
    }
}
?>