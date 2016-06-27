<?php

namespace DHIntranet;

class DialogSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page.
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Dialog Settings',
            'manage_options',
            'dialog-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback.
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('welcome_option_name');
        ?>
        <div class="wrap">
            <?php screen_icon();
        ?>
            <h2>Dialog Settings</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('dialog_option_group');
        do_settings_sections('dialog-setting-admin');
        submit_button();
        ?>
            </form>
        </div>
        <?php

    }

    /**
     * Register and add settings.
     */
    public function page_init()
    {
        //register_settings would do this for us, but doing it here so we get a chance to set autoload = no.
        $temp = get_option('welcome_option_name');
        if (!$temp) {
            add_option('welcome_option_name', 'init', '', 'no');
        }

        register_setting(
            'dialog_option_group', // Option group
            'welcome_option_name', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Show dialogs for user who are not signed in', // Title
            array($this, 'print_section_info'), // Callback
            'dialog-setting-admin' // Page
        );

        add_settings_field(
            'welcome-checkbox',
            'Show Welcome dialog',
            array($this, 'show_welcome_dialog_callback'),
            'dialog-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed.
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['show-welcome-dialog'])) {
            $new_input['show-welcome-dialog'] = sanitize_text_field($input['show-welcome-dialog']);
        }

        return $new_input;
    }

    /**
     * Print the Section text.
     */
    public function print_section_info()
    {
        echo 'Enable or disable dialogs:';
    }

    /**
     * Get the settings option array and print one of its values.
     */
    public function show_welcome_dialog_callback()
    {
        printf(
            '<input type="checkbox" id="show-welcome-dialog" name="welcome_option_name[show-welcome-dialog]" value="yes" %s />',
            (isset($this->options['show-welcome-dialog']) && $this->options['show-welcome-dialog'] == 'yes')  ? 'checked' : ''
        );
    }
}
