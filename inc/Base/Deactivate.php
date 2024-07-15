<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Base;

class Deactivate
{
  public static function deactivate()
  {
    $custom_post_types = [];


    foreach ($custom_post_types as $post_type) {
      $posts = get_posts([
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
      ]);

      foreach ($posts as $post) {
        wp_delete_posts($post->ID, true);
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

    flush_rewrite_rules();
  }
}