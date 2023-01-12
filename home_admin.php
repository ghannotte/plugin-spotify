<?php

require_once dirname(__FILE__) . '/shearch_artist.php';
require_once dirname(__FILE__) . '/shearch_album.php';
require_once dirname(__FILE__) . '/shearch_track.php';
require_once dirname(__FILE__) . '/import_album.php';
require_once dirname(__FILE__) . '/import_artist.php';
require_once dirname(__FILE__) . '/import_track.php';
require_once dirname(__FILE__) . '/artist_page.php';
require_once dirname(__FILE__) . '/album_page.php';
require_once dirname(__FILE__) . '/track_page.php';
require_once dirname(__FILE__) . '/vendor/autoload.php';

///........................................Formulaire..............................................................

    //Formulaire de recherche d'artiste
    echo 'ça va mon guigui ?';
    echo '<label for="name">Selection artiste: </label><form action="'.$_SERVER['PHP_SELF'] .
    '?page=my-plugin" method="post"><input type="text"  placeholder="nom artiste" name="artist"/>
    <input type="submit" placeholder="artiste info" name="shearch_artiste"/></form>'; 

    //Formulaire de recherche d'album
    echo '<label for="name">Selection album: </label><form action="'.$_SERVER['PHP_SELF'] .
    '?page=my-plugin" method="post"><input type="text"  placeholder="nom artiste" name="artist"/>
    <input type="text"  placeholder="nom album" name="album"/>
    <input type="submit" placeholder="artiste info" name="shearch_album"/></form>'; 

    //Formulaire de recherche de musique 
    echo '<label for="name">Selection track: </label><form action="'.$_SERVER['PHP_SELF'] 
    .'?page=my-plugin" method="post"><input type="text"  placeholder="nom artiste" name="artist_t"/>
    <input type="text"  placeholder="nom album" name="album_t"/><input type="text"  placeholder="nom musique" name="track"/>
    <input type="submit" placeholder="artiste info" name="shearch_track"/></form>'; 
     

    $artist=0;
    $album=0;
    $track=0;

///............................... Partie recherche Artiste..........................................................

    if(isset($_POST['shearch_artiste'])){ //detecte un input dans la bare de recherche d'artiste
        $name=$_POST['artist'];
        shearch_artist($name,'direct','artist');
      }


      if(isset($_GET['import_artiste'])){ //Une fois que l'utilsateur à choisit son artiste grace à la fonction fonction shearch_artist(), il l'import
        import_artist();

      }
      if(isset($_GET['display_artist'])){ //affiche l'artiste quand display_artist figure dans l'url
        display_artist_page();
      }
      if(isset($_GET['query_artist'])){ //Quand un artiste n'a pas été trouvé dans sqlite, lance la recherche de spotify
        $name=$_GET['nom']; 
        get_id_artist($name);
      }    
      
///............................... Partie recherche Album..........................................................
      
      if(isset($_POST['shearch_album'])){ //detecte un input dans la bare de recherche d'album
        $artist=$_POST['artist'];
        $album=$_POST['album'];        
        $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db'); // Je vérifie que les tables ce sont bien crée
        $db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT ,id_album TEXT PRIMARY KEY , nom_album TEXT, uri TEXT,fraicheur DATE)');
        $db->exec('CREATE TABLE IF NOT EXISTS track(id_artist TEXT ,id_album  TEXT ,id_track TEXT, nom_track TEXT, uri TEXT,fraicheur DATE)');
        if(($artist)&&($album)){ 
          shearch_album_and_artist($artist,$album);///Cette fonction va rechercher un album pour un artiste spécifique
        }elseif((!$artist)&&($album)){
          shearch_album($album);///Cette fonction va rechercher un album 
          
        }elseif(($artist)&&(!$album)){
          shearch_artist($artist,'indirect','album'); //si l'utilisateur souhaite rechercher un album seulement avec un nom d'artiste je cherche l'artiste dans la base avant de chercher l'album
        }
        
      }

      if(isset($_GET['import_album'])){ //Une fois que l'utilsateur à choisit son album grace à la fonction fonction shearch_aLBUM(), il l'import
        import_album();

      }

      if(isset($_GET['display_album'])){ //affiche l'album quand display_artist figure dans l'url
        display_album_page();
      }

      if(isset($_GET['discover_album'])){ //affiche une recherche d'album quand l'utlisateur click sur un lien spécifique 
        $id=$_GET['id_artist'];
        shearch_album_artist_on_spotify($id);
      }
///............................... Partie recherche Musique..........................................................

      if(isset($_POST['shearch_track'])){ //detecte un input dans la bare de recherche d'artiste
        $artist=$_POST['artist_t'];
        $album=$_POST['album_t'];  
        $track=$_POST['track'];  
        $db = new SQLite3('../wp-content/plugins/spotify/spotify_db.db');
        $db->exec('CREATE TABLE IF NOT EXISTS album(id_artist TEXT ,id_album TEXT PRIMARY KEY , nom_album TEXT, uri TEXT,fraicheur DATE)');
        $db->exec('CREATE TABLE IF NOT EXISTS track(id_artist TEXT ,id_album  TEXT ,id_track TEXT, nom_track TEXT, uri TEXT,fraicheur DATE)');

      }
      if((!$artist)&&($album)&&(!$track)){
        
        shearch_album($album);

      }elseif(($artist)&&(!$album)&&(!$track) && (!isset($_POST['shearch_album']))){

        shearch_artist($artist,'indirect','track');

      }elseif((!$artist)&&(!$album)&&($track)){
        shearch_track($track);
      }
      elseif((!$artist)&&($album)&&($track)){


        find_track_album($album,$track);

      }
      elseif(($artist)&&(!$album)&&($track)){

        shearch_one_track_artist($artist,$track);
      }
      elseif(($artist)&&($album)&&(!$track)){
        
        shearch_album_and_artist($artist,$album);

      }
      elseif(($artist)&&($album)&&($track)){
        
        shearch_track($artist,$track);
      
      }
      if(isset($_GET['import_track'])){ //Une fois que l'utilsateur à choisit son album grace à la fonction fonction shearch_aLBUM(), il l'import
        import_track();

      }
      if(isset($_GET['display_tracks'])){ //Une fois que l'utilsateur à choisit son album grace à la fonction fonction shearch_aLBUM(), il l'import
        $nom_track=$_GET['nom_track'];
        $id_artist=$_GET['id_artist'];
        display_track_page($nom_track,$id_artist);

      }
?>