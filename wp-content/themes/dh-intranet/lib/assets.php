<?php

add_action('init', function () {
    remove_action('wp_enqueue_scripts', 'roots_scripts', 100);
});

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script('main', get_template_directory_uri().'/../build/js/main.min.js', array(), false, true);
  wp_enqueue_style('main', get_stylesheet_directory_uri().'/../build/css/main.min.css');
});

// Add in the wp-admin.min.css file with the alternative styling.
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('wp-admin2', get_stylesheet_directory_uri().'/../build/css/wp-admin.min.css');
});

