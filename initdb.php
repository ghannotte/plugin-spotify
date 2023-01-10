<?php

function initdb(){

$db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');

$db->exec('CREATE TABLE IF NOT EXISTS artist(id_artist TEXT PRIMARY KEY NOT NULL, nom_artist TEXT, uri TEXT)');
$db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT ,id_album TEXT PRIMARY KEY , nom_album TEXT, uri TEXT)');
$db->exec('CREATE TABLE IF NOT EXISTS track(id_album TEXT ,id_track TEXT, nom_track TEXT)');
echo 'la database est initialisé';
$db.close();
}
?>