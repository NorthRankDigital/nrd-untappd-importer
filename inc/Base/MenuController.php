<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Api\CustomTaxonomyApi;
use Inc\Api\CustomPostTypeApi;
use Inc\Api\CustomFieldApi;

use Inc\Api\Callbacks\MenuCallbacks;
use Inc\Api\Callbacks\AdminCallbacks;

use Inc\Base\BaseController;

class MenuController extends BaseController
{
  public $settings;
  public $callbacks;
  public $menuCallbacks;  
  public $customPostTypes;
  public $customTaxonomies;
  public $customFields;

  public $menus = array();
  public $subpages = array();
  public $custom_post_types = array();
  public $custom_fields = array();

  public function register()
  {
    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();
    $this->menuCallbacks = new MenuCallbacks();
    $this->customPostTypes = new CustomPostTypeApi();
    $this->customTaxonomies = new CustomTaxonomyApi();
    $this->customFields = new CustomFieldApi();
    
    $this->setSubpages();
    
    $this->setSettings();
    $this->setSections();
    $this->setFields();

    $this->storeCustomPostTypes();
    $this->storeCustomFields();
    
    $this->settings->addSubPages($this->subpages)->register();
    $this->customPostTypes->register();
    $this->customTaxonomies->register();
    $this->customFields->register();

    if (!get_option('nrd_untappd_importer_menu')) {
      update_option('nrd_untappd_importer_menu', array());
    }
  }

  public function storeCustomPostTypes()
  {
    $options = get_option('nrd_untappd_importer_menu') ?: array();

    foreach ($options as $option) {
      $this->custom_post_types[] =
      [
        'post_type' => $option['post_type'],
        'name' => $option['plural_name'],
        'singular_name' => $option['singular_name']
      ];
    }   
    $this->customPostTypes->addCustomPostType($this->custom_post_types);
    $this->customTaxonomies->addCustomTaxonomy($this->custom_post_types);
  }

  public function storeCustomFields()
  {
    $options = get_option('nrd_untappd_importer_menu') ?: array();

    foreach ($options as $option) {
      $this->custom_fields[] =
      [
        'post_type' => $option['post_type'],
        'id' => 'style',
        'title' => 'Style',
        'callback' => array($this->menuCallbacks, 'renderCustomFields'),
        'args' => array(
          'label_for' => 'style',
          'place_holder' => 'Item style'
        )
      ];

      $this->custom_fields[] =
        [
          'post_type' => $option['post_type'],
          'id' => 'abv',
          'title' => 'ABV',
          'callback' => array($this->menuCallbacks, 'renderCustomFields'),
          'args' => array(
            'label_for' => 'abv',
            'place_holder' => 'ABV'
          )
        ];

      $this->custom_fields[] =
        [
          'post_type' => $option['post_type'],
          'id' => 'containers',
          'title' => 'Containers',
          'callback' => array($this->menuCallbacks, 'renderCustomFields'),
          'args' => array(
            'label_for' => 'containers',
            'place_holder' => 'Containers'
          )
        ];
    }

    $this->customFields->setFields($this->custom_fields);
  }
  
  public function setSubpages()
  {
    $this->subpages = [
      [
        'parent_slug' => 'nrd_untappd_importer',
        'page_title' => 'Menus',
        'menu_title' => 'Menu Manager',
        'capability' => 'manage_options',
        'menu_slug' => 'nrd_untappd_importer_menus',
        'callback' => array($this->callbacks, 'menusTemplate'),
      ]
    ];
  }

  public function setSettings()
  {
    $args = array(
      array(
        'option_group' => 'nrd_untappd_importer_menu_settings',
        'option_name' => 'nrd_untappd_importer_menu',
        'callback' => array($this->menuCallbacks, 'menuSanitize')
      )
    );

    $this->settings->setSettings($args);
  }

  public function setSections()
  {
    $args = [
      [
        'id' => 'nrd_untapped_importer_menu_index',
        'title' => 'Menu Manager',
        'callback' => array($this->menuCallbacks, 'menuSectionManager'),
        'page' => 'nrd_untappd_importer_menus'
      ]
    ];

    $this->settings->setSections($args);
  }

  public function setFields()
  {
    $args = [
      [
        'id' => 'singular_name',
        'title' => 'Menu Name Singular',
        'callback' => array($this->menuCallbacks, 'textField'),
        'page' => 'nrd_untappd_importer_menus',
        'section' => 'nrd_untapped_importer_menu_index',
        'args' => array(
          'option_name' => 'nrd_untappd_importer_menu',
          'label_for' => 'singular_name',
          'place_holder' => 'eg. Cocktail'
        )
      ],
      [
        'id' => 'plural_name',
        'title' => 'Menu Name Plural',
        'callback' => array($this->menuCallbacks, 'textField'),
        'page' => 'nrd_untappd_importer_menus',
        'section' => 'nrd_untapped_importer_menu_index',
        'args' => array(
          'option_name' => 'nrd_untappd_importer_menu',
          'label_for' => 'plural_name',
          'place_holder' => 'eg. Cocktails'
        )
      ],
      [
        'id' => 'post_type',
        'title' => 'Untappd Menu',
        'callback' => array($this->menuCallbacks, 'selectField'),
        'page' => 'nrd_untappd_importer_menus',
        'section' => 'nrd_untapped_importer_menu_index',
        'args' => array(
          'option_name' => 'nrd_untappd_importer_menu',
          'label_for' => 'post_type',
          'place_holder' => 'Select a menu from Untappd'
        )
      ]      
    ];  

    $this->settings->setFields($args);
  }

}