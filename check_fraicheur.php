<?php 


function check_fraicheur($id,$date2,$type) 
{
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $date1=date('d-m-y');
    $diff = strtotime($date1) - strtotime($date2);
    $delta=round($diff / 86400);
    if ($delta<1){
        $db->exec("INSERT INTO cache VALUES('$id','$type')");
    }
    

}        

function check_fraicheur_table (){

    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $db->exec('DROP TABLE IF EXISTS cache');
    $db->exec('CREATE TABLE cache(id TEXT, types TEXT )');
    $db->busyTimeout(5000);
    $db->exec('PRAGMA journal_mode = wal;');
    $sql = $db->query("SELECT id_artist,fraicheur FROM artist ");
    while ($row = $sql->fetchArray()) {        
        $id=$row['id_artist'];
        $date2=$row['fraicheur'];
        check_fraicheur($id,$date2,'artist'); 
    }
    $sql = $db->query("SELECT id_album,fraicheur FROM album ");    
    while ($row = $sql->fetchArray()) {        
        $id=$row['id_album'];
        $date2=$row['fraicheur'];
        check_fraicheur($id,$date2,'album'); 
    }
    $sql = $db->query("SELECT id_track,fraicheur FROM track ");
    while ($row = $sql->fetchArray()) {        
        $id=$row['id_track'];   
        $date2=$row['fraicheur'];
        check_fraicheur($id,$date2,'track'); 
    } 
    $sql=$db->query("SELECT * FROM cache ");
    $result = $sql->fetchArray(SQLITE3_NUM);

        if ($result) {
            echo '"données périmées, veuillez mettre à jour"';
            echo '<form action="' . $_SERVER['PHP_SELF'].'?page=my-plugin&maj_db=NULL" method="post">
            <input type="submit" class="button" name="maj" value="mise à jour de la database" /></form>';
            }else{
            echo 'la base est à jour ';   
            }
        }

function udpdateTable(){
    echo 'mise à jour de la base efectue';
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT * FROM cache ");
    while ($row = $sql->fetchArray()) {        
        $id=$row['id']; 
        $type=$row['types'];
        search($id,$type);

    }
    $db->exec('DROP TABLE cache');
}
    function search($id,$type){
        $token=getToken();
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
        $api->setAccessToken($token); 
        if ($type=='artist'){
            $artiste=$api->getArtist($id);
            $nom=$artiste->name;
            $url=$artiste->images[0]->url;
            $date=date('d-m-y');
            $sql = $db->query( "UPDATE artist 
            SET id_artist = '$id', nom_artist = '$nom', uri = '$url' ,fraicheur='$date'
            WHERE id_artist = '$id'");

        }elseif($type=='album'){
            $album=$api->getAlbum($id);
            $nom=$album->name;
            $id_artist=$album->artists[0]->id;
            $url=$album->images[0]->url;
            $date=date('d-m-y');
            $sql = $db->query( "UPDATE album
            SET id_artist = '$id_artist', id_album ='$id',nom_album = '$nom', uri = '$url' ,fraicheur='$date'
            WHERE id_album = '$id'");   
            
           
        }elseif($type=='track'){

            $track=$api->getTrack($id);
            $date=date('d-m-y');
            $name=$track->name;
            $id_album=$track->album->id;
            $id_artist=$track->album->artists[0]->id;
            $url=$track->preview_url;
            $sql = $db->query( "UPDATE track
            SET id_artist = '$id_artist', id_album ='$id_album',id_track = '$id', nom_track='$name' , uri = '$url' ,fraicheur='$date'
            WHERE id_track= '$id'"); 


        }
    }


