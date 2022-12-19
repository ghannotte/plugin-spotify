<?php

/*
Plugin Name: sputiffy
Plugin URI: https://sputiffy/
Description: Ceci est mon pr        emier plugin
Author: Guillaume Hannotte Julien Muget
Version: 1.0
Author URI: http://sputiffy/
*/


add_action( 'admin_menu', 'register_admin_page' );
function register_admin_page(){ 
    add_menu_page( 'Spotify', 'Spotify', 'edit_posts', 'my-plugin', 'admin_markup', 'some-dashicon' );
}

function admin_markup(){
    require_once dirname(__FILE__).'/home.php'; 
}





?>