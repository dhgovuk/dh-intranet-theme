<?php

add_action('init', function() {
    add_filter('default_feed', function() { return 'atom'; });

    remove_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
    remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
    remove_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
    // Remove Emoji script
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    // Remove extra crap from wp_head
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
});

 ?>
