<?php
include_once 'vendor/autoload.php';
include_once 'refreshtoken.php';
include_once 'artist_page.php';
include_once 'track_page.php';
include_once 'track_page.php';
include_once 'shearch_artist.php';


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

    } 
    
    
    function find_track_album($nom_album,$nom_track){
        $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); 
        $sql = $db->query("SELECT * FROM album WHERE nom_album='$nom_album'");
        $result = $sql->fetchArray(SQLITE3_NUM);
            if ($result) {
                $sql2 = $db->query("SELECT * FROM track WHERE nom_track='$nom_track'");
                $result2 = $sql2->fetchArray(SQLITE3_NUM);
                    if ($result2) {
                        display_track_page($nom_track,$result[0]);
                    }elseif (!$result2) { 
                        shearch_track_album($nom_album);
                    }                
            }
            elseif (!$result) { 
                shearch_album($album);
            }    
        }    ///getAlbum


function shearch_album_track_spotify($id){
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    echo"Recherche dans spotify ";
    $tracks = $api->getAlbumTracks($id);
    $date = date('d-m-y');
    $re= $db->query("SELECT * FROM album WHERE id_album='$id'");
    $artist = $re->fetchArray(SQLITE3_NUM);
    foreach ($tracks->items as $track) {
        //echo '<b>' . $track->name . '</b> <br>';
        $nom_track=$track->name;
        $id_track=$track->id;
        $url=$track->preview_url;
        $db->exec("INSERT INTO  track VALUES('$artist[0]','$id', '$id_track','$nom_track','$url','$date')");
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
        echo "titre nom trouver,";
        shearch_album_artist_on_spotify($result[0]);
    }
}

function shearch_track($track){

    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); 
    //$sql = $db->query("SELECT nom_track, FROM track as T INNER JOIN album as A ON A.id_album = T.id_album WHERE A.id_artist = '$id_artist' ");
    $sql = $db->query("SELECT * FROM track WHERE nom_track = '$track' ");
    $result = $sql->fetchArray(SQLITE3_NUM);
    if($result){
        display_track_page($track,$result[0]);
    }elseif(!$result){

        shearch_track_spotify($track);
    }
}

 function shearch_track_spotify($track){

     $token=get_token();
     $api = new SpotifyWebAPI\SpotifyWebAPI();
     $api->setAccessToken($token);
     $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
         $name = preg_replace('/\s+/', '+', $track);
         $album = $api->shearch_track($name); //recherche de l'abum via l'api. La fonction shearch_album n'était pas présente dans la librérie, nous l'avons devellope
         $first= $album->tracks->items;  //récupération de la liste des album
         for($i = 0; $i < sizeof($first) ;$i++){//pour chaque album de la liste
             $nom_album=$first[$i]->album->name;
             echo '<p>nom track :' . $first[$i]->name . '</p>';
             echo '<p>nom album :' .  $nom_album  . '</p>';
             echo '<p>nom artiste :' . $first[$i]->album->artists[0]->name . '</p>';
             $id_artist=$first[$i]->album->artists[0]->id;
             $id_album=$first[$i]->album->id;
             $url=$first[$i]->album->images[0]->url;
              if($url){ //je test si l'artiste à bien une image
                  $image = base64_encode(file_get_contents($url));//je récuépére l'image dériére l'url
                  echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom='. $nom_album .'&id_artist='. $id_artist .'&nom_track='. $track .'&id='. $id_album .'&url='. $url .'&import_track=null."><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
              }//j'affiche le résultat, l'utilisateur pourra valider l'album dans la selection généré en cliquant sur le lien'
         }
     }
    
function shearch_one_track_artist($artist,$track){
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT * FROM artist WHERE nom_artist ='$artist'");
    $result = $sql->fetchArray(SQLITE3_NUM);
    if($result){
        $sql2 = $db->query("SELECT * FROM  track WHERE nom_track ='$track'");
        $result2 = $sql2->fetchArray(SQLITE3_NUM);

            if($result2){
                display_track_page($track,$result[0]);
                }elseif(!$result2){
                    echo "titre nom trouver,";
                    shearch_album_artist_on_spotify($result[0]);    
                }
        
    elseif(!$result){
        echo "artiste nom trouvé";
        shearch_artist($artist,'indirect','track');
        }
    }

}
?>