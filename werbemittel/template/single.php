<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */


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

/* Prepare the WP_Query for the Standort CPT */
$standorte_query_args = array(
    'post_type'         => 'standort',
    'post_status'       => 'publish'
);
$standorte_query = new WP_Query($standorte_query_args);

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
        // Start the loop.
        while (have_posts()) : the_post();

            $title = get_the_title();
            $href = get_permalink();
            $beschreibung = get_post_meta(get_the_id(), 'werbemittel_beschreibung', true);
            $bild = get_post_meta(get_the_id(), 'werbemittel_bild', true);
            $stueckpreis = get_post_meta(get_the_id(), 'werbemittel_stueckpreis', true);
            $lager = get_post_meta(get_the_id(), 'werbemittel_lager', true);

            /* only execute this code if the form was submitted */
            if($submitted):

                //php mailer variables
                $current_user = wp_get_current_user();
                $menge = esc_attr($_POST['bestellung_menge']);
                $standort_id = esc_attr($_POST['bestellung_standort']);
                $standort = get_post($standort_id);
                $strasse = get_post_meta($standort_id, 'standort_strasse', true);
                $ort = get_post_meta($standort_id, 'standort_ort', true);
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . $current_user->user_email
                );
                $subject = 'Bestellung für ' . $title;
                $message = 'Es ist eine Bestellung für Werbemittel eingetroffen.<br><br>';
                $message .= '<table>';
                $message .= '<tr><td><b>Bestellt von</b></td><td>' . $current_user->user_name . '</td></tr>';
                $message .= '<tr><td valign="top"><b>Standort</b></td><td>' . $standort->post_title . '<br>' . $strasse . '<br>' . $ort . '</td></tr>';
                $message .= '<tr><td><b>Artikel</b></td><td>' . $title . '</td></tr>';
                $message .= '<tr><td><b>Menge</b></td><td>' . $menge . '</td></tr>';
                $message .= '<tr><td><b>St&uuml;ckpreis</b></td><td>' . number_format($stueckpreis, 2, ',', '.') . ' &euro;</td></tr>';
                $message .= '<tr><td><b>Gesamtpreis</b></td><td>' . number_format($stueckpreis * $menge, 2, ',', '.') . ' &euro;</td></tr>';
                $message .= '</table>';

                /* validate the input and sent the mail */
                if(empty($menge)):
                    my_contact_form_generate_response("error", $missing_menge); // missing value for menge
                else:
                    $sent = wp_mail(get_option('werbemittel_to'), $subject, $message, $headers); // sent email
                    if($sent):
                        my_contact_form_generate_response("success", $message_sent); // message sent!
                    else:
                        my_contact_form_generate_response("error", $message_unsent); // message wasn't sent
                    endif;
                endif;
            endif;


            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <?php
                    echo wp_get_attachment_image($bild, "thumbnail", false, array("class"=>"werbemittel-bild", "style"=>"float:left"));
                    echo '<p>'. $beschreibung. '<br>'.'</p>';
                    echo '<p>' . 'Auf Lager: <b>' . $lager . ' St&uuml;ck</b><br>St&uuml;ckpreis: <b>' . number_format($stueckpreis, 2, ',', '.') . ' &euro;</b></p>';
                    ?>

                    <hr>
                    <p>Sollten Sie welche für Ihren Standort bestellen wollen, kann dies über dieses Formular getan werden.</p>

                    <?php echo $response; ?>
                    <form action="<?php the_permalink(); ?>" method="post">
                        <p>
                            <label for="bestellung-menge">Menge:</label>
                            <input type="text" name="bestellung_menge" id="bestellung-menge">
                        </p>
                        <p>
                            <label for="bestellung-standort">An welchen Standort sollen die Werbemittel geschickt werden?</label><br>
                            <select name="bestellung_standort" id="bestellung-standort">
                                <?php
                                /* iterate through all the Standorte */
                                while($standorte_query->have_posts()):
                                    $standorte_query->the_post();
                                    echo '<option value="' . get_the_id() . '">' . get_the_title() . '</option>';
                                endwhile;
                                ?>
                            </select>
                        </p>
                        <input type="hidden" name="submitted" value="1">
                        <input type="submit" value="Bestellung aufgeben">
                    </form>

                </div><!-- .entry-content -->

                <?php
                // Author bio.
                if (is_single() && get_the_author_meta('description')) :
                    get_template_part('author-bio');
                endif;
                ?>

                <footer class="entry-footer">
                    <?php edit_post_link(__('Edit', 'twentyfifteen'), '<span class="edit-link">', '</span>'); ?>
                </footer><!-- .entry-footer -->

            </article><!-- #post-## -->

            <?php

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

            // End the loop.
        endwhile;
        ?>

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
