<?php

// This bit of code seems to be necessary when adding other options sub-pages
if (function_exists('acf_add_options_sub_page')) {
    acf_add_options_sub_page([
        'title' => 'Options',
        'menu' => 'Options',
        'slug' => 'acf-options',
        'capability' => 'manage_options'
    ]);
}

if (function_exists('register_field_group')) {

    // To-do items
    register_field_group(array(
        'id' => 'acf_todo-items',
        'title' => 'Todo items',
        'fields' => array(
            array(
                'key' => 'field_52af298dbdfed',
                'label' => 'Due date',
                'name' => 'due_date',
                'type' => 'date_picker',
                'date_format' => 'yy-mm-dd',
                'display_format' => 'dd/mm/yy',
                'first_day' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'todo-item',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    // Local news
    register_field_group(array(
        'id' => 'acf_local-news',
        'title' => 'Local news',
        'fields' => array(
            array(
                'key' => 'field_52b0bb27c5089',
                'label' => 'Important',
                'name' => 'important',
                'type' => 'checkbox',
                'choices' => array(
                    'important' => 'This item should appear at the top of the list and be highlighted',
                ),
                'default_value' => '',
                'layout' => 'vertical',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'local-news',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    // News locale
    register_field_group(array(
        'id' => 'acf_news-locale',
        'title' => 'News locale',
        'fields' => array(
            array(
                'key' => 'field_52b0be147ad1f',
                'label' => 'Default',
                'name' => 'default',
                'type' => 'checkbox',
                'choices' => array(
                    'default' => 'This should be the default locale for users who haven\'t set their locale',
                ),
                'default_value' => '',
                'layout' => 'vertical',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'ef_taxonomy',
                    'operator' => '==',
                    'value' => 'news-locale',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    // User options
    register_field_group(array(
        'id' => 'acf_dh-intranet-options',
        'title' => 'DH Intranet options',
        'fields' => array(
            array(
                'key' => 'field_52b0bfb38814e',
                'label' => 'News locale',
                'name' => 'news_locale',
                'type' => 'taxonomy',
                'taxonomy' => 'news-locale',
                'field_type' => 'select',
                'allow_null' => 0,
                'load_save_terms' => 0,
                'return_format' => 'id',
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'ef_user',
                    'operator' => '==',
                    'value' => 'all',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    // Fields for page and top tasks
    register_field_group(array(
        'id' => 'acf_page',
        'title' => 'Page',
        'fields' => array(
            array(
                'key' => 'field_52b2d503d9ff9',
                'label' => 'Reviewed date',
                'name' => 'reviewed_date',
                'type' => 'date_picker',
                'date_format' => 'yymmdd',
                'display_format' => 'dd/mm/yy',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_52b2d52dd9ffa',
                'label' => 'Related links',
                'name' => 'related_links',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_52b2d76189ba4',
                        'label' => 'Link name',
                        'name' => 'link_name',
                        'type' => 'text',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_52b2d77589ba5',
                        'label' => 'Link',
                        'name' => 'link',
                        'type' => 'text',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                ),
                'row_min' => '',
                'row_limit' => '',
                'layout' => 'default',
                'button_label' => 'Add Row',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'page_template',
                    'operator' => '!=',
                    'value' => 'landing-page.php',
                    'order_no' => 1,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    register_field_group(array(
        'id' => 'acf_top-tasks-2',
        'title' => 'Top tasks',
        'fields' => array(
            array(
                'key' => 'field_52b036eb7226b',
                'label' => 'Task 1',
                'name' => 'task_1',
                'type' => 'post_object',
                'required' => 1,
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_52b036fd7226c',
                'label' => 'Task 2',
                'name' => 'task_2',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_52b0370c7226d',
                'label' => 'Task 3',
                'name' => 'task_3',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_52b037187226e',
                'label' => 'Task 4',
                'name' => 'task_4',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_52b037237226f',
                'label' => 'Task 5',
                'name' => 'task_5',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_52b0375072270',
                'label' => 'Task 6',
                'name' => 'task_6',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'page',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //top tasks and page links
    register_field_group(array(
        'id' => 'acf_page',
        'title' => 'Page',
        'fields' => array(
            array(
                'key' => 'field_52b2d52dd9ffa',
                'label' => 'Related links',
                'name' => 'related_links',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_52b2d76189ba4',
                        'label' => 'Link name',
                        'name' => 'link_name',
                        'type' => 'text',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_52b2d77589ba5',
                        'label' => 'Link',
                        'name' => 'link',
                        'type' => 'text',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                ),
                'row_min' => '',
                'row_limit' => '',
                'layout' => 'table',
                'button_label' => 'Add Row',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //directorate fields
    register_field_group(array(
        'id' => 'acf_directorate-fields',
        'title' => 'Directorate Fields',
        'fields' => array(
            array(
                'key' => 'field_5357958bb3eba',
                'label' => 'Category',
                'name' => 'directorate-category',
                'type' => 'taxonomy',
                'instructions' => 'Select the category to display posts from',
                'taxonomy' => 'category',
                'field_type' => 'select',
                'allow_null' => 1,
                'load_save_terms' => 0,
                'return_format' => 'id',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_53578b8a3eb93',
                'label' => 'Featured Post',
                'name' => 'directorate-featured-post',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'all',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'allow_null' => 1,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_5356710d34b41',
                'label' => 'Director',
                'name' => 'director',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'directorate',
                ),
                'field_type' => 'select',
                'allow_null' => 0,
            ),
            array(
                'key' => 'field_5356716434b42',
                'label' => 'Key Personnel',
                'name' => 'key-personnel',
                'type' => 'post_object',
                'post_type' => array(
                    0 => 'directorate',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'instructions' => 'Select all Key Personnel from the list of users. Hold Ctrl to select multiple items.',
                'allow_null' => 1,
                'multiple' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'template-directorate-page.php',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    //Page owner details.
    register_field_group(array(
        'id' => 'acf_page-owner-details',
        'title' => 'Page owner details',
        'fields' => array(
            array(
                'key' => 'field_5357c80c2eb86',
                'label' => 'Page Owner',
                'name' => 'page_owner',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5357c8592eb87',
                'label' => 'Page Owner Email',
                'name' => 'page_owner_email',
                'type' => 'email',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_5357c9a24c33f',
                'label' => 'Page Owner Team',
                'name' => 'page_owner_team',
                'type' => 'text',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'page_template',
                    'operator' => '!=',
                    'value' => 'landing-page.php',
                    'order_no' => 1,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'page_template',
                    'operator' => '!=',
                    'value' => 'template-directorate-page.php',
                    'order_no' => 2,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //Policy Steps
    register_field_group(array(
        'id' => 'acf_policy-step-tabs',
        'title' => 'Policy Step Tabs',
        'fields' => array(
            array(
                'key' => 'field_536a3398708e4',
                'label' => 'Details',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_536a32a948907',
                'label' => 'Details',
                'name' => 'policy-details',
                'type' => 'wysiwyg',
                'instructions' => 'Please enter the content to be displayed in the details tab for this step in the Policy Toolkit Dashboard.<br>This is also the text that will be displayed if this Policy step is included on a Policy page.',
                'required' => 1,
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
            array(
                'key' => 'field_536a33be708e5',
                'label' => 'Get support',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_536a331ad3f79',
                'label' => 'Get support',
                'name' => 'policy-get-support-text',
                'type' => 'wysiwyg',
                'instructions' => 'Please enter the details of a relevant support team.',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'no',
            ),
            array(
                'key' => 'field_536a33e6708e7',
                'label' => 'Case study',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_536a2cae53e3d',
                'label' => 'Case study',
                'name' => 'policy-casestudy',
                'type' => 'wysiwyg',
                'instructions' => 'Please enter a case study that relates to this policy step.',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'policy-step',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //Policy Link
    register_field_group(array(
        'id' => 'acf_policy-test-explanation',
        'title' => 'Policy Test Explanation ',
        'fields' => array(
            array(
                'key' => 'field_5374bcd63d358',
                'label' => 'Link to Page',
                'name' => 'policy_test_link_to_page',
                'type' => 'page_link',
                'post_type' => array(
                    0 => 'all',
                ),
                'allow_null' => 0,
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'ef_taxonomy',
                    'operator' => '==',
                    'value' => 'policytests',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //Newsletter options for 'always', 'never' and 'user'
    register_field_group(array(
        'id' => 'acf_newsletter-options',
        'title' => 'Newsletter options',
        'fields' => array(
            array(
                'key' => 'field_53d12270d555c',
                'label' => 'Include in newsletter',
                'name' => 'include_in_newsletter',
                'type' => 'radio',
                'choices' => array(
                    'always' => 'Always include in Newsletter / My subscriptions',
                    'never' => 'Never    include in Newsletter / My subscriptions',
                    'user' => 'Allow user to choose',
                ),
                'other_choice' => 0,
                'save_other_choice' => 0,
                'default_value' => 'user',
                'layout' => 'vertical',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'ef_taxonomy',
                    'operator' => '==',
                    'value' => 'category',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    // Include other page/policy steps selector for pages in the policy-page category
    //get policy-article cat id
    $catSlug = get_category_by_slug('policy-article');
    $catSlug = isset($catSlug->term_id) ? $catSlug->term_id : null;

    register_field_group(array(
        'id' => 'acf_include-other-pages-or-steps-in-this-policy-article-2',
        'title' => 'Include other Pages or Steps in this Policy Article',
        'fields' => array(
            array(
                'key' => 'field_53dbabfb9aed0',
                'label' => 'Add sections',
                'name' => 'policy_article_includes2',
                'type' => 'relationship',
                'instructions' => 'Content from other Pages or Policy steps can be included in this Policy Article page.
                Click on a title in the left hand column to add it to the page.',
                'return_format' => 'object',
                'post_type' => array(
                    0 => 'page',
                    1 => 'policy-step',
                ),
                'taxonomy' => array(
                    0 => 'all',
                ),
                'filters' => array(
                    0 => 'search',
                    1 => 'post_type',
                ),
                'result_elements' => array(
                    0 => 'post_type',
                    1 => 'post_title',
                ),
                'max' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_category',
                    'operator' => '==',
                    'value' => $catSlug,
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    //add short title option for right hand navigation
    register_field_group(array(
        'id' => 'acf_short-title',
        'title' => 'Short title',
        'fields' => array(
            array(
                'key' => 'field_53d79b1ac6ea8',
                'label' => 'Shorter navigation title',
                'name' => 'shorter_navigation_title',
                'type' => 'text',
                'instructions' => 'for long titles (approx > 32 characters) provide a shorter title for use in the navigation.',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => 32,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'policy-article',
                    'order_no' => 0,
                    'group_no' => 1,
                ),
            ),
        ),
        'options' => array(
            'position' => 'side',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    register_field_group(array(
        'id' => 'acf_policy-stages-meta',
        'title' => 'Policy Stages Meta',
        'fields' => array(
            array(
                'key' => 'field_53f4c48029466',
                'label' => 'order',
                'name' => 'policy-stages-order',
                'type' => 'number',
                'default_value' => 0,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'ef_taxonomy',
                    'operator' => '==',
                    'value' => 'policystage',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array(
            ),
        ),
        'menu_order' => 0,
    ));

    register_field_group(array (
        'id' => 'acf_404-error-page',
        'title' => '404 error page',
        'fields' => array (
            array (
                'key' => 'field_5633b6a23f68b',
                'label' => 'Content',
                'name' => '404_page_content',
                'type' => 'wysiwyg',
                'default_value' => '<h1>Sorry, but the page you were trying to view does not exist</h1>

                <p>It looks like this was the result of either:</p>
                <ul>
                    <li>a mistyped address</li>
                    <li>an out-of-date link</li>
                </ul>
                ',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array (
            ),
        ),
        'menu_order' => 0,
    ));
    register_field_group(array (
        'id' => 'acf_homepage',
        'title' => 'Homepage',
        'fields' => array (
          array (
            'key' => 'field_56b9d3acd42a6',
            'label' => 'Emergency Message',
            'name' => 'emergency_message',
            'type' => 'wysiwyg',
            'instructions' => 'Used to show and emergency on the homepage above the campaign box. Only shows when content is added. ',
            'default_value' => '',
            'toolbar' => 'basic',
            'media_upload' => 'no',
          ),
          array (
            'key' => 'field_56bb08ce35b6a',
            'label' => 'Campaign Tabs',
            'name' => 'campaign_tabs',
            'type' => 'repeater',
            'sub_fields' => array (
              array (
                'key' => 'field_56bb090435b6b',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
              array (
                'key' => 'field_56bb090e35b6c',
                'label' => 'Image',
                'name' => 'image',
                'type' => 'image',
                'column_width' => '',
                'save_format' => 'url',
                'preview_size' => 'large',
                'library' => 'all',
              ),
              array (
                'key' => 'field_56bb092835b6d',
                'label' => 'Content',
                'name' => 'content',
                'type' => 'wysiwyg',
                'column_width' => '',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'no',
              ),
              array (
                'key' => 'field_56bb093935b6e',
                'label' => 'Call to action',
                'name' => 'call_to_action',
                'type' => 'text',
                'instructions' => 'Please enter the full url E.G. http://gov.uk',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
            ),
            'row_min' => 1,
            'row_limit' => 3,
            'layout' => 'row',
            'button_label' => 'Add Row',
          ),
          array (
            'key' => 'field_56b9cf25a4841',
            'label' => 'Service Links',
            'name' => 'service_links',
            'type' => 'repeater',
            'sub_fields' => array (
              array (
                'key' => 'field_56b9cf40a4842',
                'label' => 'Text',
                'name' => 'text',
                'type' => 'text',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
              ),
              array (
                'key' => 'field_56b9cf4ea4843',
                'label' => 'URL',
                'name' => 'url',
                'type' => 'text',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
              array (
                'key' => 'field_56b9cf64a4844',
                'label' => 'Icon',
                'name' => 'icon',
                'type' => 'text',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => 'icon-',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
            ),
            'row_min' => '',
            'row_limit' => '',
            'layout' => 'table',
            'button_label' => 'Add Row',
          ),
          array (
            'key' => 'field_56bb09a2fc659',
            'label' => 'Popular Pages',
            'name' => 'popular_pages',
            'type' => 'repeater',
            'sub_fields' => array (
              array (
                'key' => 'field_56bb09b7fc65a',
                'label' => 'Text',
                'name' => 'text',
                'type' => 'text',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
              array (
                'key' => 'field_56bb09c3fc65b',
                'label' => 'URL',
                'name' => 'url',
                'type' => 'text',
                'instructions' => 'Please enter the full url E.G. http://gov.uk',
                'column_width' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'none',
                'maxlength' => '',
              ),
            ),
            'row_min' => '',
            'row_limit' => '',
            'layout' => 'table',
            'button_label' => 'Add Row',
          ),
        ),
        'location' => array (
          array (
            array (
              'param' => 'options_page',
              'operator' => '==',
              'value' => 'acf-options-homepage',
              'order_no' => 0,
              'group_no' => 0,
            ),
          ),
        ),
        'options' => array (
          'position' => 'normal',
          'layout' => 'no_box',
          'hide_on_screen' => array (
          ),
        ),
        'menu_order' => 0,
      ));
}
