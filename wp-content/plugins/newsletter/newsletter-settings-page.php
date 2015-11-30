<?php
/**
 * Class NewsletterSettingsPage
 */
class NewsletterSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Newsletter Settings',
            'manage_options',
            'newsletter-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'newsletter_option_name' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Newsletter Settings</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'newsletter_option_group' );
                do_settings_sections( 'newsletter-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        //register_settings would do this for us, but doing it here so we get a chance to set autoload = no.
        $temp = get_option('newsletter_option_name');
        if (!$temp)
        {
            add_option('newsletter_option_name', 'init', '', 'no');
        }

        register_setting(
            'newsletter_option_group', // Option group
            'newsletter_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'newsletter_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'newsletter-setting-admin' // Page
        );

        add_settings_field(
            'newsletter-admins-checkbox',
            'Only send newsletter to Admins',
            array( $this, 'show_newsletter_settings_form' ),
            'newsletter-setting-admin', // page
            'newsletter_section_id' // section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['newsletter-only-admins'] ) )
            $new_input['newsletter-only-admins'] = sanitize_text_field( $input['newsletter-only-admins'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'For testing purposes only send newsletter to Administrators:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function show_newsletter_settings_form()
    {
        printf(
            '<input type="checkbox" id="newsletter-only-admins" name="newsletter_option_name[newsletter-only-admins]" value="yes" %s />',
            (isset( $this->options['newsletter-only-admins'] ) && $this->options['newsletter-only-admins'] == 'yes')  ? 'checked' : ''
        );
    }
}
?>