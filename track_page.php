<?php
function display_track_page($track,$id){ /// cette fonction affiche les informations de lartiste selon les informatons de l'url
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql2 = $db->query("SELECT * FROM album WHERE id_artist = '$id' ");
    $sql3 = $db->query("SELECT * FROM artist WHERE id_artist = '$id' ");  
    $result2 = $sql2->fetchArray(SQLITE3_NUM);
    $result3 = $sql3->fetchArray(SQLITE3_NUM);
    
    echo '<p>nom track: '.$track.'</p>';
    echo '<p>nom artiste: '.$result3[1].'</p>';
    echo '<p>nom album: '.$result2[2].'</p>';
    $db.close();

}

?>