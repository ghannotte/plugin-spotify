<?php

//Cette fonction affiche les informations de l'artiste selon les informations de l'url
function displayArtistPage()
{
    //Récupération des données de l'url
    $id = $_GET['id'];
    $nom = $_GET['nom'];
    $url = $_GET['url'];
    echo $nom . ' ' ;
    $image = base64_encode(file_get_contents($url)); //Récupération de l'image de l'artiste
    echo '<img width="50" src="data:image/jpeg;base64,' . $image . '">'; //Affichage de l'image
}