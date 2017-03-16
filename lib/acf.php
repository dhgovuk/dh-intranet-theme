<?php

// This bit of code seems to be necessary when adding other options sub-pages
if (function_exists('acf_add_options_sub_page') && current_user_can('edit_users')) {
    acf_add_options_sub_page('Homepage');
    acf_add_options_sub_page('Error page');
    acf_add_options_sub_page('Policy Kit');
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
            'hide_on_screen' => array(),
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
            'hide_on_screen' => array(),
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
            'hide_on_screen' => array(),
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // Fields for page
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // directorate fields
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

    // Page owner details.
    register_field_group(array(
        'id' => 'acf_page-owner-details',
        'title' => 'Page owner details',
        'fields' => array(
            array(
                'key' => 'field_5357c90c2eb86',
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
                'key' => 'field_5357c9592eb87',
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
                'key' => 'field_5357c5a24c33f',
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
                    'order_no' => 1,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'page_template',
                    'operator' => '!=',
                    'value' => 'landing-page.php',
                    'order_no' => 2,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'page_template',
                    'operator' => '!=',
                    'value' => 'template-directorate-page.php',
                    'order_no' => 3,
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

    // Policy kit tabs
    register_field_group(array(
        'id' => 'acf_policy-kit-tabs',
        'title' => 'PolicyKit Tabs',
        'fields' => array(
            array(
                'key' => 'field_536a3398708f8',
                'label' => 'Summary',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_536a32a948951',
                'label' => 'Summary',
                'name' => 'policy-summary',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => '',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
            array(
                'key' => 'field_536a33be705e5',
                'label' => 'Get support',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_536a331ad2f79',
                'label' => 'Get support',
                'name' => 'policy-get-support',
                'type' => 'wysiwyg',
                'instructions' => '',
                'default_value' => '',
                'toolbar' => 'basic',
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
                'instructions' => '',
                'default_value' => '',
                'toolbar' => 'basic',
                'media_upload' => 'yes',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'policy-kit',
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

    // Policy steps owner
    register_field_group(array(
        'id' => 'acf_policy-kit-owner',
        'title' => 'PolicyKit Owner',
        'fields' => array(
            array(
                'key' => 'field_5357c80d2eb87',
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
                'key' => 'field_5357d8592eb88',
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
                'key' => 'field_5357d9a24c33e',
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
                    'value' => 'policy-kit',
                    'order_no' => 0,
                    'group_no' => 1,
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

    // Policy Link
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // Newsletter options for 'always', 'never' and 'user'
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // add short title option for right hand navigation
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // Policy Stages Meta
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
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));

    // Error 404
    register_field_group(array(
        'id' => 'acf_404-error-page',
        'title' => '404 error page',
        'fields' => array(
            array(
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
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-error-page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));


    // Homepage
    register_field_group(array(
        'id' => 'acf_homepage',
        'title' => 'Homepage',
        'fields' => array(
            array(
                'key' => 'field_56b9d3acd42a6',
                'label' => 'Emergency Message',
                'name' => 'emergency_message',
                'type' => 'wysiwyg',
                'instructions' => 'Used to show and emergency on the homepage above the campaign box. Only shows when content is added. ',
                'default_value' => '',
                'toolbar' => 'basic',
                'media_upload' => 'no',
            ),
            array(
                'key' => 'field_56bb08ce35b6a',
                'label' => 'Campaign Tabs',
                'name' => 'campaign_tabs',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
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
                    array(
                        'key' => 'field_56bb090e35b6c',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'column_width' => '',
                        'save_format' => 'url',
                        'preview_size' => 'large',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_56bb092835b6d',
                        'label' => 'Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'column_width' => '',
                        'default_value' => '',
                        'toolbar' => 'full',
                        'media_upload' => 'no',
                    ),
                    array(
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
                    array(
                        'key' => 'field_5714adf2d48e5',
                        'label' => 'Show "New" banner',
                        'name' => 'show_new_banner',
                        'type' => 'radio',
                        'instructions' => '',
                        'choices' => array(
                            'no' => 'No',
                            'yes' => 'Yes',
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => '',
                        'layout' => 'vertical',
                    ),
                    array(
                        'key' => 'field_5714f4643f1ed',
                        'label' => 'Expiry date of the "New" banner',
                        'name' => 'expiry_date',
                        'type' => 'date_picker',
                        'conditional_logic' => array(
                            'status' => 1,
                            'rules' => array(
                                array(
                                    'field' => 'field_5714adf2d48e5',
                                    'operator' => '==',
                                    'value' => 'yes',
                                ),
                            ),
                            'allorany' => 'all',
                        ),
                        'date_format' => 'yymmdd',
                        'display_format' => 'dd/mm/yy',
                        'first_day' => 1,
                    ),
                ),
                'row_min' => 1,
                'row_limit' => 3,
                'layout' => 'row',
                'button_label' => 'Add Tab',
            ),
            array(
                'key' => 'field_56b9cf25a4841',
                'label' => 'Service Links',
                'name' => 'service_links',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
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
                    array(
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
                    array(
                        'key' => 'field_56d45a63c88b0',
                        'label' => 'Opens in new Window',
                        'name' => 'opens_in_new_window',
                        'type' => 'radio',
                        'choices' => array(
                            'No' => 'No',
                            'Yes' => 'Yes',
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'No',
                        'layout' => 'vertical',
                    ),
                    array(
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
                'button_label' => 'Add Link',
            ),
            array(
                'key' => 'field_56bb09a2fc659',
                'label' => 'Popular Pages',
                'name' => 'popular_pages',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
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
                    array(
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
                'button_label' => 'Add Link',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-homepage',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));


    // Policy kit page
    register_field_group(array(
        'key' => 'group_582d8f5d43c21',
        'title' => 'Policy kit page',
        'fields' => array(
            array(
                'key' => 'field_582d8f60af2b2',
                'label' => 'Content',
                'name' => 'policy-kit-content',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'The one-stop shop for professional policy makers at DH. Everything you need to successfully apply the six DH Policy Tests throughout your policy projects.',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-policy-kit',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));
}

function current_user_is_admin()
{
    return current_user_can('edit_users');
}
