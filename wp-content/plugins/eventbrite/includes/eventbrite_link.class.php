<?php
class EBL {
    // Eventbrite api handler
    var $api;
    
    // Eventbrite options
    var $options;
    
    // Transient expiration seconds
    public static $cache_expiration = 0;
    
    // Errors cache
    var $errors = array();
    
    // Error ID cache
    var $post_id = null;
    
    /**
     * Static constructor, hooks into EB class
     */
    function EBL() {
        $this->options = EBO::get_options();
        $this->api = new EBAPI( $this->options['eventbrite_app_key'], $this->options['eventbrite_user_key'], $this->options['eventbrite_oauth_token'], $this->options['eventbrite_user_id'] );
        
        if ( !empty( $this->options['eventbrite_user_email'] ) )
            $this->api->setUser( $this->options['eventbrite_user_email'] );
        if ( !empty( $this->options['eventbrite_user_pass'] ) )
            $this->api->setPassword( $this->options['eventbrite_user_pass'] );
        
        add_filter( 'eventbrite_organizers_list', array( &$this, 'fill_organizers' ) );
        add_filter( 'eventbrite_venues_list', array( &$this, 'fill_venues' ) );
        add_filter( 'eventbrite_widget_events', array( &$this, 'fill_events' ) );
        add_filter( 'eventbrite_save', array( &$this, 'publish_event' ), 10, 2 );
        add_filter( 'eventbrite_save_ticket', array( &$this, 'save_ticket' ), 10, 2 );
        add_action( 'eventbrite_event_update', array(  &$this, 'payment_update' ) );
        add_action( 'eventbrite_event_update', array(  &$this, 'organizer_update' ), 10, 2 );
        add_action( 'eventbrite_event_update', array(  &$this, 'venue_update' ), 10, 2 );
        add_action( 'save_post', array( &$this, 'on_save_post' ) );
    }
    
    /**
     * fill_organizers( $organizers )
     * 
     * Populate $organizers with data from Eventbrite
     * @param Mixed $organizers, the initial data
     * @return Mixed filled data
     */
    function fill_organizers( $organizers ) {
        // Check for cached data
        $organizers_list = get_transient( 'organizers_list' );
        if( $organizers_list )
            return $organizers_list;
        
        $results = array();
        $query = $this->api->user_list_organizers();
        if( !$this->api->getError() )
            foreach ( $query->organizers as $o )
                $results[] = get_object_vars( $o->organizer );
        
        $organizers = array_merge( $organizers, $results );
        
        // Do some caching
        set_transient( 'organizers_list', $organizers, self::$cache_expiration );
        
        return $organizers;
    }
    
    /**
     * fill_venues( $venues )
     * 
     * Populate $venues with data from Eventbrite
     * @param Mixed $venues the initial data
     * @return Mixed filled data
     */
    function fill_venues( $venues ) {
        // Check for cached data
        $venues_list = get_transient( 'venues_list' );

        if( $venues_list )
            return $venues_list;
        
        $results = array();
        $query = $this->api->user_list_venues();
        if( !$this->api->getError() )
            foreach ( $query->venues as $v )
                $results[] = get_object_vars( $v->venue );

        $venues = array_merge( $venues, $results );
        
        // Do some caching
        set_transient( 'venues_list', $venues, self::$cache_expiration );
        
        return $venues;
    }
    
    /**
     * fill_events( $events )
     * 
     * Populate $events data from Eventbrite
     * @param Mixed $events the initial data
     * @return Mixed filled data
     */
    function fill_events( $events ) {
        // Check for cached data
        $events_list = get_transient( 'events_list' );
        if( $events_list )
            return $events_list;
        
        $results = array();
        $query = $this->api->user_list_events( array( 'event_statuses' => 'live,started' ) );

        if( !$this->api->getError() )
            foreach ( $query->events as $v )
                $results[] = get_object_vars( $v->event );
        
        $events = array_merge( $events, $results );
        
        // Do some caching
        set_transient( 'events_list', $events, self::$cache_expiration );
        
        return $events;
    }
    
