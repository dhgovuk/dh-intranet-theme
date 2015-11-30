<?php
class EBO {
    // Our option names
    public static $options = array(
        'eventbrite_app_key',
        'eventbrite_user_key',
        'eventbrite_user_email',
        'eventbrite_user_pass',
        'eventbrite_oauth_token',
        'eventbrite_user_id',
        'eventbrite_background_color',
        'eventbrite_text_color',
        'eventbrite_link_color',
        'eventbrite_title_text_color',
        'eventbrite_box_background_color',
        'eventbrite_box_text_color',
        'eventbrite_box_border_color',
        'eventbrite_box_header_background_color',
        'eventbrite_box_header_text_color'
    );
    
    /**
     * Static constructor
     */
    function init() {
        add_action( 'admin_menu', array( __CLASS__, 'menus' ) );
    }
    
    /**
     * Adds menu entries to `wp-admin`
     */
    function menus() {
        add_options_page(
            __( 'Eventbrite', 'eventbrite' ),
            __( 'Eventbrite', 'eventbrite' ),
            'administrator',
            'eventbrite',
            array( __CLASS__, "screen" )
        );
    }
    
    /**
     * Menu screen handler in `wp-admin`
     */
    function screen() {
        $flash = null;
        $vars = array();

        if( isset( $_POST['eventbrite_options_nonce'] ) && wp_verify_nonce( $_POST['eventbrite_options_nonce'], 'eventbrite' ) ) {

            if($_POST['eventbrite_oauth_token']) {

                // Sanitize the oauth token by removing all non alphanumeric characters.
                $_POST['eventbrite_oauth_token'] = preg_replace('#\W#', '', $_POST['eventbrite_oauth_token']);

                $url = 'https://www.eventbriteapi.com/v3/users/me/?token=' . $_POST['eventbrite_oauth_token'];

                $response = json_decode(@file_get_contents($url));

                if($response->id) {
                    // Get user id.
                    $_POST['eventbrite_user_id'] = $response->id;
                }
            }

            foreach( self::$options as $o )
                if( isset( $_POST[$o] ) ) {
                    $v = sanitize_text_field( $_POST[$o] );
                    // Save option
                    update_option( $o, $v );
                }
            $flash = __( 'Eventbrite options saved.', 'eventbrite' );
        }
        
        $vars = self::get_options();
        $vars['flash'] = $flash;
        self::template_render( 'options', $vars );
    }
    
    /**
     * Fetches options
     */
    function get_options( $check = false ) {
        $options = array();
        
        foreach( self::$options as $o )
            $options[$o] = get_option( $o, false );
        
        if( $check )
            if( $options[self::$options[0]] && $options[self::$options[1]] )
                return true;
            else
                return false;
        
        return $options;
    }
    
    /**
     * check_template()
     *
     * Checks if current theme has the template file for this post type
     */
    function check_template() {
        $template_name = 'single-event.php';
        $source_template_file = dirname( __FILE__ ) .'/templates/' . $template_name;
        $theme_folder = get_stylesheet_directory();
        if( !file_exists( $theme_folder . '/' . $template_name ) )
            copy( $source_template_file, $theme_folder . '/' . $template_name );
    }
    
    /**
     * template_render( $name, $vars = null, $echo = true )
     *
     * Helper to load and render templates easily
     * @param String $name, the name of the template
     * @param Mixed $vars, some variables you want to pass to the template
     * @param Boolean $echo, to echo the results or return as data
     * @return String $data, the resulted data if $echo is `false`
     */
    function template_render( $_name, $vars = null, $echo = true ) {
        ob_start();
        if( !empty( $vars ) )
            extract( $vars );
        
        if( !isset( $path ) )
            $path = dirname( __FILE__ ) . '/templates/';
        
        include $path . $_name . '.php';
        
        $data = ob_get_clean();
        
        if( $echo )
            echo $data;
        else
            return $data;
    }
}
?>
