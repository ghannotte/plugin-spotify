<?php

function display_album_page(){ /// cette fonction affiche les informations d'un album précis 
$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
$id= $_GET['id'];//récupération des données de l'url
$nom= $_GET['nom'];
$url= $_GET['url'];
$id_artist= $_GET['id_artist'];
$sql=$db->query("SELECT * FROM artist WHERE id_artist= '$id_artist'");
$artist = $sql->fetchArray(SQLITE3_NUM);
echo '<p>nom artiste: '. $artist[1] . ' </p>';
echo '<p>nom album: '. $nom . ' </p>';
$image = base64_encode(file_get_contents($url));//récupération récupétation de l'image de l'artiste
echo '<img width="50" src="data:image/jpeg;base64,'.$image.'">';//affichage de l'image  
$sql=$db->query("SELECT * FROM track WHERE id_album= '$id'");
echo "<p> Musique de l'Album :</p>"; 
while ($row = $sql->fetchArray()) {
   echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&nom_track='. $row['nom_track'] .'&id_artist='. $id_artist .'&display_tracks=null"><p>'.$row['nom_track'].'</p>';   
}

echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&id_artist='. $id_artist .'&discover_album=null"><p>rechercher dautres album pour cette artiste</p></a>';

}

function display_album_all_page($id){/// cette fonction affiche les album enregistré pour un artiste
$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
$sql=$db->query("SELECT * FROM album WHERE id_artist= '$id'");
$re=$db->query("SELECT * FROM artist WHERE id_artist= '$id'");
$artist = $re->fetchArray(SQLITE3_NUM);
echo '<p>nom artiste: '. $artist[1] . ' </p>';
while ($row = $sql->fetchArray()) {
    echo '<p>nom album: '. $row['nom_album'] . ' </p>';
    $image = base64_encode(file_get_contents($row['uri']));
    echo '<img width="50" src="data:image/jpeg;base64,'.$image.'">';
    $url=$_SERVER['PHP_SELF'].'?page=my-plugin&id_artist='.$id.'&nom='.$row['nom_album'].'&id='.$row['id_album'].'&url='.$row['uri'].'&display_album=null' ;
    echo '<a href="'.$url.'"><p>detail album </p></a>';
    
}

echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&id_artist='. $id .'&discover_album=null"><p>rechercher dautres album pour cette artiste dans spotify </p></a>';

}


?>