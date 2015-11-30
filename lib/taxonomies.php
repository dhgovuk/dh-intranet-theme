<?php

add_action('init', function () {

    register_taxonomy('policystage', ['policy-step'], [
        'update_count_callback' => '_update_post_term_count',
        'hierarchical' => '1',
        'rewrite' => [
            'slug' => 'policystage',
            'with_front' => true,
            'hierarchical' => false,
        ],
        'query_var' => 'policystage',
        'show_ui' => true,
        'show_tagcloud' => false,
        'show_in_nav_menus' => true,
        'labels' => [
            'name' => 'Policy Stages',
            'singular_name' => 'Policy Stage',
            'search_items' => 'Search Policy Stages',
            'popular_items' => 'Popular Policy Stages',
            'all_items' => 'All Policy Stages',
            'parent_item' => 'Parent Policy Stage',
            'parent_item_colon' => 'Parent Policy Stage:',
            'edit_item' => 'Edit Policy Stage',
            'update_item' => 'Update Policy Stage',
            'add_new_item' => 'Add New Policy Stage',
            'new_item_name' => 'New Policy Stage name',
            'separate_items_with_commas' => 'Separate Policy Stages with commas',
            'add_or_remove_items' => 'Add or remove Policy Stages',
            'choose_from_most_used' => 'Choose from the most used Policy Stages',
        ],
        'capabilities' => [
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
        ],
        'public' => true,
    ]);

    register_taxonomy('policytests', ['policy-step'], [
        'update_count_callback' => '_update_post_term_count',
        'hierarchical' => '1',
        'rewrite' => [
            'slug' => 'policytests',
            'with_front' => true,
            'hierarchical' => false,
        ],
        'query_var' => 'policytests',
        'show_ui' => true,
        'show_tagcloud' => false,
        'show_in_nav_menus' => true,
        'labels' => [
            'name' => 'Policy Tests',
            'singular_name' => 'Policy Test',
            'search_items' => 'Search Policy Tests',
            'popular_items' => 'Popular Policy Tests',
            'all_items' => 'All Policy Tests',
            'parent_item' => 'Parent Policy Test',
            'parent_item_colon' => 'Parent Policy Test:',
            'edit_item' => 'Edit Policy Test',
            'update_item' => 'Update Policy Test',
            'add_new_item' => 'Add New Policy Test',
            'new_item_name' => 'New Policy Test Name',
            'separate_items_with_commas' => 'Separate Policy Tests with commas',
            'add_or_remove_items' => 'Add or remove Policy Tests',
            'choose_from_most_used' => 'Choose from the most used Policy Tests',
        ],
        'capabilities' => [
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
        ],
        'public' => true,
    ]);

    register_taxonomy('policypriority', ['policy-step'], [
        'update_count_callback' => '_update_post_term_count',
        'hierarchical' => '1',
        'rewrite' => [
            'slug' => 'policypriority',
            'with_front' => true,
            'hierarchical' => false,
        ],
        'query_var' => 'policypriority',
        'show_ui' => true,
        'show_tagcloud' => false,
        'show_in_nav_menus' => true,
        'labels' => [
            'name' => 'Policy Priorities',
            'singular_name' => 'Policy Priority',
            'search_items' => 'Search Policy Priorities',
            'popular_items' => 'Popular Policy Priorities',
            'all_items' => 'All Policy Priorities',
            'parent_item' => 'Parent Policy Priority',
            'parent_item_colon' => 'Parent Policy Priority:',
            'edit_item' => 'Edit Policy Priority',
            'update_item' => 'Update Policy Priority',
            'add_new_item' => 'Add New Policy Priority',
            'new_item_name' => 'New Policy Priority Name',
            'separate_items_with_commas' => 'Separate Policy Priorities with commas',
            'add_or_remove_items' => 'Add or remove Policy Priorities',
            'choose_from_most_used' => 'Choose from the most used Policy Priorities',
        ],
        'capabilities' => [
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
        ],
        'public' => true,
    ]);

});