    /**
     * publish_event( $post_id, $yes )
     * 
     * Try to publish/update the event if $yes is true
     * @param Int $post_id, the ID of the post
     * @param Bool $yes, user confirmation to publish event
     */
    function publish_event( $post_id, $yes ) {

        $event = array();
        
        $this->post_id = $post_id;
        
        // Ask for $post_id and user confirmation first
        if( !$post_id || !$yes )
            return;
        
        // Fetch $post_id data
        $event_data = get_post( $post_id );
        $event_meta = EB::get_settings( $post_id );
        $event_tickets = EB::get_tickets( $post_id );

        $event['title'] = apply_filters( 'the_title', $event_data->post_title );
        $event['description'] = apply_filters( 'the_content', $event_data->post_content );
        $event['personalized_url'] = $event_data->post_name;

        if( $event_data->post_status == 'publish' )
            $event['status'] = 'live';
        else
            $event['status'] = 'draft';
        
        foreach ( array_slice( EB::$meta_keys, 1, 8 ) as $k )
            $event[$k] = $event_meta[$k];

        // Load the colors
        foreach ( array_slice( EBO::get_options(), 4, 9 ) as $k => $v )
            $event[ str_replace( 'eventbrite_', '', $k ) ] = str_replace( '#', '', $v );

        // Convert UTC to GMT for Eventbrite
        $event['timezone'] = preg_replace( '/UTC/', 'GMT', $event_meta['timezone'] );

        // Insert venue details before publishing or updating the event.
        // We are creating a new venue each time we update. This is to
        // prevent an address mismatch bug between wordpress and eventbrite
        // which was difficult to track down. Not perfect but it does the job.
        $venue_id = $this->process_venue( $event_meta, $post_id );
        $event['venue_id'] = $venue_id;

        if( $event_meta['event_id'] ) {
            $event['event_id'] = $event_meta['event_id'];
            unset( $event['personalized_url'] ); // Eventbrite doesn't allow updating non-empty
            $this->api->event_update( $event );
        }
        else {
            $response = $this->api->event_new( $event );

            if( !$this->api->getError() ) {
                $event['event_id'] = $response->id;
                update_post_meta( $post_id, 'event_id', $response->id );
            }
        }
        
        // Save the error if any
        $this->saveErrors( $this->api->getError() );
        
        // Register an update hook
        do_action( 'eventbrite_event_update', $event_meta, $post_id );
    }

    /**
     * Add a new venue. Used to add a new venue before
     * publishing a new event. 
     *
     * Ticket DH-185.
     * 
     * @author  Martynas Kveksas martynas.kveksas@wtg.co.uk
     */
    private function process_venue( $event_meta, $post_id ) {

        $venue_details = array();

        $venue_details['organizer_id'] = $event_meta['venue_organizer_id'];
        $venue_details['venue']        = $event_meta['venue'];
        $venue_details['adress']       = $event_meta['adress'];
        $venue_details['city']         = $event_meta['city'];
        $venue_details['region']       = $event_meta['region'];
        $venue_details['postal_code']  = $event_meta['postal_code'];
        $venue_details['country_code'] = $event_meta['country_code'];

        $response = $this->api->venue_new( $venue_details );

        // Return the new venue id.
        return $response->id;
    }
    
    /**
     * save_ticket( $ticket, $post_id )
     *
     * Saves event tickets
     * @param Mixed $ticket, the ticket data
     * @param Int $post_id, the ID of the post
     * @return Mixed ticket data
     */
    function save_ticket( $ticket, $post_id ) {

        $event_id = get_post_meta( $post_id, 'event_id', true );
        $response = null;

        $ticket_id_meta = get_post_meta($post_id, 'ticket_id');

        // If the event is not synced, skip it
        if( !$event_id )
            return $ticket;
        
        // Donations do not require price, quantity, min, max
        if( $ticket['is_donation'] ) {
            unset( $ticket['price'] );
            unset( $ticket['quantity'] );
            unset( $ticket['min'] );
            unset( $ticket['max'] );
        }
        
        // Update/add a ticket
        if( $ticket['ticket_id'] != 0 ) {
            $ticket['id'] = $ticket['ticket_id'];
            unset( $ticket['ticket_id'] );
            $ticket['event_id'] = $event_id;
            $response = $this->api->ticket_update( $ticket );
        }
        // Temporary fix for tickets. We only allow one ticket class
        // per event due to Eventbrite ticket update issue.
        elseif(empty($ticket_id_meta)) {
            unset( $ticket['ticket_id'] );
            $ticket['event_id'] = $event_id;
            $response = $this->api->ticket_new( $ticket );

            if($response->id) {
                add_post_meta($post_id, 'ticket_id', $response->id, TRUE);
            }
        }

        // Check the response
        if( !$this->api->getError() )
            $ticket['ticket_id'] = $response->id;
        
        // Save the error if any
        $this->saveErrors( $this->api->getError() );
        
        return $ticket;
    }
    
