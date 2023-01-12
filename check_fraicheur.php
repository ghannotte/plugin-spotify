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
    $db->exec('DROP TABLE cache');
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
    while ($row = $sql->fetchArray()) {        
        $id=$row['id_track'];
        $date2=$row['fraicheur'];
        check_fraicheur($id,$date2,'track'); 
    }    

}

