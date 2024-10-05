<?php

class AICShortcodes {
  function __construct() {
    add_shortcode('display_artwork', array($this, 'display_artwork_shortcode'));
    add_filter('manage_artwork_posts_columns', array($this, 'add_artwork_shortcode_column'));
    add_action('manage_artwork_posts_custom_column', array($this, 'populate_artwork_shortcode_column'), 10, 2);
    add_action('wp_enqueue_scripts', array($this, 'enqueue_shortcode_assets'));
  }

  function enqueue_shortcode_assets() {
    wp_enqueue_style('aic-shortcode-styles', plugin_dir_url(__FILE__) . '../assets/shortcode.css');
  }

  function add_artwork_shortcode_column($columns) {
    $columns['artwork_shortcode'] = __('Shortcode', 'textdomain');
    return $columns;
  }

  function populate_artwork_shortcode_column($column, $post_id) {
    if ($column === 'artwork_shortcode') {
      echo '[display_artwork id="' . esc_attr($post_id) . '"]';
    }
  }

  function display_artwork_shortcode($atts) {
    $atts = shortcode_atts(array(
      'id' => ''
    ), $atts, 'display_artwork');

    $post_id = $atts['id'];
    if (!$post_id) {
      return '<p class="shortcode-notice">Artwork ID is required.<p>';
    }

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'artwork') {
      return '<p class="shortcode-notice">Invalid artwork ID.<p>';
    }

    $output = '<div class="artwork">';
    if (has_post_thumbnail($post_id)) {
      $output .= get_the_post_thumbnail($post_id, 'large');
    }
    $creation_date = get_post_meta($post_id, 'creation_date', true);
    $output .= '<h2>' . esc_html($post->post_title) . ', ' . esc_html($creation_date) . '</h2>';
    $artist_name = get_post_meta($post_id, 'artist_name', true);
    if ($artist_name and $creation_date) {
      $output .= '<p><strong>Artist Name:</strong> ' . esc_html($artist_name) .  '</p>';
    }
    $creation_date = get_post_meta($post_id, 'creation_date', true);
    if ($creation_date) {
      $output .= '<p><strong>Creation Date:</strong> ' . esc_html($creation_date) . '</p>';
    }
    $output .= '</div>';

    return $output;
  }
}

new AICShortcodes();
