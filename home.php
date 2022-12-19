<?php

require_once dirname(__FILE__).'/shearch_artist.php';
require_once dirname(__FILE__).'/initdb.php';
require_once dirname(__FILE__).'/import_artist.php';
require_once dirname(__FILE__).'/artist_page.php';
require_once dirname(__FILE__).'/vendor/autoload.php';


    echo '<form action="" method="post"><input type="text" name="inputText"/><input type="submit" placeholder="artiste info" name="shearch_artiste"/></form>'; 
    echo '<a href="'.$_SERVER['PHP_SELF'] .'?page=my-plugin&init_db=null"><p>init_database<p/></a>';

      if(isset($_POST['shearch_artiste'])){ //detecte un input dans la bare de recherche d'artiste
        $name=$_POST['inputText'];
        shearch_artist($name);
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
      if(isset($_GET['init_db'])){ //Quand un artiste n'a pas été trouvé dans sqlite, lance la recherche de spotify
        initdb();
      }   

?>