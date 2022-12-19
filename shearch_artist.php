<?php
require 'vendor/autoload.php';
require 'refreshtoken.php';
require 'artist_page.php';

function get_id_artist($name) { //cette fonction permet de recherche un artiste dans spotify, afficher les résultats retourné par spotify et porposer la selection à l'utilisateur
    $token=get_token();
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $api->setAccessToken($token);
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    echo"Recherche dans spotify ";
        $name = preg_replace('/\s+/', '+', $name);
        $artist = $api->shearch_artiste($name); //recherche de m'artiste via l'api. La fonction shearch_artiste n'était pas présente dans la librairie, nous l'avons devellope
        $first= $artist->artists->items;  //récupération de la liste des artiste
        for($i = 0; $i < sizeof($first) ;$i++){//pour chaque artiste de la liste
            $id=$first[$i]->id; //je récupére l'id de l'artiste
            $nom=$first[$i]->name;//je récupére du nom de l'artiste
            $images=$api->getArtist($id);//je récupére l'artiste via son $id en passant pour l'api afin de récupérer son image (la requéte $api->shearch_artiste ne retourne pas d'image)
            $url=$images->images[0]->url;//je récupére l'url de l'image
            echo $nom .' ' ;
            if($url){ //juste test si l'artiste à bien une image
            $image = base64_encode(file_get_contents($url));//je récuépére l'image dériére l'url

            echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom='. $nom .'&id='. $id .'&url='. $url .'&import_artiste=null"><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
            }//j'affiche le résultat, l'utilisateur pourra valider le résultat grace au lien'
        }
}


function shearch_artist($name) { //cete fonction recherche la présense d'un artiste dans la base
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $db->exec('PRAGMA foreign_keys = ON;');
    $db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT)');


        $sql = $db->query("SELECT * FROM artist WHERE nom_artist= '$name'"); //recherche de l'artiste
        $result = $sql->fetchArray(SQLITE3_NUM);

            if ($result) { //si trouvé
                echo"Recherche dans sqlite";
                $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$result[1].'&id='.$result[0].'&url='.$result[2].'&display_artist=null' ;
                echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//modifation de la la page pour afficher l'artiste
   
            }
            if (!$result) {//si no trouvé
                $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$name.'&query_artist=null' ;
                echo '<meta http-equiv="Refresh" content="0; url='.$url.'>';//modifation de la la page pour chercher l'artiste dans spotify
            
            }
        }  

?>