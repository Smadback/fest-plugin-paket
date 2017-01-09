<?php

function fest_werbemittel_shortcode($atts, $content = null) {

    $atts = shortcode_atts( array (
            'title' => 'Werbemittel',
            'pagination' => false
        ), $atts
    );

    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = array(
        'post_type'         => 'werbemittel',
        'post_status'       => 'publish',
        'no_found_rows'     => $atts['pagination'],
        'paged'             => $paged
    );

    $werbemittel = new WP_Query($args);

    if ($werbemittel->have_posts()) :
        $html_output = '<div id="werbemittel-liste">';
        $html_output .= '<table>';
        $html_output .= '<tr>';
        $html_output .= '<th>Werbemittel</th>';
        $html_output .= '<th>St&uuml;ckpreis</th>';
        $html_output .= '<th>Menge auf Lager</th>';
        $html_output .= '</tr>';

        /* iterate through all posts of the CPT */
        while($werbemittel->have_posts()) : $werbemittel->the_post();

            /* read all the meta data of the post */
            $title = get_the_title();
            $href = get_permalink();
            $stueckpreis = get_post_meta(get_the_id(), 'werbemittel_stueckpreis', true);
            $lager = get_post_meta(get_the_id(), 'werbemittel_lager', true);

            /* put the data into html */
            $html_output .= '<tr>';
            $html_output .= '<td>'.sprintf('<a href="%s">%s</a>&nbsp;', esc_url($href), esc_html($title)).'</td>';
            $html_output .= '<td>' . esc_html(number_format($stueckpreis, 2, ',', '.')) . ' €</td>';
            $html_output .= '<td>'.esc_html($lager).'</td>';
            $html_output .= '</tr>';
        endwhile;

        $html_output .= '</table>';
        $html_output .= '</div>';
    endif;

    /* reset the data */
    wp_reset_postdata();

    return $html_output;
}
add_shortcode('werbemittel_liste', 'fest_werbemittel_shortcode');
