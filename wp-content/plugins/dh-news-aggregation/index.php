<?php

/**
 * @package DH_News_Aggregation
 * @version 0.1
 */
/*
Plugin Name: Department of Health News Aggregation Tool
Plugin URI: http://wordpress.org/plugins/wp-refresh/
Description: Displays blog posts grouped by category. Activate and then add the short code [dh_news_aggregation] to a relevant page.
Author: WTG: Rob Waller
Version: 0.1
Author URI: http://wtg.co.uk
*/

/* The front end ajax method */
function dhCategoryPosts() {
	
	/* Initiate the WordPress Database object*/
	global $wpdb;

	$cID    = (int)$_POST['catID'];
	$count  = (int)$_POST['count'];
	$offset = (int)$_POST['offset'];
	$status = 'publish';

	$news_title_length = newsGetTitleLength();

	/* Count the live posts in the category. This may not be the most 'WordPress' way to do this but I have no idea how WordPress does anything... :/ */
	$subcategories = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT tt.term_id FROM $wpdb->term_taxonomy as tt WHERE tt.parent = %d",
			$cID
		)
	);

	$categories = array($cID);

	if (!empty($subcategories)) {

		foreach ($subcategories as $s) {
			$categories[] = $s->term_id;
		}

	}

	$catString = implode(',',$categories);

	$query = $wpdb->prepare(
					"SELECT COUNT(DISTINCT(p.ID)) 
						FROM $wpdb->term_relationships AS tr
						JOIN $wpdb->term_taxonomy AS tt
						ON tr.term_taxonomy_id = tt.term_taxonomy_id
						JOIN $wpdb->posts AS p 
						ON tr.object_id = p.ID
						WHERE p.post_status = %s
						AND p.post_type = 'post'
						AND tt.term_id IN($catString)",
					$status
			);

	$post_count = $wpdb->get_var(
			$query
	);

	/* If there are some posts carry on */
	if ($post_count>0) {

		//$args = array('posts_per_page'=>$count,'category'=>$cID,'offset'=>$offset,'post_status'=>'publish','post_type'=>'post');
		/* Get the posts based on the arguments provided above */
		//$posts = get_posts($args);
		/* foreach post get the relevant thumb and add it to the posts array */
		
		$query = $wpdb->prepare(
					"SELECT p.*
						FROM $wpdb->term_relationships AS tr
						JOIN $wpdb->term_taxonomy AS tt
						ON tr.term_taxonomy_id = tt.term_taxonomy_id
						JOIN $wpdb->posts AS p 
						ON tr.object_id = p.ID
						WHERE p.post_status = '%s'
						AND p.post_type = 'post'
						AND tt.term_id IN($catString)
						GROUP BY p.ID
						ORDER BY p.post_date DESC	 
						LIMIT %d,%d",
					$status,$offset,$count
		);

		$posts = $wpdb->get_results(
			$query
		);

		foreach ($posts as $c => $p) {
			$posts[$c]->post_title = wtg_truncate_string($posts[$c]->post_title, $news_title_length);
			$posts[$c]->image = wp_get_attachment_image_src(get_post_thumbnail_id($p->ID));
		} 

		/* Output the json object for the ajax call */
		echo json_encode(array('category'=>$cID,
								'category_name'=>get_the_category_by_ID($cID),
								'offset'=>$offset+4,
								'posts'=>$posts,
								'count'=>count($posts),
								'post_count'=>$post_count,
								'message'=>'Posts returned successfully'));
	
	}
	else {
		echo json_encode(array('category'=>$cID,
								'category_name'=>get_the_category_by_ID($cID),
								'offset'=>0,
								'posts'=>NULL,
								'count'=>0,
								'post_count'=>0,
								'message'=>'No posts found',
								'queryError'=>print_r($wpdb->last_error,true)));
	}

	/* Tell WordPress the request ran ok... */
	die(1);
}

