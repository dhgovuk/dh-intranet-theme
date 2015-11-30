<?php
class EB_Widget extends WP_Widget {
    /**
     * Widget constructor
     */
    function EB_Widget() {
        $widget_name = __( 'Eventbrite event widget', 'eventbrite' );
        $widget_vars = array(
            'classname' => 'eventbrite-event-widget',
            'description' => __( 'Show an Eventbrite event widget', 'scrm_mc_wg' )
        );
        parent::WP_Widget( "eventbrite-event-widget", $widget_name, $widget_vars );
    }
    
    /**
     * Widget content
     */
    function widget( $args, $instance ) {
        $args['title'] = '';
        
        if( isset( $instance['title'] ) )
            $args['title'] = apply_filters( 'widget_title', $instance['title'] );
        
        if( isset( $instance['event'] ) )
            $args['event'] = apply_filters( 'eventbrite_widget_event', $instance['event'] );

        $vars['args'] = $args;
        EBO::template_render( 'widget_body', $vars, true );
    }
    
    /**
     * Widget on update handler
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['event'] = intval( $new_instance['event'] );
        
        return $instance;
    }
    
    /**
     * Widget form
     */
    function form( $instance ) {
        // Get some events
        $vars['events'] = apply_filters( 'eventbrite_widget_events', array() );
        
        $vars['title'] = '';
        $vars['title_id'] = $this->get_field_id( 'title' );
        $vars['title_name'] = $this->get_field_name( 'title' );
        
        $vars['event'] = '';
        $vars['event_id'] = $this->get_field_id( 'event' );
        $vars['event_name'] = $this->get_field_name( 'event' );
        
        if( isset( $instance['title'] ) )
            $vars['title'] = sanitize_text_field( $instance['title'] );
        
        if( isset( $instance['event'] ) )
            $vars['event'] = intval( $instance['event'] );
        
        EBO::template_render( 'widget_form', $vars, true );
    }
}
?>