    /**
     * payment_update( $event_data )
     *
     * Try to update event payment details
     * @param Mixed $event_data, event details
     */
    function payment_update( $event_data ) {
        // If the event was not published yet, cancel update
        if( !$event_data['event_id'] )
            return;
        
        $payment_options = array();
        $payment_options['event_id'] = $event_data['event_id'];
        
        foreach ( array_slice( EB::$meta_keys, 11, 10 ) as $k )
            $payment_options[$k] = $event_data[$k];
        
        $this->api->payment_update( $payment_options );
        
        // Save the error if any
        $this->saveErrors( $this->api->getError() );
    }
    
    /**
     * organizer_update( $event_data, $post_id )
     * 
     * Try to update event organizer details
     * @param Mixed $event_data, event details
     * @param Int $post_id, the post object ID
     */
    function organizer_update( $event_data, $post_id ) {
        // If the event was not published yet, cancel update
        if( !$event_data['event_id'] )
            return;
        
        $response = null;
        
        $organizer_details = array();
        $organizer_details['id'] = $event_data['organizer_id'];
        $organizer_details['name'] = $event_data['organizer_name'];
        $organizer_details['description'] = $event_data['organizer_description'];

        if( ! empty($event_data['organizer_name'])) {
            if( $event_data['organizer_id'] ) {
                $this->api->organizer_update($organizer_details);
            } else {
                unset( $organizer_details['id'] );
                $response = $this->api->organizer_new( $organizer_details );
                if( !$this->api->getError() )
                    update_post_meta( $post_id, 'organizer_id', $response->id );
            }
        }

        // Save the error if any
        $this->saveErrors( $this->api->getError() );
    }
    
    /**
     * venue_update( $event_data, $post_id )
     * 
     * Try to update event venue details
     * @param Mixed $event_data, event details
     * @param Int $post_id, the post object ID
     */
    function venue_update( $event_data, $post_id ) {
        // If the event was not published yet, cancel update
        if( !$event_data['event_id'] )
            return;

        $response = null;

        $venue_details = array();
        $venue_details['id'] = $event_data['venue_id'];
        foreach ( array_slice( EB::$meta_keys, 28, 7 ) as $k )
            $venue_details[$k] = $event_data[$k];

        $venue_details['organizer_id'] = $event_data['venue_organizer_id'];
        unset( $venue_details['venue_organizer_id'] );

        if( $event_data['venue_id'] ) {
            unset( $venue_details['organizer_id'] );

            $response = $this->api->venue_update( $venue_details );

            if( !$this->api->getError() )
                $this->venue_post_meta_update($response->id, $post_id);
        }
        
        // Save the error if any
        $this->saveErrors( $this->api->getError() );
    }

    function venue_post_meta_update( $venue, $post_id ) {

        update_post_meta( $post_id, 'venue_id',     $venue->id );
        update_post_meta( $post_id, 'venue',        $venue->name );
        update_post_meta( $post_id, 'adress',       $venue->address->address_1);
        update_post_meta( $post_id, 'city',         $venue->address->city );
        update_post_meta( $post_id, 'region',       $venue->address->region );
        update_post_meta( $post_id, 'postal_code',  $venue->address->postal_code );
        update_post_meta( $post_id, 'country_code', $venue->address->country );
    }
    
    /**
     * on_save_post( $post_id )
     * 
     * Save sent data for current $post_id
     * @param Int $post_id, the ID of the post
     * @return Int $post_id, the ID of the post
     */
    function on_save_post( $post_id ) {
        // Force cache expiration
        delete_transient( 'organizers_list' );
        delete_transient( 'events_list' );
        delete_transient( 'venues_list' );
        
        return $post_id;
    }
    
    /**
     * saveErrors( $error )
     *
     * Will save errors if any
     * @param Mixed $error, an error object
     */
    function saveErrors( $error ) {
        if( $error ) {
            $this->errors[] = $error;
            // Update our transient if any erorrs occur
            if( $this->post_id )
                set_transient( 'eventbrite_errors' . $this->post_id, $this->errors, self::$cache_expiration );
        }
    }
}