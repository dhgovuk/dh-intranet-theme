<?php

add_action('init', function () {

    register_post_type(
        'policy-kit',
        array(
            'labels' => array(
                'name' => __('PolicyKit'),
                'singular_name' => __('PolicyKit'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'public' => true,
            'menu_position' => 30,
            'show_ui' => true,
            'hierarchical' => true,
            'has_archive' => true,
        )
    );

    register_post_type(
        'policy-step',
        array(
            'labels' => array(
                'name' => __('Policy Steps'),
                'singular_name' => __('Policy Step'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'public' => true,
            'menu_position' => 60,
            'show_ui' => true,
            'hierarchical' => true,
            'has_archive' => true,
        )
    );

    register_post_type(
        'directorate',
        array(
            'labels' => array(
                'name' => __('Personnel'),
                'singular_name' => __('Personnel'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'public' => true,
            'menu_position' => 30,
        )
    );

    register_taxonomy('news-locale', 'local-news', array(
        'labels' => array(
            'name' => 'Locales',
            'singular_name' => 'Locale',
        ),
        'public' => true,
        'menu_position' => 20,
        'show_ui' => true,
    ));
});
