<?php

/*
Plugin Name: Department of Health Subscription plugin
Description: Add front-end widgets for subscriptions
Author: WTG: Daniel Chalk
Version: 0.1
Author URI: http://wtg.co.uk
*/

require __DIR__ . '/class-dh-subscriptions.php';

function dhSubscribeCategory()
{
    header('content-type: application/json');

    if (!array_key_exists('cat_ID', $_POST)) {
        exit(json_encode(array('error' => 'cat_ID expected')));
    }

    $dhSub = new dh_subscriptions();
    $catID = $_POST['cat_ID'];
    $enabled = (array_key_exists('enabled', $_POST) && $_POST['enabled'] == 1);

    try {
        exit(json_encode($dhSub->set_user_feed_category($catID, $enabled)));
    } catch (Exception $e) {
        exit(json_encode(array('error' => $e->getMessage())));
    }
}

add_action('admin_post_dh_subscribe_category', 'dhSubscribeCategory');

function dh_category_subscribe_checkbox($cat_ID)
{
    $newsletterCategories = dh_subscriptions::getInstance()->get_newsletter_categories();
    $userCategories = dh_subscriptions::getInstance()->get_user_feed_categories();

    if (array_key_exists($cat_ID, $newsletterCategories) && !property_exists($newsletterCategories[$cat_ID], 'disabled')) { ?>
        <input id="subscribe_to_<?php $cat_ID ?>"
               value="1"
               data-value="<?php echo $cat_ID ?>"
               type="checkbox"
               class="subscribe_checkbox"<?php if (array_search($cat_ID, $userCategories) !== false) {
            echo " checked";
        } ?>/>
        <?php
        wp_enqueue_script(__FUNCTION__, plugin_dir_url(__FILE__) . '/subscribe.js');
    }
}