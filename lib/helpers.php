<?php

// Removes width and height attr from post images to make doing squishy images easier
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );

function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}

function getMonthName($month)
{
    return \Missing\Dates::strftime('2000-'.$month.'-01', '%B', '', 'Etc/UTC');
}

function new_excerpt_length($length) {
  return 20;
}
add_filter('excerpt_length', 'new_excerpt_length');