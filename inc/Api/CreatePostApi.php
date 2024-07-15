<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Api;


class CreatePostApi
{
  
  public function createPost(array $post)
  {
    $new_post = [
      'post_title' => wp_strip_all_tags( $post['post_title']),
      'post_content' => $post['post_content'],
      'post_status' => 'publish',
      'post_author' => 1,
      'post_type' => $post['post_type']
    ];
    $custom_fields = $post['custom_fields'];
    $taxonomies = $post['taxonomies'];

    $post_id = wp_insert_post($new_post);

    if (is_wp_error($post_id)) {
      return $post_id->get_error_message();
    } else {
      // Add custom fields if provided
      if (!empty($custom_fields)) {
        foreach ($custom_fields as $key => $value) {
          add_post_meta($post_id, $key, $value, true);
      }
      // Add custom taxonomies if provided
      if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy => $terms) {
            $this->addTermsToPost($post_id, $terms, $taxonomy);
        }
      }
    }
      return $post_id;
    }
  }

  private function addTermsToPost($post_id, $terms, $taxonomy)
  {
    // Ensure terms are an array
    if (!is_array($terms)) {
      $terms = array($terms);
    }

    // Set the terms for the post
    wp_set_object_terms($post_id, $terms, $taxonomy);
  }

  public function deleteAllPosts($post_type)
  {
    global $wpdb;

    // Get all posts of the custom post type
    $posts = get_posts(
      array(
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
      )
    );

    // Loop through each post and delete it along with its meta and terms
    foreach ($posts as $post) {
      // Delete the post and all its associated data
      wp_delete_post($post->ID, true); // true to force delete
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
  

}