<?php

/**
 * Trigger this file on Plugin uninstall
 * 
 * @package NRDUntappdImporter
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {

  $custom_post_types = get_option('nrd_untappd_importer_menu') ?: array();

  if(count($custom_post_types) > 0) {
    // Delete all posts
    foreach ($custom_post_types as $post_type) {
      $posts = get_posts([
        'post_type' => $post_type['post_type'],
        'numberposts' => -1,
        'post_status' => 'any'
      ]);

      foreach ($posts as $post) {
        wp_delete_posts($post->ID, true);
      }
    }

    // Delete all terms of associated taxonomies
    $taxonomies = get_object_taxonomies($post_type);
    foreach ($taxonomies as $taxonomy) {
      $terms = get_terms(
        array(
          'taxonomy' => $taxonomy,
          'hide_empty' => false,
        )
      );

      foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
      }
    }
  }
  
  $options = [
    'nrd_untappd_importer_schedule',
    'nrd_untappd_importer_api_creds',
    'nrd_untappd_importer_untappd_menu',
    'nrd_untappd_importer_menu',
    'nrd_untappd_importer'
  ];

  foreach ($options as $option) {
    delete_option($option);
  }
}

// TODO: Clear Database stored data