function updateCategories($opt,$name) {

	if (isset($_POST['cats']) && !empty($_POST['cats'])) {

		// We are expecting a single dimension array.
		if(is_array($_POST['cats'])) {
			// Loop through the array.
			foreach($_POST['cats'] as $key => $category) {
				// Individual category must be passed as
				// a string from the submit form.
				if(is_string($category)) {
					// Category cannot be 0.
					if((int)$category) {
						$_POST['cats'][$key] = (int)$category;
					}
					else {
						unset($_POST['cats'][$key]);
					}
				}
				// Check if it is an integer
				// before unsetting.
				elseif( ! is_int($category)) {
					unset($_POST['cats'][$key]);
				}
				// If it is an int, it should not be 0.
				elseif( ! $category) {
					unset($_POST['cats'][$key]);
				}
			}
		}
		// No single dimensional array - unset.
		else {
			unset($_POST['cats']);
		}

		/* Check to see if options data exists */
		if (empty($opt)) {

			/*Add first version of option data*/
			add_option($name,json_encode($_POST['cats']),false,'yes');
		}
		else
		{
			/*Update option data*/

			update_option($name,json_encode($_POST['cats']));
		}

		$message = 'Category options updated successfully';

	}
	else {

		if (empty($opt)) {
			$message = 'Failed to update messages!!';
		}
		else
		{
			$cats = array();
			update_option($name,json_encode($cats));
		}
	}

}

/* Method is triggered when user clicks on admin menu item -- see run() method. */

function display() { 

	/* name of the option data that the plugin pulls from the WordPress options table */
	$name = 'dh_news_aggregation';

	/* Get current option data */
	$opt = get_option($name,false);

	if (isset($_POST['submit'])) {

		newsSaveTitleLength((int)$_POST['news_title_length']);

		/* If the add categories form has been submitted update the option data */
		updateCategories($opt,$name);

	}
	else if (isset($_GET['pos'])) {

		/* If on of the category order links has been clicked update the option data */
		reorderCategories($opt,$name);

	}

	/* Get new/updated option data */
	$opt = get_option($name,false);

	if (!empty($opt)) {
		/* The opt data is stored as a JSON string so decode it */
		$opt = json_decode($opt, TRUE);
	}

	/* Call in admin form */
	require('views/form.php');
}

function wtg_get_news_aggregator_categories() {
	$name = 'dh_news_aggregation';

	/* Get new/updated option data */
	$opt = get_option($name,false);

	if (!empty($opt)) {
		/* The opt data is stored as a JSON string so decode it */
		$opt = json_decode($opt);
	}

	return $opt;
}

function run() {
	
	/* 
		Adds a menu item to the main admin menu 
		When the user clicks on the menu item the display() method is triggered.
	*/

	add_menu_page('new aggregation','News Aggregation','edit_posts','display_management','display','',23);
}

/* Pulls in the front end content */
function showCategories() {
	$name = 'dh_news_aggregation';
        return 'ERROR';
	// require('views/categories.php');
}

function showCategory($category_id) {
    require('views/category.php');
}

function newsGetTitleLength() {
	$option_name = 'dh_news_aggregation_title_length';
	$news_title_length = get_option($option_name, FALSE);

	// Default to 50 characters if the option has not been saved yet.
	if( ! $news_title_length) {
		$news_title_length = 50;
	}

	return $news_title_length;
}

function newsSaveTitleLength($news_title_length = 50) {
	$option_name = 'dh_news_aggregation_title_length';
	update_option($option_name, $news_title_length);
}

/* Initiates the admin functionality */
add_action('admin_menu','run');

/* Action for the Ajax call made by the js/cats.js file */
add_action('wp_ajax_nopriv_get-category-posts','dhCategoryPosts');
add_action('wp_ajax_get-category-posts','dhCategoryPosts');

/* The action for the short code [dh_news_aggregation] that you add to the WordPress page you want this functionality to appear on. */
add_shortcode('dh_news_aggregation','showCategories');
