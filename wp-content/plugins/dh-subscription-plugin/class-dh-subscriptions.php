<?php

class dh_subscriptions
{
    const meta_field = '_per_user_feeds_cats';

    private static $instance;


    public function __construct()
    {
        $this->user = wp_get_current_user();
        $this->categories = self::get_newsletter_categories();
    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get_newsletter_categories()
    {

        $args = array(
            'type' => 'post',
            'child_of' => 0,
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
            'hierarchical' => true,
            'exclude' => '',
            'include' => '',
            'number' => '',
            'taxonomy' => 'category',
            'pad_counts' => false
        );

        $categories = array();

        foreach (get_categories($args) as $key => $cat) {
            $flag = get_field('include_in_newsletter', $cat);

            if ($flag === 'never') {
                continue;
            }
            if ($flag === 'always') {
                $cat->disabled = true;
            }

            $categories[$cat->cat_ID] = $cat;
        }

        return $categories;
    }

    public function get_user_feed_categories()
    {
        $cats = reset(get_user_meta($this->user->ID, self::meta_field));
        return empty($cats) ? array() : $cats;
    }

    public function set_user_feed_categories($categories, $old_categories)
    {
        foreach($categories as $cat) {
            if( property_exists($this->categories[$cat], 'disabled')) {
                throw Exception('cannot subscribe to selected category');
            }
        }

        return update_user_meta($this->user->ID, self::meta_field, array_unique($categories), $old_categories);
    }

    public function set_user_feed_category($category, $enabled = true)
    {
        $old_categories = $this->get_user_feed_categories();
        $categories = $old_categories;

        $key = array_search($category, $categories);
        if($enabled) {
            if($key === false) {
                array_push($categories, $category);
            }
        } else {
            if($key !== false) {
                unset($categories[$key]);
            }
        }

        return $this->set_user_feed_categories($categories, $old_categories);
    }
}