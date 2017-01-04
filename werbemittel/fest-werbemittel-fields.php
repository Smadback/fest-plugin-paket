<?php



function fest_add_custom_werbemittel_metabox() {
    add_meta_box(
        'fest_werbemittel_meta',          // id
        'Allgemeine Informationen',       // title
        'fest_werbemittel_meta_callback', // callback function
        'werbemittel',                    // screen on which to show the box
        'normal',                         // horizontal positioning
        'high'                            // priority for positioning (vertical) of the metabox
    );

    add_meta_box(
        'fest_werbemittel_bestand-id',
        'Bestand aktualisieren',
        'fest_werbemittel_bestand_cb',
        'werbemittel',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fest_add_custom_werbemittel_metabox');

function fest_werbemittel_meta_callback() {
    global $post;
    wp_nonce_field(basename(__FILE__), 'fest_werbemittel_nonce');
    $fest_stored_meta = get_post_meta($post->ID);
?>

    <div>
        <div class="meta-row">
            <div class="meta-th">
                <span>Beschreibung</span>
            </div>
        </div>
        <div class="meta-editor">
            <?php
                $content = get_post_meta($post->ID, 'werbemittel_beschreibung', true);
                $editor = 'werbemittel_beschreibung';
                $settings = array(
                    'textarea_rows' => 8,
                    'media_buttons' => false
                );

                wp_editor($content, $editor, $settings);
            ?>
        </div>

        <div class="meta-row">
            <div class="meta-th">
                <label for="werbemittel-stueckpreis" class="fest-row-title">St&uuml;ckpreis</label>
            </div>
            <div class="meta-td">
                <input type="text" name="werbemittel_stueckpreis" id="werbemittel-stueckpreis" value="<?php if (!empty ($fest_stored_meta['werbemittel_stueckpreis'])) echo esc_attr($fest_stored_meta['werbemittel_stueckpreis'][0]); ?>"/>
            </div>
        </div>

        <div class="meta-row">
            <div class="meta-th">
                <label for="werbemittel_lager" class="fest-row-title">Menge auf Lager</label>
            </div>
            <div class="meta-td">
                <input type="text" name="werbemittel_lager" id="werbemittel-lager" value="<?php if (!empty ($fest_stored_meta['werbemittel_lager'])) echo esc_attr($fest_stored_meta['werbemittel_lager'][0]); ?>"/>
            </div>
        </div>

        <div class="meta-row">
            <div class="meta-th">
                <label for="werbemittel-bild" class="fest-row-title">Bild hochladen</label>
            </div>
            <div class="meta-td">
                <input id="werbemittel-bild" type="text" name="werbemittel_bild" value="<?php if (!empty ($fest_stored_meta['werbemittel_bild'])) echo esc_attr($fest_stored_meta['werbemittel_bild'][0]); ?>" />
                <input id="werbemittel-bild-button" type="button" value="Bild auswählen" />
            </div>
        </div>


    </div>
<?php
}


function fest_werbemittel_bestand_cb() {
    wp_nonce_field(basename(__FILE__), 'fest_werbemittel_nonce');
    ?>

    <div>
        <div class="meta-row">
            <div class="meta-th">
                <label for="werbemittel-hinzufuegen" class="fest-row-title">Vom Bestand hinzufuegen:</label>
            </div>
            <div class="meta-td">
                <input type="number" name="werbemittel_hinzufuegen" id="werbemittel-hinzufuegen"/>
            </div>
        </div>

        <div class="meta-row">
            <div class="meta-th">
                <label for="werbemittel-abziehen" class="fest-row-title">Vom Bestand abziehen:</label>
            </div>
            <div class="meta-td">
                <input type="number" name="werbemittel_abziehen" id="werbemittel-abziehen" />
            </div>
        </div>
    </div>
    <?php
}



function fest_werbemittel_meta_save($post_id) {
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = isset($_POST['fest_werbemittel_nonce']) && wp_verify_nonce($_POST['fest_werbemittel_nonce'], basename(__FILE__)) ? true : false;

    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }

    if(isset($_POST['werbemittel_beschreibung'])) {
        update_post_meta($post_id, 'werbemittel_beschreibung', sanitize_text_field($_POST['werbemittel_beschreibung']));
    }
    if(isset($_POST['werbemittel_stueckpreis'])) {
        update_post_meta($post_id, 'werbemittel_stueckpreis', sanitize_text_field($_POST['werbemittel_stueckpreis']));
    }
    if(isset($_POST['werbemittel_lager'])) {
        update_post_meta($post_id, 'werbemittel_lager', sanitize_text_field($_POST['werbemittel_lager']));
    }
    if(isset($_POST['werbemittel_hinzufuegen']) || isset($_POST['werbemittel_abziehen']) ) {
        $neuer_bestand = intval(sanitize_text_field($_POST['werbemittel_lager']));

        if($_POST['werbemittel_hinzufuegen'] > 0):
            $neuer_bestand = $neuer_bestand + intval(sanitize_text_field($_POST['werbemittel_hinzufuegen']));
            update_post_meta($post_id, 'werbemittel_lager', sanitize_text_field($neuer_bestand));
        endif;

        if($_POST['werbemittel_abziehen'] > 0):
            $neuer_bestand = $neuer_bestand - intval(sanitize_text_field($_POST['werbemittel_abziehen']));
            update_post_meta($post_id, 'werbemittel_lager', sanitize_text_field($neuer_bestand));
        endif;
    }
    if(isset($_POST['werbemittel_bild'])) {
        update_post_meta($post_id, 'werbemittel_bild', sanitize_text_field($_POST['werbemittel_bild']));
    }

}
add_action('save_post', 'fest_werbemittel_meta_save');