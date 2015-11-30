<?php

// Search results breadcrumbs
function get_the_breadcrumbs()
{
    $crumbs = array();
    $crumbs[] = array(
        'name' => 'Home',
        'url' => get_bloginfo('url'),
    );

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

    return implode('</li><li>', array_map(
        function ($crumb) {
            return esc_html($crumb['name']);
        },
        $crumbs)
    );
}

function the_breadcrumbs()
{
    echo '<ul><li>', get_the_breadcrumbs(), '</li></ul>';
}

function excerpt($text, $length)
{
    $return = wp_trim_words($text, $length);

    return $return;
}

function pagination($q = null, $mode = null)
{
    global $wp_query;

    if ($q === null) {
        $q = $wp_query;
    }

    $wp_query_old = $wp_query;
    $wp_query = $q;

    echo '<div class="paginate">';
    the_posts_pagination(array(
        'mid_size'  => 6,
        'prev_text' => 'Older posts',
        'next_text' => 'Newer posts',
    ));
    echo '</div>';

    $wp_query = $wp_query_old;
}
