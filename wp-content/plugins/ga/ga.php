<?php

/*
Plugin Name: GA plugin
Description: Adds GA to your site (requires ACF Options Page or ACFv5)
Author: dxw
Author URI: https://www.dxw.com/
*/

// Add the subpage

if (function_exists('acf_add_options_sub_page')) {
    acf_add_options_sub_page([
        'title' => 'GA',
        'menu' => 'GA',
        'parent' => 'options-general.php',
        'slug' => 'ga',
        'capability' => 'manage_options'
    ]);
}

// Register the options

if(function_exists("register_field_group")) {
    register_field_group(array (
        'id' => 'acf_ga',
        'title' => 'GA',
        'fields' => array (
            array (
                'key' => 'field_565365280d242',
                'label' => 'GA account',
                'name' => 'ga_account',
                'type' => 'text',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ga',
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

// Display the code

add_action('wp_footer', function () {
    if (!function_exists('get_field')) {
        return;
    }

    $account = (string)get_field('ga_account', 'option');

    if ($account === '') {
        return;
    }
    ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo esc_js($account) ?>', 'auto');
        ga('send', 'pageview');
    </script>
    <?php
});
