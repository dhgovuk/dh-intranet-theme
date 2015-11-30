<?php

add_action('init', function () {
    // Make atom the default
    add_filter('default_feed', function () { return 'atom'; });

    // Disable the other formats
    remove_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
    remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
    remove_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
});

// Add it to the output
add_action('wp_head', function () {
    ?>
    <link rel="alternate" type="application/atom+xml" title="<?php echo get_bloginfo('name') ?> Feed" href="<?php echo esc_attr(get_feed_link('atom')) ?>">
    <?php
});
