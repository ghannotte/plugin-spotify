<?php

/*
Plugin Name: Spofify
Plugin URI: https://spofify/
Description: Encore mieux que Spotify
Author: Guillaume Hannotte & Julien Muguet
Version: 1.0
Author URI: http://spofify/
*/

//Section Admin/User
add_action('admin_menu', 'registerAdminPage');
function registerAdminPage()
{
  add_menu_page('Theme page title', 'Spotify', 'read', 'my-plugin', 'userMarkup');
  add_submenu_page('my-plugin', 'Settings page title', 'Admin', 'activate_plugins', 'admin_page', 'adminMarkup');
}

function adminMarkup()
{
    require_once dirname(__FILE__) . '/home_admin.php'; 
}

function userMarkup()
{
    require_once dirname(__FILE__) . '/home.php'; 
}