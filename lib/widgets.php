<?php

add_action('widgets_init', function () {
    register_sidebar(array(
        'name' => 'Navigation sidebar',
        'id' => 'navigation_sidebar',
        'before_widget' => '<section class="sidenav">',
        'after_widget' => '</section>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
});

add_action('widgets_init', function () {
    register_sidebar(array(
        'name' => 'Homepage events',
        'id' => 'homepage_events',
        'before_widget' => '<section class="events-widget">',
        'after_widget' => '</section>',
    ));
});

add_action('widgets_init', function () {
    register_sidebar(array(
        'name' => 'Policy kit sidebar',
        'id' => 'policy_kit_sidebar',
        'before_widget' => '<section class="policy-kit-widget">',
        'after_widget' => '</section>',
    ));
});
