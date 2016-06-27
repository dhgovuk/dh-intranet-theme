<?php

namespace DHIntranet;

class Pages
{
    // Search results breadcrumbs
    public static function get_the_breadcrumbs()
    {
        $crumbs = array();

        $ancestors = array_reverse(array_map(
            function ($id) {
                return array(
                    'name' => get_the_title($id),
                    'url' => get_permalink($id),
                );
            },
            get_post_ancestors(get_the_ID())
        ));

        array_reverse($ancestors);
        foreach ($ancestors as $ancestor) {
            $crumbs[] = $ancestor;
        }

        return implode('', array_map(
            function ($crumb) {
                return '<li><a href="'.esc_attr($crumb['url']).'">'.esc_html($crumb['name']).'</a></li>';
            },
            $crumbs)
        );
    }

    public static function the_breadcrumbs()
    {
        echo '<ul>', self::get_the_breadcrumbs(), '</ul>';
    }

    public static function excerpt($text, $length)
    {
        $return = wp_trim_words($text, $length);

        return $return;
    }

    public static function pagination($q = null, $mode = null)
    {
        global $wp_query;

        if ($q === null) {
            $q = $wp_query;
        }

        $wp_query_old = $wp_query;
        $wp_query = $q;

        echo '<div class="paginate">';
        the_posts_pagination(array(
            'mid_size' => 6,
            'prev_text' => 'Older posts',
            'next_text' => 'Newer posts',
        ));
        echo '</div>';

        $wp_query = $wp_query_old;
    }
}
