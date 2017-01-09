<?php
/**
* Plugin Name: FEST Standorte
* Description: Plugin zur Pflege der einzelnen Standorte der Tochterunternehmen der FEST AG
* Author: Maik Schmaddebeck
* Version: 0.1.0
*/

/* Exit if accessed directly */
if(! defined('ABSPATH')) {
    exit;
}

/* Require the files for the plugin */
require_once (plugin_dir_path(__FILE__) . 'fest-standort-cpt.php');
require_once (plugin_dir_path(__FILE__) . 'fest-standort-fields.php');
