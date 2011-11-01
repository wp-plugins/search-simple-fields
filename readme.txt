=== Plugin Name ===
Contributors: SimonaIlie
Donate link: 
Tags: admin, fields, custom fields, media fields, search
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 0.2

Set custom fields to apply the Wordpress Search on.

== Description ==

Search Simple Fields for WordPress let you add custom fields to be used during the wordpress search.

Search Simple Fields is mainly, an extension for <a href='http://eskapism.se/code-playground/simple-fields/' target='_blank'>Simple Fields</a> plugin, but not only.

#### Features and highlight
* choose the post types to be used in search. Easy way to include registered post types
* choose the defined Simple Fields to be used in search query
* advanced option: choose from Media custom fields. It required programming knowledge: you need to include the name of the function used for the "attachment_fields_to_edit" filter.
* to include the set custom fields, add in the active theme function.php file the lines:
    if(!is_admin()) :
        if(function_exists('search_simple_fields_search_posts')) :
            search_simple_fields_search_posts();
        endif;


#### Help and Support
You can find more information about this plugin here: http://elfdreamer.blogspot.com/2011/10/new-wordpress-plugin-search-simple.html

== Installation ==

As always, make a backup of your database first!

1. Upload the folder "search-simple-fields" to "/wp-content/plugins/"
1. Activate the plugin through the "Plugins" menu in WordPress
1. A new option will be added to Settings: Search Simple Fields

== Screenshots ==

1. A post in edit, showing two field groups: "Article options" and "Article images".
These groups are just example: you can create your own field groups with any combinatin for fields.
See that "Add"-link above "Article images"? That means that it is repeatable, so you can add as many images as you want to the post.

2. One field group being created (or modified).

3. Group field groups together and make them available for different post types.


== Changelog ==

= 0.1 =
- First beta version.

= 0.2 =
- Added possibility to search in WordPress custom fields too

