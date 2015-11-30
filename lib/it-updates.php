<?php

add_action('admin_menu', 'it_updates_menu');
function it_updates_menu()
{
    wp_enqueue_script('script-name', get_stylesheet_directory_uri().'/../assets/js/it-updates.js', array(), uniqid(), true);
    add_menu_page('IT Updates', 'IT Updates', 'manage_options', 'it-updates', 'show_it_updates_backend', 'dashicons-dashboard');
}

function initialize_plugin()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix.'it_updates';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        data_key varchar(55) DEFAULT '' NOT NULL,
        data_value text NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function show_it_updates_backend()
{
    if (isset($_POST['save_changes'])) {
        \DHIntranet\IT_Updates::save_updates($_POST);
    }

    initialize_plugin();

    // Include template.
    get_template_part('partials/it-updates-admin');
}
