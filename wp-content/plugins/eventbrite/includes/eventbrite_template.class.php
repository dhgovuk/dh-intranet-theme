<?php
class EBT {
    /**
     * Static constructor
     */
    function init() {
        add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );
        //add_action( 'init', array( 'MC_WG', 'subscribe' ) );
    }
    
    /**
     * register_widget()
     *
     * Will register our widgets
     */
    function register_widget() {
        register_widget( "EB_Widget" );
    }
    
    /**
     * get_event( $post_id )
     *
     * Loads event details for the $post_id
     * @param Int $post_id, the post object ID
     * @return Mixed event data
     */
    function get_event( $post_id ) {
        $event_data = EB::get_settings( $post_id );
        return apply_filters( 'get_event', $event_data, $post_id );
    }
}
?>