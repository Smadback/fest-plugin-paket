<?php


function fest_register_werbemittel_post_type() {

    $singular = 'Werbemittel';
    $plural = 'Werbemittel';

    $labels = array (
        'name'               => $plural,
        'singular_name'      => $singular,
        'add_new'            => sprintf('%s erstellen', $singular),
        'add_new_item'       => sprintf('Neues %s erstellen', $singular),
        'edit_item'          => sprintf('%s bearbeiten', $singular),
        'new_item'           => sprintf('Neues %s', $singular),
        'all_items'          => sprintf('Alle %s', $plural),
        'view'               => sprintf('%s anzeigen', $singular),
        'view_item'          => sprintf('%s anzeigen', $singular),
        'search_term'        => sprintf('%s suchen', $plural),
        'not_found'          => sprintf('Kein %s gefunden', $singular),
        'not_found_in_trash' => sprintf('Kein %s im Papierkorb gefunden', $singular)
    );

    $args = array(
        'public'    => true,
        'labels'    => $labels,
        'label'     => 'Werbemittel',
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => false,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-megaphone',
        'can_export' => true,
        'delete_with_user' => false,
        'hierarchical' => false,
        'has_archive' => true,
        'query_var' => true,
        'capability_type' => 'page',
        'map_meta_cap' => true,
//        'capabilities' => array(),
        'rewrite' => array(
            'slug' => 'werbemittel',
            'with_front' => true,
            'pages' => true,
            'feeds' => false
        ),
        'supports' => array(
            'title'
        )
    );

    register_post_type('werbemittel', $args);
}
add_action('init', 'fest_register_werbemittel_post_type');