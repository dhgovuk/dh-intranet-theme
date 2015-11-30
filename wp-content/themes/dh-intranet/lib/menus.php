<?php

register_nav_menu('top_nav', 'Top Menu');

/*
* remove menu id (and class) to avoid validation errors with duplicate ids between the desktop and mobile versions
* if the navigation
*/
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var)
{
    return is_array($var) ? array_intersect($var, array('current-menu-item')) : '';
}
