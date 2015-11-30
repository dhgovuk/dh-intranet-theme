<?php

// Add some filters to change the default settings for the logo link and title.
add_filter('login_headerurl', function () { return get_bloginfo('url'); });
add_filter('login_headertitle', function () { return get_bloginfo('name'); });

// set the Admin bar off by default for new users
add_action('user_register', 'set_user_admin_bar_false_by_default', 10, 1);
function set_user_admin_bar_false_by_default($user_id)
{
    update_user_meta($user_id, 'show_admin_bar_front', 'false');
}

//remove the Howdy message in the admin bar
function replace_howdy($wp_admin_bar)
{
    $my_account = $wp_admin_bar->get_node('my-account');
    $newtitle = str_replace('Howdy,', 'Hello, ', $my_account->title);
    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => $newtitle,
    ));
}
add_filter('admin_bar_menu', 'replace_howdy', 25);

/*
* tidy up the admin dashboard
*/
add_action('admin_init', 'rw_remove_dashboard_widgets');
function rw_remove_dashboard_widgets()
{
    remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');  // quick press
}

/*
* Add categorys for pages
*/
register_taxonomy_for_object_type('category', 'page');

/*
* remove the restriction on policy articles parent, so we can make policy article children of regular pages.
*/
add_action('admin_menu', function () { remove_meta_box('pageparentdiv', 'policy-article', 'normal');});
add_action('add_meta_boxes', function () { add_meta_box('policy-article-parent', 'Parent', 'policy_article_attributes_meta_box', 'policy-article', 'side', 'core');});

function policy_article_attributes_meta_box($post)
{
    $post_type_object = get_post_type_object($post->post_type);
    if ($post_type_object->hierarchical) {
        /*
        *  would be cleaner to get these as an array and put them into a select statement manually .
        */
        $pages = wp_dropdown_pages(array('post_type' => 'policy-article',
        'selected' => $post->post_parent,
        'name' => 'parent_id',
        'show_option_none' => __('(no parent)'), 'sort_column' => 'menu_order, post_title',
        'echo' => 0, ));

        $policyArticles = wp_dropdown_pages(array('post_type' => 'page',
        'selected' => $post->post_parent,
        'name' => 'parent_id',
        'show_option_none' => __('(no parent)'), 'sort_column' => 'menu_order, post_title',
        'echo' => 0, ));

        if ($pages) {
            //drop closing select tag
            $pages = substr($pages, 0, -strlen('</select>') - 1);
            $pages .= "\t<option class='level-0' value=''>----------PAGES----------</option>\n\t";
            //drop the opening select statement
            $pos = strpos($policyArticles, '<option');
            $policyArticles = substr($policyArticles, $pos);
            $pages .= $policyArticles;
            echo $pages;
        } elseif ($policyArticles) {
            echo $policyArticles;
        }
    } // end hierarchical check.
}

/*
* Enable the page reordering plugin for the policy steps custom post type.
* @author Bill Erickson
* @link http://www.billerickson.net/code/change-post-types-cms-page-order
*
* @param array $post_types
* @param array $post_types modified
*/
function allow_sort_policy_step($post_types)
{
    $post_types[] = 'policy-step';

    return $post_types;
}
add_filter('cmspo_post_types', 'allow_sort_policy_step');
