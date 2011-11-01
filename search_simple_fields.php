<?php
/*
Plugin Name: Search Simple Fields
Plugin URI: http://elfdreamer.blogspot.com/2011/10/new-wordpress-plugin-search-simple.html
Description: Allows user to search in different cusomt fields. It extends this plugin: <a href='http://eskapism.se/code-playground/simple-fields/' target='_blank'>Simple Fields</a>. Also let the user choose from Media custom fields or Wordpress custom fields.
Version: 0.2
Author: SimonaIlie
Author URI: http://elfdreamer.blogspot.com/
License: GPL2

*/

/*  Copyright 2010  Pär Thernström (email: par.thernstrom@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!defined('SEARCH_SIMPLE_FIELDS_URL')) define('SEARCH_SIMPLE_FIELDS_URL', WP_PLUGIN_URL . '/search-simple-fields/');
if(!defined('SSF_POST_TYPES_FOR_SEARCH')) define('SSF_POST_TYPES_FOR_SEARCH', '_search_simple_fields_post_types');
if(!defined('SSF_CUSTOM_FIELDS_FOR_SEARCH')) define('SSF_CUSTOM_FIELDS_FOR_SEARCH', '_search_simple_fields_fields');
if(!defined('SSF_MEDIA_FIELDS_FOR_SEARCH')) define('SSF_MEDIA_FIELDS_FOR_SEARCH', '_search_media_fields');
if(!defined('SSF_WP_FIELDS_FOR_SEARCH')) define('SSF_WP_FIELDS_FOR_SEARCH', '_search_wp_fields_fields');
if(!defined('SSF_MEDIA_FUNCTION')) define('SSF_MEDIA_FUNCTION', '_ssf_media_function');


// on admin init: add styles and scripts
add_action( 'admin_init', 'search_simple_fields_admin_init' );
add_action( 'admin_menu', 'search_simple_fields_admin_menu' );

function search_simple_fields_admin_init()
{
	wp_enqueue_style("search-simple-fields-style", SEARCH_SIMPLE_FIELDS_URL . 'css/style.css', false, null);
}

require_once("functions_admin.php");
require_once("functions_post.php");
?>
