<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;

class AboutController extends BaseController
{
  public $subpages = array();
  public $callbacks;
  public $settings;

  public function register()
  {
    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();

    $this->setSubpages();
    $this->settings->addSubPages($this->subpages)->register();
  }

  public function setSubpages()
  {
    $this->subpages = [
      [
        'parent_slug' => 'nrd_untappd_importer',
        'page_title' => 'About',
        'menu_title' => 'About',
        'capability' => 'manage_options',
        'menu_slug' => 'nrd_untappd_importer_about',
        'callback' => array($this->callbacks, 'aboutTemplate'),
      ],
    ];
  }

}