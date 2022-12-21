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
            $image = base64_encode(file_get_contents($url));//je récuépére l'image dérriére l'url
            if(isset($_GET['query_artist_other'])){ //je redirige refresh la page pour importer l'artiste. Je detecte si dans l'url il y a 'query_artist_other' afin de rajouter ce champ à la redirection
            echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom='. $nom .'&id='. $id .'&url='. $url .'&import_artiste=null&query_artist_other=null"><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';
              }else{
            echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom='. $nom .'&id='. $id .'&url='. $url .'&import_artiste=null"><img width="50" src="data:image/jpeg;base64,'.$image.'"></a>';}
            }//j'affiche le résultat, l'utilisateur pourra valider le résultat grace au lien'
        }
}


function shearch_artist($name,$method,$type) { //cete fonction recherche la présense d'un artiste dans la base
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $db->exec('PRAGMA foreign_keys = ON;');
    $db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT)');


        $sql = $db->query("SELECT * FROM artist WHERE nom_artist= '$name'"); //recherche de l'artiste dans la table artiste
        $result = $sql->fetchArray(SQLITE3_NUM);///dans ces test la notion de direct signifit que l'utilisateur à cherché directement un artist
                                                ///si c'est indrect, c'est que l'utilisateur cherche un album ou une musique avec le nom d'un artiste
            if (($result)&&($method=='direct')) { //si trouvé et direct
                echo"Recherche dans sqlite";
                $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$result[1].'&id='.$result[0].'&url='.$result[2].'&display_artist=null' ;//je redirige vers la page d'affichage de l'artiste
               echo '<script type="text/javascript">',
               'window.location.replace("http://localhost'.$url.'");',
                '</script>'
;
            }
            if ((!$result)&&($method=='direct')) {//si non trouvé et direct
                
                
                $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$name.'&query_artist=null' ; //je remplace ma page pour faire une recherche d'artiste
                echo '<script type="text/javascript">',
                    'window.location.replace("http://localhost'.$url.'");',
                     '</script>'
;

            }
            if ((!$result)&&($method=='indirect')) {//si non trouvé et indirect 
                $url=$_SERVER['PHP_SELF'].'?page=my-plugin&nom='.$name.'&query_artist=null&query_artist_other='. $type .'' ;//je remplace ma page pour faire une recherche d'artiste et je rajoute le 
                                                                                                                     //&query_artist_other dans l'url pour faire un recherche d'album ou de titre juste aprés
                echo '<script type="text/javascript">',
                'window.location.replace("http://localhost'.$url.'");',
                 '</script>'
                 
;
            }    
            if (($result)&&($method=='indirect')) {//si trouvé et indirect
                echo $type; 
                if($type=="album"){
                    shearch_album_artist($result[0]);
                }elseif($type=="track"){ 
                                    
                  shearch_track_artist($result[0]); //je redirge vers la recheche d'album par artiste            
                }             
            }
        }  


        function shearch_artist_back($id) { //cette fonction recherche la présense d'un artiste dans la base puis l'insert directement sin non trouvé
                                            // cette fonction est éxécuté seulement lorsque l'utilisateur à selectionné un album ou un track et non directement un artiste
            $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
            $db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT)'); //Si jamais l'utilisateur selectionne directement un album ou un track à sa 1e utilisation,    
                                                                                                                            // il va directement vouloir insérer dans la table artiste par la suite, donc je créer la table s'il elle n'existe pas    
        
                $sql = $db->query("SELECT * FROM artist WHERE id_artist= '$id'"); //recherche de l'artiste
                $result = $sql->fetchArray(SQLITE3_NUM);

                    if (!$result) {//si non trouvé
                    $api = new SpotifyWebAPI\SpotifyWebAPI();
                    $token=get_token();
                    $api->setAccessToken($token);
                    $artiste=$api->getArtist($id);
                    $nom=$artiste->name;
                    $url=$artiste->images[0]->url;
                    if (!$url){ //il ce peut que l'artiste n'est pas d'image, dans ce cas ma vaiable url est "null"
                        $url='NULL';
                    }
                    $db->exec("INSERT INTO  artist VALUES('$id', '$nom','$url')");
                    return $id;
                    }
                return $result[0]; 
                }  
        

?>