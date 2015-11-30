<?php
class EB {
    // Our post type
    public static $post_type = 'event';
    
    // Our post type archive slug
    public static $post_type_slug = 'events-archive';
    
    // Our post type taxonomy
    public static $post_type_taxonomy = 'location';
    
    // Our category taxonomy
    public static $post_type_cat = 'eventcat';
    
    // Post type meta keys
    public static $meta_keys = array(
        'event_id',
        'start_date',
        'end_date',
        'timezone',
        'privacy',
        'venue_id',
        'organizer_id',
        'capacity',
        'currency',
        'event_template',
        'eventbrite_ready',
        'accept_paypal',
        'accept_google',
        'accept_check',
        'accept_cash',
        'accept_invoice',
        'paypal_email',
        'google_merchant_id',
        'google_merchant_key',
        'instructions_check',
        'instructions_cash',
        'instructions_invoice',
        'status',
        'custom_header',
        'custom_footer',
        'organizer_name',
        'organizer_description',
        'venue_organizer_id',
        'venue',
        'adress',
        'city',
        'region',
        'postal_code',
        'country_code'
    );
    
    // Transient expiration seconds
    public static $cache_expiration = 0;
    
    /**
     * Static constructor
     */
    function init() {
        if( EBO::get_options( 'check' ) )
            add_action( 'init', array( __CLASS__, 'post_type' ) );
        add_action( 'save_post', array( __CLASS__, 'save' ) );
        add_action( 'save_post', array( __CLASS__, 'save_ticket' ) );
        add_action( 'init', array( __CLASS__, 'assets' ) );
        add_action( 'single_template', array( __CLASS__, 'load_template' ) );
        /*
         * add duplicate event functionality
         */
        add_action('admin_action_rd_duplicate_post_as_draft', array( __CLASS__, 'rd_duplicate_post_as_draft'));        
        add_filter('post_row_actions', array( __CLASS__, 'rd_duplicate_post_link'), 10, 2);
    }
    
    /**
     * post_type()
     * 
     * Register our post type
     */
    function post_type() {
        register_post_type( self::$post_type, array(
            'public' => true,
            'map_meta_cap' => true,
            'rewrite' => array( 'slug' => self::$post_type ),
            'supports' => array( 'title', 'editor', 'thumbnail', 'author' ),
            'register_meta_box_cb' => array( __CLASS__, 'meta_boxes' ),
            'show_ui' => true,
            'has_archive' => self::$post_type_slug,
            'labels' => array(
                'name' => __( 'Events', 'eventbrite' ),
                'singular_name' => __( 'Event', 'eventbrite' ),
                'add_new_item' => __( 'New Event', 'eventbrite' ),
                'edit_item' => __( 'Edit Event', 'eventbrite' ),
            )
        ) );
        
        register_taxonomy( self::$post_type_taxonomy, array( self::$post_type ), array(
            'hierarchical' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => self::$post_type_taxonomy ),
            'labels' => array(
                'name' => _x( 'Locations', 'taxonomy for events location/city', 'eventbrite' ),
                'singular_name' => _x( 'Location', 'taxonomy for events location/city', 'eventbrite' ),
                'all_items' => __( 'All Locations', 'eventbrite' ),
                'edit_item' => __( 'Edit Location', 'eventbrite' ),
                'update_item' => __( 'Update Location', 'eventbrite' ),
                'add_new_item' => __( 'New Location', 'eventbrite' ),
                'new_item_name' => __( 'New Location Name', 'eventbrite' ),
                'separate_items_with_commas' => __( 'Separate locations with commas', 'eventbrite' ),
                'add_or_remove_items' => __( 'Add or Remove Locations', 'eventbrite' ),
                'choose_from_most_used' => __( 'Choose from the most used locations', 'eventbrite' ),
                'parent_item' => __( 'Parent Location/State', 'eventbrite' ),
                'parent_item_colon' => __( 'Parent Location/State:', 'eventbrite' )
            )
        ));
        
