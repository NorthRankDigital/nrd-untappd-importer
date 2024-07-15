<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Base;

class Activate
{
  public static function activate()
  {
    flush_rewrite_rules();

    $default = array();

    if (!get_option('nrd_untappd_importer')) {
      update_option('nrd_untappd_importer', $default);
    }

    if (!get_option('nrd_untappd_importer_menu')) {
      update_option('nrd_untappd_importer_menu', $default);
    }

    if (!get_option('nrd_untappd_importer_api_creds')) {
      update_option('nrd_untappd_importer_api_creds', 0);
    }

    if (!get_option('nrd_untappd_importer_schedule')) {
      update_option('nrd_untappd_importer_schedule', '');
    }
  }
}