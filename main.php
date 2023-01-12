<?php

/*
Plugin Name: Spofify
Plugin URI: https://spofify/
Description: Encore mieux que Spotify
Author: Guillaume Hannotte & Julien Muguet
Version: 1.3
Author URI: http://spofify/
*/

//Section Admin/user
add_action('admin_menu', 'register_admin_page');
function register_admin_page()
{ 

  add_menu_page('Theme page title', 'Spotify', 'read', 'my-plugin', 'user_markup');
  add_submenu_page( 'my-plugin', 'Settings page title', 'Admin', 'activate_plugins', 'admin_page', 'admin_markup');
 

}

function admin_markup()
{
    require_once dirname(__FILE__).'/home_admin.php'; 
}

function user_markup()
{
    require_once dirname(__FILE__).'/home.php'; 
}