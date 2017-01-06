<?php 
/**
* Plugin Name: FEST Plugin Paket
* Description: Enthält die Plugins für die Werbemittelbestellung und für Standorte der FEST AG
* Author: Maik Schmaddebeck
 * Version: 1.0
*/


/*
 * todo:
 * settings seite zum einfachen aktualisieren vom bestand nach bestellung sowie einstellung von wer die email bekommt etc
 * shortcode style verbessern
 * bestellformular style verbessern
 * bild für werbemittel
 * */


/* Exit if accessed directly */
if(! defined('ABSPATH')) {
    exit;
}

/* Require the files for the plugin */
require_once (plugin_dir_path(__FILE__) . 'werbemittel/fest-werbemittel.php');
require_once (plugin_dir_path(__FILE__) . 'standort/fest-standort.php');