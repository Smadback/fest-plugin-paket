<?php
/**
* Plugin Name: FEST Werbemittel Liste
* Description: Liste zur Werbemittelbestellung der FEST AG
* Author: Maik Schmaddebeck
* Version: 0.1.1
*/

/* Exit if accessed directly */
if(! defined('ABSPATH')) {
    exit;
}

/* Require the files for the plugin */
require_once (plugin_dir_path(__FILE__) . 'fest-werbemittel-cpt.php');
require_once (plugin_dir_path(__FILE__) . 'fest-werbemittel-fields.php');
require_once (plugin_dir_path(__FILE__) . 'fest-werbemittel-shortcode.php');
require_once (plugin_dir_path(__FILE__) . 'fest-werbemittel-settings.php');


/* Filter the single_template with our custom function*/
function fest_werbemittel_single_template($single) {
    global $post;
    $plugin_path = plugin_dir_path( __FILE__ );

    /* Checks for single template by post type */
    if ($post->post_type == "werbemittel"){
        if(file_exists($plugin_path . '/template/single.php'))
            return $plugin_path . '/template/single.php';
    }
    return $single;
}
add_filter('single_template', 'fest_werbemittel_single_template');



/* Import the JS and CSS files only when necessary */
function fest_werbemittel_admin_enqueue_scripts() {
    global $pagenow, $typenow;

    wp_enqueue_style('fest-single-css', plugins_url('css/single.css', __FILE__));

    // only load css and js when we really need it (on a Werbemittel edit/create page)
    if(($pagenow == 'post.php' || $pagenow == 'post-new.php') && $typenow == 'werbemittel') {
        wp_enqueue_style('fest-werbemittel-css', plugins_url('css/admin-werbemittel.css', __FILE__));

        wp_enqueue_script('fest-werbemittel-js', plugins_url('js/admin-werbemittel.js', __FILE__), array('jquery','media-upload','thickbox'));
    }
}
add_action('admin_enqueue_scripts', 'fest_werbemittel_admin_enqueue_scripts');
