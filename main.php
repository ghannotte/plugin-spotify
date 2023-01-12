<?php

/*
Plugin Name: Spofify
Plugin URI: https://spofify/
Description: Encore mieux que Spotify
Author: Guillaume Hannotte & Julien Muguet
Version: 1.3
Author URI: http://spofify/
*/

//Section Admin
add_action('admin_menu', 'register_admin_page');
function register_admin_page()
{ 
    add_menu_page('Spotify', 'Admin', 'activate_plugins', 'my-plugin', 'admin_markup', 'some-dashicon');
    //add_menu_page('Spotify', 'Spotify', 'read', 'my-plugin', 'user_markup', 'some-dashicon');
}

function admin_markup()
{
    require_once dirname(__FILE__).'/home_admin.php'; 
}

//Section User
add_action('user_menu', 'register_user_page');
function register_user_page()
{ 
    add_menu_page('Spotify', 'Spotify', 'edit_posts', 'my-plugin', 'user_markup', 'some-dashicon');
}

function user_markup()
{
    require_once dirname(__FILE__).'/home.php'; 
}