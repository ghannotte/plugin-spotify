<?php

require_once dirname(__FILE__) . '/search_artist.php';
require_once dirname(__FILE__) . '/search_album.php';
require_once dirname(__FILE__) . '/search_track.php';
require_once dirname(__FILE__) . '/import_album.php';
require_once dirname(__FILE__) . '/import_artist.php';
require_once dirname(__FILE__) . '/import_track.php';
require_once dirname(__FILE__) . '/artist_page.php';
require_once dirname(__FILE__) . '/album_page.php';
require_once dirname(__FILE__) . '/track_page.php';
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/page.css';
require_once dirname(__FILE__) . '/check_fraicheur.php';


//........................................Formulaires..............................................................
//Formulaire de recherche d'artiste
echo '
<label for="name">
  Selection artiste: 
</label>
<form action="' . $_SERVER['PHP_SELF'] . '?page=my-plugin" method="post">
  <input type="text"  placeholder="nom artiste" name="artist"/>
  <input type="submit" placeholder="artiste info" name="searchArtist"/>
</form>
'; 

//Formulaire de recherche d'album
echo '
<label for="name">
  Selection album: 
</label>
<form action="' . $_SERVER['PHP_SELF'] . '?page=my-plugin" method="post">
  <input type="text"  placeholder="nom artiste" name="artist"/>
  <input type="text"  placeholder="nom album" name="album"/>
  <input type="submit" placeholder="artiste info" name=" searchAlbum"/>
</form>
'; 

//Formulaire de recherche de musique 
echo '<label for="name">Selection track: </label><form action="' . $_SERVER['PHP_SELF'].
'?page=my-plugin" method="post">
  <input type="text"  placeholder="nom artiste" name="artist_t"/>
  <input type="text"  placeholder="nom album" name="album_t"/>
  <input type="text"  placeholder="nom musique" name="track"/>
  <input type="submit" placeholder="artiste info" name="searchTrack"/>
  </form>'; 
 
$artist = 0;
$album = 0;
$track = 0;
echo '<p></p>';
echo '<form action="' . $_SERVER['PHP_SELF'].'?page=my-plugin" method="post">
<input type="submit" class="button" name="verif" value="Verification de la fraicheur des données" /></form>';

if (isset($_POST['verif'])) {
  check_fraicheur_table ();
}

if (isset($_GET['maj_db'])) {
  udpdateTable();
}

//............................... Partie recherche Artiste..........................................................
//Detecte un input dans la barre de recherche d'artiste
if (isset($_POST['searchArtist'])) {
  $name = $_POST['artist'];
  searchArtist($name,'direct','artist');
}
//Une fois que l'utilisateur a choisi son artiste grâce à la fonction shearch_artist(), nous l'importons
if (isset($_GET['import_artist'])) {
  importArtist();
}
//Affiche l'artiste quand display_artist figure dans l'url
if (isset($_GET['display_artist'])) {  
  displayArtistPage();
}
//Quand un artiste n'a pas été trouvé dans sqlite, lance la recherche dans Spotify
if (isset($_GET['query_artist'])) {
  $name = $_GET['nom']; 
  getIdArtist($name);
}    
      
//............................... Partie recherche Album..........................................................  
if (isset($_POST['searchAlbum'])) { //Detecte un input dans la barre de recherche d'album
  $artist = $_POST['artist'];
  $album = $_POST['album'];        
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); //Nous vérifions que les tables se sont bien créées
  $db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT,id_album TEXT PRIMARY KEY, nom_album TEXT, uri TEXT, fraicheur DATE)');
  $db->exec('CREATE TABLE IF NOT EXISTS track(id_artist TEXT,id_album  TEXT, id_track TEXT, nom_track TEXT, uri TEXT, fraicheur DATE)');
  if (($artist) && ($album)) { 
     searchAlbumAndArtist($artist, $album); //Fonction pour rechercher un album pour un artiste spécifique
  } elseif ((!$artist) && ($album)) {
     searchAlbum($album); //Fonction pour rechercher un album
  } elseif (($artist) && (!$album)) {
    //Si l'utilisateur souhaite rechercher un album seulement avec un nom d'artiste, nous recherchons l'artiste dans la base avant de chercher l'album
    searchArtist($artist, 'indirect', 'album');
  }  
}
//Une fois que l'utilisateur a choisi son album grâce à la fonction  searchAlbum(), nous l'importons
if (isset($_GET['import_album'])) {
  importAlbum();
}
//Affiche l'album quand "display_artist" figure dans l'url
if (isset($_GET['display_album'])) {
  displayAlbumPage();
}
//Affiche une recherche d'album quand l'utilisateur clique sur un lien spécifique 
if (isset($_GET['discover_album'])) {
  $id = $_GET['id_artist'];
  searchAlbumArtistOnSpotify($id);
}

//............................... Partie recherche Musique..........................................................
if (isset($_POST['searchTrack'])) { //Detecte un input dans la barre de recherche de musique
  $artist = $_POST['artist_t'];
  $album = $_POST['album_t'];  
  $track = $_POST['track'];  
  $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
  $db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT, id_album TEXT PRIMARY KEY, nom_album TEXT, uri TEXT, fraicheur DATE)');
  $db->exec('CREATE TABLE IF NOT EXISTS track(id_artist TEXT, id_album  TEXT, id_track TEXT, nom_track TEXT, uri TEXT, fraicheur DATE)');
}
/*
if ($artist) {
  if ($album) {
    if ($track) {
      shearch_track($artist, $track);
    } else {
      searchAlbumAndArtist($artist, $album);
    }
  } else {
    if ($track) {
      shearch_one_track_artist($artist, $track);
    } elseif (!isset($_POST['searchAlbum'])) {
      shearch_artist($artist, 'indirect', 'track');
    }
  }
} else {
  if ($album) {
    if ($track) {
      find_track_album($album, $track);
    } else {
      searchAlbum($album);    
    }
  } else {
    if ($track) {
      shearch_track($track);    
    } 
  }
}*/

//A suppr
if ((!$artist) && ($album) && (!$track)) {
  searchAlbum($album);
} elseif (($artist) && (!$album) && (!$track)  &&  (!isset($_POST['searchAlbum']))) {//OK
  searchArtist($artist,'indirect','track');
} elseif ((!$artist) && (!$album) && ($track)) { //OK
  searchTrack($track);
}
elseif ((!$artist) && ($album) && ($track)) { //OK
  findTrackAlbum($album,$track);
}
elseif (($artist) && (!$album) && ($track)) {//OK
  searchOneTrackArtist($artist,$track);
}
elseif (($artist) && ($album) && (!$track)) { //OK
  searchAlbumAndArtist($artist,$album);
}
elseif (($artist) && ($album) && ($track)) {
  findTrackAlbum($album,$track);
}

//Une fois que l'utilisateur a choisi son son grâce à la fonction fonction shearch_track(), nous l'importons
if (isset($_GET['import_track'])) {
  importTrack();
}
//Affichage des tracks
if (isset($_GET['display_tracks'])) {
  $nom_track = $_GET['nom_track'];
  $id_artist = $_GET['id_artist'];
  displayTrackPage($nom_track,$id_artist);
}