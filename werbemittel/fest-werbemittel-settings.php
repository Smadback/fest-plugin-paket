<?php


/* Settings Menu Item for Werbemittel */
function fest_werbemittel_settings_menu() {
    add_submenu_page('edit.php?post_type=werbemittel', 'Einstellungen', 'Einstellungen', 'manage_options',
        'werbemittel_einstellungen', 'fest_werbemittel_settings_page');
}
add_action('admin_menu', 'fest_werbemittel_settings_menu' );




/*  */
function fest_werbemittel_settings_init() {
    /* register the new option within the option group Werbemittel with the name werbemittel_to */
    register_setting('werbemittel', 'werbemittel_to');
    register_setting('werbemittel', 'werbemittel_subject');
    register_setting('werbemittel', 'werbemittel_message');

    /* add a section for the settings with the name Werbemittel Einstellungen */
    add_settings_section('werbemittel_section_settings', 'Werbemittel Einstellungen', 'werbemittel_section_settings_cb',
        'werbemittel_einstellungen');

    /* Setting Fields for Werbemittel settings */
    add_settings_field( 'werbemittel_field_to', 'Bestell Empfänger', 'werbemittel_field_to_cb',
        'werbemittel_einstellungen', 'werbemittel_section_settings');
    add_settings_field( 'werbemittel-field-subject-id', 'Betreff der gesendeten Mail', 'werbemittel_field_subject_cb',
        'werbemittel_einstellungen', 'werbemittel_section_settings');
    add_settings_field( 'werbemittel-field-message-id', 'Die gesendete Nachricht', 'werbemittel_field_message_cb',
        'werbemittel_einstellungen', 'werbemittel_section_settings');
}
add_action('admin_init', 'fest_werbemittel_settings_init');



/*  */
function werbemittel_section_settings_cb($args) {
}

/*  */
function werbemittel_field_to_cb($args) {

    $option = get_option('werbemittel_to');
    $users = get_users( array( 'fields' => array( 'ID' ) ) );

    echo '<select id="werbemittel-to" name="werbemittel_to">';

    /* iterate through all the Users and set the user who is the current receiver as selected */
    foreach($users as $user){
        $user_meta = get_user_by('id', $user->ID );
        echo '<option value="' . $user_meta->user_email . '"' . (isset($option) ? (selected($option, $user_meta->user_email, false)) : ('')) .'>' . $user_meta->user_firstname . ' ' . $user_meta->user_lastname . '</option>';
    }

    echo '</select>';
}

/*  */
function werbemittel_field_subject_cb($args) {
    $option = get_option('werbemittel_subject');

    echo 'Der Inhalt der Nachricht kann mit PHP geschrieben werden, um dynamischen Inhalt zu erzeugen.';
    wp_editor((isset($option)? $option : ''), 'werbemittel-subject', array('media_buttons'=> false, 'textarea_name'=>'werbemittel_subject', 'textarea_rows' => 2));
}

/*  */
function werbemittel_field_message_cb($args) {
    $option = get_option('werbemittel_message');

    echo 'Der Inhalt der Nachricht kann mit PHP geschrieben werden, um dynamischen Inhalt zu erzeugen.';
    wp_editor((isset($option)? $option : ''), 'werbemittel-message', array('media_buttons'=> false, 'textarea_name'=>'werbemittel_message', 'textarea_rows' => 5));
}




/* Settings Page for Werbemittel */
function fest_werbemittel_settings_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <?php settings_errors(); ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('werbemittel');
            do_settings_sections('werbemittel_einstellungen');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}