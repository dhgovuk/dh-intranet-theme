<?php

/*
Plugin Name: Department of Health slider widget plugin
Plugin URI: http://wordpress.org/plugins/wp-refresh/
Description: Offers javascript and CSS for content sliders with more and less buttons.
Author: WTG: Daniel Chalk
Version: 0.1
Author URI: http://wtg.co.uk
*/

/**
 * queue the script and stylesheet for our widget
 */
function wtg_slider_assets()
{
    $plugin_uri = plugin_dir_url(__FILE__) . '/assets/';
    wp_enqueue_script('jsrender', plugin_dir_url(__FILE__) . '/build/bower_components/jsrender/jsrender.min.js', array('main'));
    wp_enqueue_script('wtg_slider_templates', $plugin_uri . 'wtg-slider-templates.js', array('jsrender'));
    wp_enqueue_script('wtg_slider', $plugin_uri . 'wtg-slider.js', array('wtg_slider_templates'));
    wp_enqueue_style('wtg_slider', $plugin_uri . 'wtg-slider.css');
}

/**
 * Create the javascript needed to initialise the widget
 * @param $element_id
 * @param array $items
 * @param array $options
 */
function wtg_slider($element_id, array $items, array $options = array())
{
    wtg_slider_assets();
    $options['items'] = $items;
    ?>
    <!-- DH Content Slider -->
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("<?php echo $element_id ?>").wtgSlider(<?php echo json_encode($options) ?>);
        });
    </script>
<?php
}

/**
 * Created a unique implementation for categories because they're loaded into the page asynchronously
 * @param int $element_id
 * @param int $category_id
 * @param string[] $options
 */
function wtg_category_slider($element_id, $category_id, $options = array())
{
    wtg_slider_assets();
    $options['source'] = array(
        'url' => '/wp-admin/admin-ajax.php',
        'method' => 'POST',
        'params' => array(
            'action' => 'get-category-posts',
            'catID' => $category_id,
        )
    );
    $options['tpl'] = 'category';
    ?>
    <!-- DH Content Slider -->
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var containerEle = <?php echo json_encode($element_id) ?>,
                options = <?php echo json_encode($options) ?>;
                $(containerEle).wtgSlider(options);
        });
    </script>
<?php
}
