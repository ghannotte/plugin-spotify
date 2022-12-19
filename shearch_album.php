<?php
include_once  'shearch_artist.php';
include_once 'vendor/autoload.php';
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

function shearch_album_sans_artist($album){//cette fonction permet de recherche un album dans spotify, afficher les résultats retourné par spotify et porposer la selection à l'utilisateur
$token=get_token();
$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($token);
$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $name = preg_replace('/\s+/', '+', $album);
    echo $name;
    $album = $api->shearch_album($name); //recherche de l'abum via l'api. La fonction shearch_album n'était pas présente dans la librérie, nous l'avons devellope
    $first= $album->albums->items;  //récupération de la liste des album
    for($i = 0; $i < sizeof($first) ;$i++){//pour chaque album de la liste
        $id=$first[$i]->id; //je récupére l'id de l'album
        $nom=$first[$i]->name;//je récupére du nom de l'album
        $images=$api->getAlbum($id);//je récupére des infos complémentaire avec cette fonction comme son image (la fonction $api->shearch_album ne retourne pas d'image)
        $url=$images->images[0]->url;//je récupére l'url de l'image
        $id_artist=$images->artists[0]->id;//je récupére l'id de l'artiste
        $name_artist=$images->artists[0]->name;//je récupére le nom de l'artiste
        echo 'nom artist:'. $name_artist . ' ';
        if($url){ //je test si l'artiste à bien une image
        $image = base64_encode(file_get_contents($url));//je récuépére l'image dériére l'url
        echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&id_artist='. $id_artist .'&nom='. $nom .'&id='. $id .'&url='. $url .'&import_album=null."><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
        }//j'affiche le résultat, l'utilisateur pourra valider l'album dans la selection généré en cliquant sur le lien'
    }

}

function shearch_album($album){
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); 
    $db->exec('PRAGMA foreign_keys = ON;'); 
    $db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT ,id_album TEXT PRIMARY KEY , nom_album TEXT, uri TEXT)');
    $sql = $db->query("SELECT * FROM album WHERE nom_album= '$album'");
    $result = $sql->fetchArray(SQLITE3_NUM);
        if ($result) {
            $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$result[2].'&id='.$result[1].'&url='.$result[3].'&display_album=null&artiste='.$result[0].'' ;
            ///echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//modifation de la la page pour afficher l'artiste
            echo '<script type="text/javascript">',
            'window.location.replace("http://localhost'.$url.'");',
             '</script>'
            ;
        }
        if (!$result) { 
            echo "recherche dans spotify";
            shearch_album_sans_artist($album);
        }
    
}


function shearch_album_artist($id){
   
    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token); 
    $album=$api->getArtistAlbums($id);
    $first= $album->items;
    for($i = 0; $i < sizeof($first) ;$i++){//pour chaque album de la liste
        $id=$first[$i]->id; //je récupére l'id de l'album
        $nom=$first[$i]->name;//je récupére du nom de l'album
        $images=$api->getAlbum($id);//je récupére des infos complémentaire avec cette fonction comme son image (la fonction $api->shearch_album ne retourne pas d'image)
        $url=$images->images[0]->url;//je récupére l'url de l'image
        $id_artist=$images->artists[0]->id;//je récupére l'id de l'artiste
        $name_artist=$images->artists[0]->name;//je récupére le nom de l'artiste
        echo 'nom artist:'. $name_artist . ' ';
        if($url){ //je test si l'artiste à bien une image
        $image = base64_encode(file_get_contents($url));//je récuépére l'image dériére l'url
        echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&id_artist='. $id_artist .'&nom='. $nom .'&id='. $id .'&url='. $url .'&import_album=null."><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
        }//j'affiche le résultat, l'utilisateur pourra valider l'album dans la selection généré en cliquant sur le lien'
    }


}


//get_album('Olatu','thylacine')

?>