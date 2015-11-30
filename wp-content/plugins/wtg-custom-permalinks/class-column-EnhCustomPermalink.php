<?php
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
* GUID Column
*
*/
class CPAC_Column_EnhCustomPermalink extends CPAC_Column {
 
    function __construct( $storage_model ) {
     
    // Identifier, pick an unique name.
    $this->properties['type'] = 'column-EnhCustomPermalink';
     
    // Default column label.
    $this->properties['label'] = __( 'Custom Permalink' );
    // (optional) You can make it support sorting with the pro add-on enabled. Sorting will done by it's raw value.
    $this->properties['is_sortable'] = true;
     
    // Do not change this part.
    parent::__construct( $storage_model );
    }
     
    /**
    * Get value
    *
    * Returns the value for the column.
    *
    * @since 2.0.0
    * @param int $id ID
    * @return string Value
    */
    function get_value( $post_id ) {
     
    // get guid
    $ecp = $this->get_raw_value( $post_id );
        
     
    // optionally you can change the display of the value.
    return '<a href="' . $ecp . '">' . $ecp . '</a>';
    }
     
    /**
    * Get the raw, underlying value for the column
    * Not suitable for direct display, use get_value() for that
    *
    * @since 2.0.3
    * @param int $id ID
    * @return mixed Value
    */
    function get_raw_value( $post_id ) {

        $ecp = get_post_meta($post_id,"custom_permalink",true);
        
        if($ecp == ""){
            $post_data = get_post($post_id, ARRAY_A);
            $ecp = $post_data['post_name']; 
            
        }
        
        return $ecp;
    }
}// end CPAC_Column_Post_EnhCustomPermalink