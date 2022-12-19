<?php

function initdb(){
    echo'toto';
$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
$db->exec('CREATE TABLE artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT)');
$db->exec('CREATE TABLE album(id_artist TEXT ,id_album TEXT PRIMARY KEY , nom_album TEXT)');
$db->exec('CREATE TABLE track(id_album TEXT foreign_keys ,id_track TEXT foreign_keys , nom_track TEXT)');
}
?>