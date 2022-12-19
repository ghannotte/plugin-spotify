<?php
include_once  'shearch_artist.php';
require 'vendor/autoload.php';
include_once 'refreshtoken.php';


function get_album_artist($name) {
    $db = new SQLite3('spotify.db');
    $db->exec('PRAGMA foreign_keys = ON;');
    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($token);
        $id=shearch_artist($name);  
        $albums = $api->getArtistAlbums($id);
        foreach ($albums->items as $album) {
            $db->exec("INSERT INTO  album VALUES('$id', '$album->id','$album->name')");
        }   
}    
function shearch_album($artist){
    $db = new SQLite3('spotify.db'); 
    $sql = $db->query("SELECT nom_album, FROM album as L INNER JOIN artist as A ON A.id_artist = L.id_artist WHERE A.nom_artist = ''$artist' ");
    //shearch_artist('Justice');    
      $result = $query->fetchArray(SQLITE3_NUM);
        if ($result) {
            while ($row = $sql->fetchArray()) {
                var_dump($row);
            }
        }
        if (!$result) { 
            get_album_artist($artist);
        }
}


//get_album('Olatu','thylacine')

?>