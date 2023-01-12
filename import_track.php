<?php

require_once 'import_album.php';
include_once 'shearch_artist.php';

//Envoi de la saisie utlisateur du son dans sqlite
function import_track()
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

    shearch_artist_back($id_artist); //On insère l'artiste si il n'est pas présent dans la BDD
    shearch_track_album($id);        //On insère les titres de l'album si il ne sont pas présents dans la BDD
    display_track_page($nom_track, $id_artist);
}