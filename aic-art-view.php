<?php
/*
* Plugin Name: AIC Art View
* Description: This plugin allows you to search for any artwork in the Art Institute of Chicagos digital library and display it on your site.
* Version: 1.0.0
* Author: Edlando Eliacin
* Author URI: https://www.edlandoeliacin.com/
*/

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . 'admin/class-admin-pages.php';
require_once plugin_dir_path(__FILE__) . 'includes/artwork-shortcodes.php';

class AIC_Art_View_Plugin {
  function __construct() {
    add_action('init', array($this, 'register_artwork_post_type'));
    register_activation_hook(__FILE__, array($this, 'flush_rewrite_rules_on_activation'));
  }

  function register_artwork_post_type() {
    $labels = array(
      'name'               => _x('Artworks', 'post type general name', 'textdomain'),
      'singular_name'      => _x('Artwork', 'post type singular name', 'textdomain'),
      'menu_name'          => _x('Artworks', 'admin menu', 'textdomain'),
      'name_admin_bar'     => _x('Artwork', 'add new on admin bar', 'textdomain'),
      'add_new'            => _x('Add New', 'artwork', 'textdomain'),
      'add_new_item'       => __('Add New Artwork', 'textdomain'),
      'new_item'           => __('New Artwork', 'textdomain'),
      'edit_item'          => __('Edit Artwork', 'textdomain'),
      'view_item'          => __('View Artwork', 'textdomain'),
      'all_items'          => __('All Artworks', 'textdomain'),
      'search_items'       => __('Search Artworks', 'textdomain'),
      'not_found'          => __('No artworks found.', 'textdomain'),
      'not_found_in_trash' => __('No artworks found in Trash.', 'textdomain')
    );

    $args = array(
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => true,
      'show_in_rest'       => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'menu_icon'          => 'dashicons-art',
      'query_var'          => true,
      'rewrite'            => array('slug' => 'artwork'),

      'capability_type'    => 'post',
      'capabilities'       => array(
        'create_posts' => false,
      ),
      'map_meta_cap'      => true,
      'supports'           => array('title', 'thumbnail', '',)
    );

    register_post_type('artwork', $args);
  }

  function flush_rewrite_rules_on_activation() {
    $this->register_artwork_post_type();
    flush_rewrite_rules();
  }
}

$AIC_Art_View_Plugin = new AIC_Art_View_Plugin($aic_admin_pages);
