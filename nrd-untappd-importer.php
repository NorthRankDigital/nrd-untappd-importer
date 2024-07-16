<?php

/**
 * @package NRDUntappdImporter
 */

/*
  Plugin Name: NRD Untappd Importer
  Plugin URI: https://northrankdigital.com
  Description: Import menus from Untappd to Wordpress
  Version: 1.0.2
  Author: North Rank Digital
  Author URI: https://northrankdigital.com
  License: GPLv2 or later
  Text Domain: NRDUntappedImporter
*/

/* 
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public LIcense for more details.

  You should have received a copy of the GNU General Public License
  alon with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

  Copyright 2005-2015 Automattic, Inc
*/


// If this file is called directly abort!!
defined('ABSPATH') or die('404 NOT FOUND');

// Require once the Composer Autoload.
if (file_exists(dirname(__FILE__) .'/vendor/autoload.php')) {
  require_once dirname(__FILE__) .'/vendor/autoload.php';
}  

// Define CONSTANTS
define('NRDUI_PATH', plugin_dir_path( __FILE__ ));
define('NRDUI_URL', plugin_dir_url( __FILE__ ));
define('NRDUI_NAME', plugin_basename(__FILE__));

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
  'https://github.com/NorthRankDigital/nrd-untappd-importer',
  __FILE__,
  'nrd-untappd-importer'
);

//Set the branch that contains the stable release.
// $myUpdateChecker->setBranch('master');

/**
 * The code that runs during plugin activation.
 */
function activate_nrd_untappd_importer()
{
  Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_nrd_untappd_importer');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_nrd_untappd_importer()
{
  Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_nrd_untappd_importer');

/**
 * Initialize all the core classes of the plugin.
 */
if ( class_exists('Inc\\Init')) 
{
  Inc\Init::register_services();
}