        register_taxonomy( self::$post_type_cat, self::$post_type, array(
            'label' => __( 'Events Category', 'eventbrite' ),
            'show_ui' => true,
            'query_var' => true,
            'hierarchical' => true,
            'rewrite' => array( 'slug' => 'event-category' ),
        ));
    }
    
    
    /**
     * Hooks into `edit_post` on `post_type` event to load the required enqueues
     */
    function assets() {
        wp_register_script(
            'jquery-datepicker-slider',
            EB_WEB_ROOT . '/assets/js/jquery-ui-custom/jquery-ui-1.10.4.min.js',
            array( 'jquery' ),
            '1.8.13'
        );
        wp_register_script(
            'timepicker',
            EB_WEB_ROOT . '/assets/js/timepicker/jquery-ui-timepicker-addon.js',
            array( 'jquery', 'jquery-datepicker-slider' ),
            '1.2',
			true
        );
        
        wp_register_style(
            'jquery-datepicker-slider-css',
            EB_WEB_ROOT . '/assets/js/jquery-ui-custom/smoothness/jquery-ui-1.8.13.custom.css',
            array(),
            '1.8.13'
        );

//        if ( !isset( $_GET['post'] ) || get_post_type( $_GET['post'] ) != self::$post_type )
//            return;

        wp_enqueue_script(
            'eventbrite-admin',
            EB_WEB_ROOT . '/assets/js/eventbrite-admin.js',
            array( 'timepicker', 'jquery-datepicker-slider' ),
            EB_VERSION,
            true
        );
         
        wp_enqueue_style(
            'timepicker',
            EB_WEB_ROOT . '/assets/js/timepicker/jquery-ui-timepicker-addon.css',
            array( 'jquery-datepicker-slider-css' ),
            '0.9.5'
        );
    }
    
    /**
     * meta_boxes( $post )
     * 
     * Activate the meta boxes
     * @param Object $post, the post/page object
     */
    function meta_boxes( $post ) {
        // Basic settings box
        add_meta_box( 
            'event_details',
            __( 'Details', 'eventbrite' ),
            array( __CLASS__, 'details_box' ),
            self::$post_type,
            'side'
        );
        // Venue settings box
        add_meta_box( 
            'event_venue',
            __( 'Venue', 'eventbrite' ),
            array( __CLASS__, 'venue_box' ),
            self::$post_type,
            'side'
        );
        // Organizer settings box
        add_meta_box( 
            'event_organizer',
            __( 'Organizer', 'eventbrite' ),
            array( __CLASS__, 'organizer_box' ),
            self::$post_type
        );
        
        // Ticket settings box
        add_meta_box( 
            'event_ticket',
            __( 'Tickets', 'eventbrite' ),
            array( __CLASS__, 'event_ticket' ),
            self::$post_type
        );
    }
    
    /**
     * details_box( $post )
     * 
     * Render the details meta box
     * @param Object $post, the post/page object
     */
    function details_box( $post ) {
        EBO::template_render(
            'details_box',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * venue_box( $post )
     * 
     * Render the venue meta box
     * @param Object $post, the post/page object
     */
    function venue_box( $post ) {
        EBO::template_render(
            'venue_box',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * organizer_box( $post )
     * 
     * Render the organizer meta box
     * @param Object $post, the post/page object
     */
    function organizer_box( $post ) {
        EBO::template_render(
            'organizer_box',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * event_ticket( $post )
     * 
     * Render the ticket meta box
     * @param Object $post, the post/page object
     */
    function event_ticket( $post ) {
        EBO::template_render(
            'ticket_box',
            self::get_tickets( $post->ID )
        );
    }
    
    /**
     * header_box( $post )
     * 
     * Render the header meta box
     * @param Object $post, the post/page object
     */
    function header_box( $post ) {
        EBO::template_render(
            'header_box',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * footer_box( $post )
     * 
     * Render the footer meta box
     * @param Object $post, the post/page object
     */
    function footer_box( $post ) {
        EBO::template_render(
            'footer_box',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * event_template( $post )
     * 
     * Render the template selector meta box
     * @param Object $post, the post/page object
     */
    function event_template( $post ) { 
        EBO::template_render(
            'event_template',
            self::get_settings( $post->ID )
        );
    }
    
    /**
     * load_template( $template )
     * This will load the event post template if any
     *
     * @param String $template, initial template path
     * @return String, new template path
     */
    function load_template( $template ) {
        global $wp_query;
        $post = $wp_query->get_queried_object();
        if ( $post ) {
            $post_template = get_post_meta( $post->ID, '_post_template', true );
            if( !empty( $post_template ) && $post_template != 'default' ) {
                $template = get_stylesheet_directory() . "/{$post_template}";
                if( !file_exists( $template ) )
                    $template = get_template_directory() . "/{$post_template}";
            }
        }
        return $template;
    }
    
    /**
     * get_settings( $post_id )
     * 
     * Fetch the settings for given ID
     * @param Int $post_id, the ID of the post
     * @return Mixed $settings, the fetched settings array
     */
    function get_settings( $post_id = null ) {
        $transient_name = self::$post_type . '_' . $post_id;
        // Check for a cache
        $settings = FALSE; // get_transient( $transient_name );  Disabled for now as this populates with the event_id
		// as blank before getting the key and putting it into the database. @todo tidy this up!
        
        if( !$settings ) {
            $settings['gmt_offset'] = get_option( 'gmt_offset', 'UTC+0' );
            $settings['currency_list'] = apply_filters( 'eventbrite_currency_list', array( 'USD', 'EUR' ) );
            
            if( !$post_id )
                return $settings;
            
            foreach ( self::$meta_keys as $k )
			{
				$settings[$k] = get_post_meta( $post_id, $k, true );
			}

            // Cache the data
            // set_transient( $transient_name, $settings, self::$cache_expiration );
        }


        // Get post template
        $settings['post_template'] = get_post_meta( $post_id, '_post_template', true );
        // Add a filter to be able later to populate organizers list
        $settings['organizers_list'] = apply_filters( 'eventbrite_organizers_list', array() );
        // Add a filter to be able later to populate venues list
        $settings['venues_list'] = apply_filters( 'eventbrite_venues_list', array() );
        // Check for any Eventbrite errors
        $settings['eventbrite_errors'] = get_transient( 'eventbrite_errors' . $post_id );
        return $settings;
    }
    
    /**
     * get_tickets( $post_id )
     * 
     * Fetch the tickets for given ID
     * @param Int $post_id, the ID of the post
     * @return Mixed $settings, the fetched settings array
     */
    function get_tickets( $post_id = null ) {
        $data = array_fill_keys( 
            array(
                // Dummy ticket to skip notices
                'ticket_id',
                'is_donation',
                'name',
                'description',
                'price',
                'quantity',
                'start_sales',
                'end_sales',
                'include_fee',
                'min',
                'max',
            )
        , null );
        
        if( !$post_id )
            return null;
        
        $tickets = get_post_meta( $post_id, 'ticket' );
        $all_tickets = array();
        
        if( !empty( $tickets ) )
            foreach( $tickets as $t )
                if( $t != null )
                    $all_tickets[md5( $t )] = maybe_unserialize( $t );
        
        $data['ticket_fields'] = $data;
        // Add a filter to be able later to populate tickets list
        $data['tickets'] = apply_filters( 'eventbrite_tickets_list', $all_tickets );
        
        return $data;
    }
    
    /**
     * save( $post_id )
     * 
     * Save sent data for current $post_id
     * @param Int $post_id, the ID of the post
     * @return Int $post_id, the ID of the post
     */
    function save( $post_id ) {

        // Check if we are deleting an event from wordpress. We cannot
        // delete an event from Eventbrite, but we can unpublish it.
        if($post_id && empty($_POST) && $_GET['action'] == 'trash') {
            $event_id = get_post_meta($post_id, 'event_id', TRUE);

            if($event_id) {
                $oauth_token = get_option('eventbrite_oauth_token');

                $EBAPI_V3 = new EBAPI_V3();
                $EBAPI_V3->event_unpublish($event_id, $oauth_token);
            }
        }

        $file_id = null;
        $restrict_to = null;
        $new_settings = null;
        
        // Delete any previous errors
        delete_transient( 'eventbrite_errors' . $post_id );
        
        if ( isset( $_POST['eventbrite_nonce'] ) && !wp_verify_nonce( $_POST['eventbrite_nonce'], 'eventbrite' ) )
            return $post_id;
        
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
        
        if( isset( $_POST['event'] ) && !empty( $_POST['event'] ) )
            $new_settings = $_POST['event'];
        else
            return $post_id;
        
        foreach( self::$meta_keys as $k )
            if( isset( $new_settings[$k] ) )
                if( in_array( $k, array( 'privacy', 'organizer_id', 'venue_id', 'venue_organizer_id', 'capacity' ) ) )
                    if( $new_settings[$k] != '' )
                        update_post_meta( $post_id, $k, intval( $new_settings[$k] ) );
                    else
                        update_post_meta( $post_id, $k, '' );
                else
                    if ( in_array( $k, array( 'custom_header', 'custom_footer', ) ) )
                        update_post_meta( $post_id, $k, wp_filter_post_kses( $new_settings[$k] ) );
                    else
                        update_post_meta( $post_id, $k, sanitize_text_field( $new_settings[$k] ) );
        
        foreach ( array_slice( self::$meta_keys, 9, 7 ) as $k )
            if ( !isset( $new_settings[$k] ) )
                update_post_meta( $post_id, $k, '0' );
            else
                if( $k != 'event_template' || $new_settings['event_template'] != $new_settings['eventbrite_ready'] )
                    update_post_meta( $post_id, $k, '1' );
        
        // Save post template
        if( isset( $_POST['post_template'] ) )
            update_post_meta( $post_id, '_post_template', sanitize_text_field( $_POST['post_template'] ) );
        
        // Make sure no cached data exists
        delete_transient( self::$post_type . '_' . $post_id );

        // Trigger eventbrite save hook
        do_action( 'eventbrite_save', $post_id, $new_settings['eventbrite_ready'] );
        
        // Check if the template file is on place
        EBO::check_template();
        
        return $post_id;
    }
    
    /**
     * save_ticket( $post_id )
     * 
     * Save sent data for current $post_id
     * @param Int $post_id, the ID of the post
     * @return Int $post_id, the ID of the post
     */
    function save_ticket( $post_id ) {
        $ticket_keys = array(
            'ticket_id',
            'is_donation',
            'name',
            'description',
            'price',
            'quantity',
            'start_sales',
            'end_sales',
            'include_fee',
            'min',
            'max'
        );

        $new_tickets = null;
        
        if ( isset( $_POST['eventbrite_ticket_nonce'] ) && !wp_verify_nonce( $_POST['eventbrite_ticket_nonce'], 'eventbrite' ) )
            return $post_id;
        
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
        
        if( isset( $_POST['tickets'] ) && !empty( $_POST['tickets'] ) )
            $new_tickets = $_POST['tickets'];
        else
            return $post_id;

        // Build the sanitized ticket data
        if( $new_tickets ) {
            // Cleanup the old tickets
            delete_post_meta( $post_id, 'ticket' );
            // Parse the new ones
            foreach ( $new_tickets as $new_ticket ) {
                $ticket_data = array();
                
                // Sanitize the rest
                foreach ( $ticket_keys as $k )
                    if( isset( $new_ticket[$k] ) ) 
                        if( in_array( $k, array( 'ticket_id', 'quantity', 'min', 'max' ) ) )
                            if( $new_ticket[$k] != '' )
                                $ticket_data[$k] = intval( $new_ticket[$k] );
                            else
                                $ticket_data[$k] = '';
                        else
                            $ticket_data[$k] = sanitize_text_field( $new_ticket[$k] );

                // Minimum ticket quantity per order fix.
                if( ! $ticket_data['min'])
                    $ticket_data['min'] = 1;

                // Total ticket quantity fix.
                if( ! $ticket_data['quantity'])
                    $ticket_data['quantity'] = 10;

                // Ticket name cannot be empty.
                // Apply this to the very first ticket.
                if(count($new_tickets) == 1) {
                    if( ! $ticket_data['name'])
                        $ticket_data['name'] = 'Tickets';
                }

                // Ticket description should not be null.
                // Set it to a single space.
                if( ! $ticket_data['description'])
                    $ticket_data['description'] = ' ';
                
                // Check checkboxes
                if( isset( $new_ticket['is_donation'] ) )
                    $ticket_data['is_donation'] = 1;
                if( isset( $new_ticket['include_fee'] ) )
                    $ticket_data['include_fee'] = 1;

                // Price should be float
                $ticket_data['price'] = esc_attr( $ticket_data['price'] );

                // Save ticket, Skip empty tickets
                if( $ticket_data['name'] ) {
                    // Filter for ticket data
                    $ticket_data = apply_filters( 'eventbrite_save_ticket', $ticket_data, $post_id );
                    $ticket_data = maybe_serialize( $ticket_data );
                    add_post_meta( $post_id, 'ticket', $ticket_data );
                }
            }
        }

        return $post_id;
    }

    /*
     * Function creates post duplicate as a draft and redirects then to the edit post screen
     */
    function rd_duplicate_post_as_draft(){
        global $wpdb;
        if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
            wp_die('No post to duplicate has been supplied!');
        }
     
        /*
         * get the original post id
         */
        $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
        $post_id = (int)$post_id;
        /*
         * and all the original post data then
         */
        $post = get_post( $post_id );
     
        /*
         * if you don't want current user to be the new post author,
         * then change next couple of lines to this: $new_post_author = $post->post_author;
         */
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;
     
        /*
         * if post data exists, create the post duplicate
         */
        if (isset( $post ) && $post != null) {
     
            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title,
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );
     
            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post( $args );
     
            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
            }
     
            /*
             * duplicate all post meta
             */
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos)!=0) {       
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;                   
                    //exclude the event_id
                    if ($meta_key != 'event_id') {
                        $meta_value = addslashes($meta_info->meta_value);
                        $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                    }                   
                }
                $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }
     
     
            /*
             * finally, redirect to the edit post screen for the new draft
             */
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        } else {
            wp_die('Post creation failed, could not find original post: ' . $post_id);
        }
    }

     
    /*
     * Add the duplicate link to action list for post_row_actions
     */
    function rd_duplicate_post_link( $actions, $post ) {

        global $current_user;

        if (current_user_can('edit_posts')) {
            if ($post->post_type =="event"){
                $actions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
            }           
        }
        return $actions;
    }

}
