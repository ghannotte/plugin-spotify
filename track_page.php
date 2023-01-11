<?php
function display_track_page($track,$id){ /// cette fonction affiche les informations de lartiste selon les informatons de l'url
    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $sql = $db->query("SELECT id_album,uri FROM track WHERE nom_track = '$track' ");
    $result = $sql->fetchArray(SQLITE3_NUM);
    $sql2 = $db->query("SELECT nom_album FROM album WHERE id_album = '$result[0]' ");
    $sql3 = $db->query("SELECT nom_artist FROM artist WHERE id_artist = '$id' ");  
    $result2 = $sql2->fetchArray(SQLITE3_NUM);
    $result3 = $sql3->fetchArray(SQLITE3_NUM);
    
    echo '<p>nom track: '.$track.'</p>';
    echo '<p>nom artiste: '.$result3[0].'</p>';
    echo '<p>nom album: '.$result2[0].'</p>';

    echo'<iframe src="'. $result[1] .'" width="300" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media" loading="lazy" ></iframe>';
    
}  

?>