<?php

function fest_add_custom_standort_metabox() {
    add_meta_box(
        'fest_standort_meta', // id
        'Standort',
        'fest_standort_meta_callback', // callback function
        'standort',
        'normal', // horizontal positioning
        'high' // priority for positioning (vertical) of the metabox
    );
}
add_action('add_meta_boxes', 'fest_add_custom_standort_metabox');

function fest_standort_meta_callback() {
    global $post;
    wp_nonce_field(basename(__FILE__), 'fest_standort_nonce');
    $fest_stored_meta = get_post_meta($post->ID);
?>

    <div>
        <div class="meta-row">
            <div class="meta-th">
                <label for="standort-strasse" class="fest-row-title">Stra&szlig;e</label>
            </div>
            <div class="meta-td">
                <input type="text" name="standort_strasse" id="standort-strasse" value="<?php if (!empty ($fest_stored_meta['standort_strasse'])) echo esc_attr($fest_stored_meta['standort_strasse'][0]); ?>"/>
            </div>
        </div>

        <div class="meta-row">
            <div class="meta-th">
                <label for="standort-ort" class="fest-row-title">Ort</label>
            </div>
            <div class="meta-td">
                <input type="text" name="standort_ort" id="standort-ort" value="<?php if (!empty ($fest_stored_meta['standort_ort'])) echo esc_attr($fest_stored_meta['standort_ort'][0]); ?>"/>
            </div>
        </div>
    </div>
<?php
}

function fest_standort_meta_save($post_id) {
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = isset($_POST['fest_standort_nonce']) && wp_verify_nonce($_POST['fest_standort_nonce'], basename(__FILE__)) ? true : false;

    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }

    if(isset($_POST['standort_strasse'])) {
        update_post_meta($post_id, 'standort_strasse', sanitize_text_field($_POST['standort_strasse']));
    }
    if(isset($_POST['standort_ort'])) {
        update_post_meta($post_id, 'standort_ort', sanitize_text_field($_POST['standort_ort']));
    }

}
add_action('save_post', 'fest_standort_meta_save');