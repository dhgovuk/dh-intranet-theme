<?php


add_action('init', function () {
    register_post_type(
        'todo-item',
        array(
            'labels' => array(
                'name' => __('Checklist items'),
                'singular_name' => __('Checklist item'),
            ),
            'public' => true,
            'menu_position' => 30,
        )
    );

    register_post_type(
        'local-news',
        array(
            'labels' => array(
                'name' => __('In your building'),
                'singular_name' => __('In your building'),
            ),
            'public' => true,
            'menu_position' => 30,
        )
    );

    register_post_type(
        'policy-step',
        array(
            'labels' => array(
                'name' => __('Policy Steps'),
                'singular_name' => __('Policy Step'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'public' => true,
            'menu_position' => 30,
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
