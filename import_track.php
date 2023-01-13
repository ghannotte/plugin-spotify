<?php

require_once 'import_album.php';
include_once 'search_artist.php';

//Envoi de la saisie utlisateur du son dans sqlite
function importTrack()
{
    //Récupération des données de l'url
    $id_album = $_GET['id'];        
    $nom_track = $_GET['nom_track'];
    $nom_album = $_GET['nom'];
    $id_artist = $_GET['id_artist'];
    $url = $_GET['url'];

    $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
    $date = date('d-m-y');
    $db->exec("INSERT INTO album VALUES('$id_artist', '$id_album', '$nom_album', '$url', '$date')");

    searchArtistBack($id_artist); //On insère l'artiste si il n'est pas présent dans la BDD
    searchTrackAlbum($id_album);        //On insère les titres de l'album si ils ne sont pas présents dans la BDD
    displayTrackPage($nom_track, $id_artist);
}