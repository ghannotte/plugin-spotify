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

  add_menu_page('Theme page title', 'Spotify', 'read', 'theme-options', 'user_markup');
  add_submenu_page( 'theme-options', 'Settings page title', 'Admin', 'activate_plugins', 'activate_plugins', 'admin_markup');
 

}

function admin_markup()
{
    require_once dirname(__FILE__).'/home_admin.php'; 
}

function user_markup()
{
    require_once dirname(__FILE__).'/home.php'; 
}