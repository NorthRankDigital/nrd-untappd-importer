<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Api\Callbacks;

class MenuCallbacks
{ 

  public function menuSectionManager()
  {
    echo 'Manage the menus imported from Untappd';
  }

  public function menuSanitize( $input )
  {

    $output = get_option('nrd_untappd_importer_menu') ?: array();

    if( isset($_POST["remove"])){
      unset($output[$_POST["remove"]]);

      return $output; 
    }

    if (count($output) == 0) {
      $output[$input['post_type']] = $input;

      return $output;
    }
    
    foreach ($output as $key => $value) {
      if ($input['post_type'] === $key) {
        $output[$key] = $input;
      } else {
        $output[$input['post_type']] = $input;
      }
    }

    return $output;
  }

  public function textField( $args )
  {
    $name = $args['label_for'];
    $option_name = $args['option_name'];
    $value = '';

    if( isset( $_POST["edit_post"] ) ) {
      $input = get_option($option_name);
      $value = $input[$_POST["edit_post"]][$name];
    }

    echo '<input type="text" class="regular-text" name="' . $option_name . '[' . $name . ']' . '" value="'. $value .'" placeholder="'. $args['place_holder'].'" required>';
  }

  public function selectField( $args )
  {

    $select_options = get_option( 'nrd_untappd_importer_untappd_menu', array());
    $name = $args['label_for'];
    $option_name = $args['option_name'];
    $selected = '';
    $disabled = '';
    

    if (isset($_POST["edit_post"])) {
      $selectedInput = get_option($option_name);      
      $selected = $selectedInput[$_POST["edit_post"]][$name];
      if($name == 'post_type') {
        $disabled = 'disabled';
        echo '<input type="hidden" name="'. $option_name . '[' . $name . ']'.'" value="'.$selected.'">';
      }
    }

    echo '<select'. $disabled .' name="'.$option_name . '[' . $name . ']'.'" id="untappd-menus">';
      foreach ($select_options as $key => $value)
      {
        if($selected == $key)
        {          
          echo '<option value="' . $key . '" selected>' . $value .'</option>';
        }
        else
        {
          echo '<option value="' . $key . '">' . $value .'</option>';
        }
      }
    echo '</selec>';
  }

  public function renderCustomFields($post, $args)
  {
    $name = $args['args']['label_for'];
    $placeholder = $args['args']['place_holder'];
    $value = get_post_meta($post->ID, $name, true);
    echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $name . '" value="' . esc_attr($value) . '" placeholder="'.$placeholder.'" />';
  }

}