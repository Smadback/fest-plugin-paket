<?php

function fest_werbemittel_shortcode($atts, $content = null) {
  //response generation function
  $response = "";

  //function to generate response
  function my_contact_form_generate_response($type, $message) {
      global $response;

      if ($type == "success") :
          $response = "<div class='success'>{$message}</div>";
      else :
          $response = "<div class='error'>{$message}</div>";
      endif;
  }

  //response messages
  $missing_menge = "Es wurde keine Menge eingegeben.";
  $message_unsent = "Die Bestellung konnte nicht verschickt werden.";
  $message_sent = "Die Bestellung wurde erfolgreich aufgegeben.";

  //user posted variables
  $submitted = isset($_POST['submitted']) ? true : false;



    $atts = shortcode_atts( array (
            'title' => 'Werbemittel',
            'pagination' => false
        ), $atts
    );

    $html_output = '';
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    add_image_size( 'werbemittel-image-size', 100, 100 );

    $werbemittel_query_args = array(
        'post_type'         => 'werbemittel',
        'post_status'       => 'publish',
        'no_found_rows'     => $atts['pagination'],
        'paged'             => $paged
    );
    $werbemittel_query = new WP_Query($werbemittel_query_args);

    /* Prepare the WP_Query for the Standort CPT */
    $standorte_query_args = array(
        'post_type'         => 'standort',
        'post_status'       => 'publish'
    );
    $standorte_query = new WP_Query($standorte_query_args);




    /*
    * only execute this code if the form was submitted
    */
    if($submitted):
      $artikel = get_post(esc_attr($_POST['bestellung_werbemittel']));
      $standort = get_post(esc_attr($_POST['bestellung_standort']));
      $the_order = array(
        'artikel' => $artikel->post_title,
        'stueckpreis' => get_post_meta($artikel->ID, 'werbemittel_stueckpreis', true),
        'menge' => esc_attr($_POST['bestellung_menge']),
        'standort' => $standort->post_title,
        'strasse' => get_post_meta($standort->ID, 'standort_strasse', true),
        'ort' => get_post_meta($standort->ID, 'standort_ort', true),
        'benutzer' => wp_get_current_user()
      );

      $headers = array(
          'Content-Type: text/html; charset=UTF-8',
          'From: ' . $the_order['benutzer']->user_email
      );
      $subject = 'Bestellung für ' . $the_order['artikel'];
      $message = 'Es ist eine Bestellung für Werbemittel eingetroffen.<br><br>';
      $message .= '<table>';
      $message .= '<tr><td><b>Bestellt von</b></td><td>' . $the_order['benutzer']->user_firstname . ' ' . $the_order['benutzer']->user_lastname . '</td></tr>';
      $message .= '<tr><td valign="top"><b>Standort</b></td><td>' . $the_order['standort'] . '<br>' . $the_order['strasse'] . '<br>' . $the_order['ort'] . '</td></tr>';
      $message .= '<tr><td><b>Artikel</b></td><td>' . $the_order['artikel'] . '</td></tr>';
      $message .= '<tr><td><b>Menge</b></td><td>' . $the_order['menge'] . '</td></tr>';
      $message .= '<tr><td><b>St&uuml;ckpreis</b></td><td>' . number_format($the_order['stueckpreis'], 2, ',', '.') . ' &euro;</td></tr>';
      $message .= '<tr><td><b>Gesamtpreis</b></td><td>' . number_format($the_order['stueckpreis'] * $the_order['menge'], 2, ',', '.') . ' &euro;</td></tr>';
      $message .= '</table>';

      /* validate the input and sent the mail */
      if(empty($the_order['menge'])):
          $html_output .= '<div class="error">Es wurde keine Menge eingegeben.</div><br>';
      else:
          $sent = wp_mail(get_option('werbemittel_to'), $subject, $message, $headers); // sent email
          if($sent):
              $html_output .= '<div class="success">Die Bestellung wurde erfolgreich aufgegeben.</div><br>';
          else:
              $html_output .= '<div class="error">Die Bestellung konnte nicht verschickt werden.</div><br>';
          endif;
      endif;
    endif;



    if ($werbemittel_query->have_posts()) :

        $html_output .= '<form action="' . get_page_link() . '" method="post">';
        $html_output .= '<input type="hidden" name="submitted" value="1">';

        $html_output .= '<div id="werbemittel-liste">';
        $html_output .= '<table style="width:100%">';
        $html_output .= '<tr>';
        $html_output .= '<th style="width: 100px"></th>';
        $html_output .= '<th>Werbemittel</th>';
        $html_output .= '<th style="width: 20%;">St&uuml;ckpreis</th>';
        $html_output .= '<th style="width: 15%;">Mindestbestellmenge</th>';
        $html_output .= '<th style="width: 5%;"></th>';
        $html_output .= '</tr>';

        /* iterate through all posts of the CPT */
        while($werbemittel_query->have_posts()) : $werbemittel_query->the_post();

            /* read all the meta data of the post */
            $the_post = array(
              'titel' => get_the_title(),
              'permalink' => get_permalink(),
              'beschreibung' => get_post_meta(get_the_id(), 'werbemittel_beschreibung', true),
              'anmerkung' => get_post_meta(get_the_id(), 'werbemittel_anmerkung', true),
              'bild' => get_post_meta(get_the_id(), 'werbemittel_bild', true),
              'stueckpreis' => get_post_meta(get_the_id(), 'werbemittel_stueckpreis', true),
              'lager' => get_post_meta(get_the_id(), 'werbemittel_lager', true)
            );

            /* put the data into html */
            $html_output .= sprintf('<tr><td>%s</td>', wp_get_attachment_image($the_post['bild'], 'werbemittel-image-size', false, array("class"=>"werbemittel-bild", "style"=>"float:right")));
            // $html_output .= sprintf('<td><a href="%s">%s</a>&nbsp;', esc_url($the_post['permalink']), esc_html($the_post['titel']));
            $html_output .= sprintf('<td><b>%s</b>', $the_post['titel']);
            $html_output .= sprintf('<p>%s</p><p>%s</p></td>', $the_post['beschreibung'], $the_post['anmerkung']);
            $html_output .= sprintf('<td>%s €</td>', number_format($the_post['stueckpreis'], 2, ',', '.'));
            $html_output .= sprintf('<td>%s</td>', $the_post['lager']);
            $html_output .= sprintf('<td><input type="radio" id="bestellung-werbemittel" name="bestellung_werbemittel" value="%s"></td></tr>', get_the_id());
        endwhile;

        $html_output .= '</table></div>';

        $html_output .= '<p><label for="bestellung-menge">Menge:</label>';
        $html_output .= '<input type="text" name="bestellung_menge" id="bestellung-menge"></p>';
        $html_output .= '<p><label for="bestellung-standort">An welchen Standort sollen die Werbemittel geschickt werden?</label><br>';
        $html_output .= '<select name="bestellung_standort" id="bestellung-standort">';

        while($standorte_query->have_posts()):
            $standorte_query->the_post();
            $html_output .= '<option value="' . get_the_id() . '">' . get_the_title() . '</option>';
        endwhile;

        $html_output .= '</select></p><input type="submit" value="Bestellung aufgeben"></form>';

    endif;

    /* reset the data */
    wp_reset_postdata();

    return $html_output;
}
add_shortcode('werbemittel_liste', 'fest_werbemittel_shortcode');
