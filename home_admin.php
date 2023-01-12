<?php
require_once dirname(__FILE__) . '/check_fraicheur.php';

$artist = 0;
$album = 0;
$track = 0;

echo '<label for="name">Supression artiste: </label><form action="' . $_SERVER['PHP_SELF'].
'?page=admin_page" method="post"><input type="text"  placeholder="nom artiste" name="artist"/>
<input type="submit" placeholder="artiste info" name="delete_artist"/></form>'; 

echo '<label for="name">Supression album: </label><form action="' . $_SERVER['PHP_SELF'].
'?page=admin_page" method="post"><input type="text"  placeholder="nom album" name="album"/>
<input type="submit" placeholder="artiste info" name="delete_album"/></form>'; 

echo '<label for="name">Supression musique: </label><form action="' . $_SERVER['PHP_SELF'].
'?page=admin_page" method="post"><input type="text"  placeholder="nom musique" name="track"/>
<input type="submit" placeholder="artiste info" name="delete_track"/></form>'; 
echo '<p></p>';
echo '<form action="' . $_SERVER['PHP_SELF'].'?page=admin_page" method="post">
<input type="submit" class="button" name="purge" value="purge_database" /></form>';

echo '<p></p>';
echo '<form action="' . $_SERVER['PHP_SELF'].'?page=my-plugin" method="post">
<input type="submit" class="button" name="verif" value="Verification de la fraicheur des données" /></form>';

if (isset($_POST['verif'])) {
  check_fraicheur_table ();
}

if (isset($_GET['maj_db'])) {
  udpdate_table();
}

if (isset($_POST['delete_artist'])) {
  $artist = $_POST['artist'];
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
  $db->exec("DELETE FROM artist WHERE nom_artist='$artist'");
  echo 'artiste '.$artist .' delete de la table artist';
}

if (isset($_POST['delete_album'])) {
  $album = $_POST['album'];
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
  $db->exec("DELETE FROM album WHERE nom_album= '$album'");
  echo 'album '.$album .' delete de la table album';
}

if (isset($_POST['delete_track'])) {
  $track = $_POST['track'];
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
  $db->exec("DELETE FROM track WHERE nom_track= '$track'");
  echo 'Musique '.$track .' delete de la table track';
}

if (isset($_POST['purge'])) {
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
  $db->exec("DROP TABLE artist");
  $db->exec("DROP TABLE album");
  $db->exec("DROP TABLE track");
  echo 'la base a été purgé';

}

?>