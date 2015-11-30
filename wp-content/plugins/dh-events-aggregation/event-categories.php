<?php
/**
 * Plugin Name: Department of Health Events Aggregation Tool
 * Description: Displays events grouped by category. Activate and then add the shortcode [dh_events_aggregation] to a relevant page.
 * Version: 0.1
 * Author: WTG: Martynas Kveksas
 * Author URI: http://www.webtechnologygroup.co.uk/
 */

defined('ABSPATH') or die('Error 554');

require_once(__DIR__.'/dh-content-slider-plugin/index.php');

define('EVENT_CATEGORIES_VERSION', '1.0');
define('EVENT_CATEGORIES_ROOT', dirname(__FILE__));
define('EVENT_CATEGORIES_FILE_PATH', WP_PLUGIN_URL . '/' . basename(EVENT_CATEGORIES_ROOT));

add_action('admin_menu', 'event_categories_menu');

// The action for the shortcode [dh_events_aggregation] to be added
// in the WordPress page for this functionality to appear on.
add_shortcode('dh_events_aggregation', 'showEventCategories');

// Pulls in the frontend content.
function showEventCategories()
{
        ob_start();
	require('views/categories.php');
        return ob_get_clean();
}

function event_categories_menu()
{
	wp_enqueue_script( 'event-categories-helper', plugin_dir_url(__FILE__) . '/js/events-aggregator-admin.js', array(), uniqid(), true );
	add_submenu_page('edit.php?post_type=event', 'Events Aggregation', 'Events Aggregation', 'manage_options', 'event-categories', 'show_event_categories_backend');
}

function show_event_categories_backend()
{
	if(isset($_POST['save_changes']) && isset($_POST['categories'])) {
		unset($_POST['save_changes']);
		Event_Categories::save_categories($_POST['categories']);
		Event_Categories::save_title_length((int)$_POST['event_title_length']);
		Event_Categories::save_description_length((int)$_POST['event_description_length']);
	}

	// Include the template.
	include(EVENT_CATEGORIES_ROOT . '/views/admin.php');
}

class Event_Categories {

	private static $option_name_categories = 'dh_event_categories';
	private static $option_name_title_length = 'dh_event_categories_title_length';
	private static $option_name_description_length = 'dh_event_categories_description_length';

	private static function get_term_taxonomies()
	{
		global $wpdb;

		$table_term_taxonomy = $wpdb->prefix . 'term_taxonomy';

		$taxonomies = $wpdb->get_results("
						SELECT *
						FROM $table_term_taxonomy
						WHERE taxonomy = 'eventcat'
					  ");

		return $taxonomies;
	}

	public static function get_all_categories()
	{
		global $wpdb;

		$table_terms = $wpdb->prefix . 'terms';
		$table_term_taxonomy = $wpdb->prefix . 'term_taxonomy';

		$taxonomy_term_list = array();

		$taxonomies = self::get_term_taxonomies();

		foreach($taxonomies as $taxonomy) {
			$taxonomy_term_list[] = '\'' . $taxonomy->term_id . '\'';
		}

		$taxonomy_term_list = implode(', ', $taxonomy_term_list);

		$categories = $wpdb->get_results("
								SELECT *
								FROM $table_terms
								WHERE term_id IN ($taxonomy_term_list)
					  ");

		foreach($taxonomies as $tax_key => $taxonomy) {
			foreach($categories as $cat_key => $category) {
				if($taxonomy->term_id == $category->term_id) {
					$categories[$cat_key]->term_taxonomy_id = $taxonomy->term_taxonomy_id;
				}
			}
		}

		$categories_list = array();

		foreach($categories as $category) {
			$categories_list[$category->term_taxonomy_id] = $category->name;
		}

		$categories_list['uncategorized'] = 'Uncategorized';

		return $categories_list;
	}

	/**
	 * Get category states
	 * Returns categories and their states
	 * as set in the options table in the DB.
	 *
	 * @return array of categories
	 */
	public static function get_category_states()
	{
		$category_states = get_option(self::$option_name_categories, FALSE);

		$category_states = json_decode($category_states, TRUE);

		$all_categories = self::get_all_categories();

		// Add categories that might have been newly added.
		foreach($all_categories as $cat_id => $name) {
			if( ! isset($category_states[$cat_id]))	{
				$category_states[$cat_id] = 'hide';
			}
		}

		if( ! isset($category_states['uncategorized'])) {
			$category_states['uncategorized'] = 'hide';
		}


		return $category_states;
	}

	public static function get_event_title_length()
	{
		$event_title_length = get_option(self::$option_name_title_length, FALSE);

		// Default to 50 characters if the option has not been saved yet.
		if( ! $event_title_length) {
			$event_title_length = 50;
		}

		return $event_title_length;
	}

	public static function get_event_description_length()
	{
		$event_description_length = get_option(self::$option_name_description_length, FALSE);

		// Default to 100 characters if the option has not been saved yet.
		if( ! $event_description_length) {
			$event_description_length = 100;
		}

		return $event_description_length;
	}

	// Get event categories and their events (posts).
	// This method is used for the front-end.
	public static function get_categories_and_events()
	{
		global $wpdb;

		$taxonomy_id_list = array();

		$table_term_relationships = $wpdb->prefix . 'term_relationships';

		$taxonomies = self::get_term_taxonomies();

		foreach($taxonomies as $taxonomy) {
			$taxonomy_id_list[]   = '\'' . $taxonomy->term_taxonomy_id . '\'';
		}

		$taxonomy_id_list   = implode(', ', $taxonomy_id_list);

		$term_relationships = $wpdb->get_results("
								SELECT *
								FROM $table_term_relationships
								WHERE term_taxonomy_id IN ($taxonomy_id_list)
							  ");

		$categories_and_events = array();

		foreach($term_relationships as $relationship) {
			$categories_and_events[$relationship->term_taxonomy_id][] = $relationship->object_id;
		}

		$all_posts = self::get_all_posts();

		if( ! empty($all_posts)) {
			$uncategorized_posts = $all_posts;

			foreach($categories_and_events as $category) {
				foreach($uncategorized_posts as $key => $post_id) {
					if(in_array($post_id, $category)) {
						unset($uncategorized_posts[$key]);
					}
				}
			}
		}

		if( ! empty($uncategorized_posts)) {
			$categories_and_events['uncategorized'] = $uncategorized_posts;
		}

		return $categories_and_events;
	}

	public static function save_categories($form_data_categories)
	{
		// Get all categories.
		// Check against submitted form ids.
		// This is to prevent any possible injection attack,
		// even though it's in the admin panel.
		$all_categories  = self::get_all_categories();
		$category_states = array();

		foreach($form_data_categories as $key => $status) {
			$key = str_ireplace('category_', '', $key);

			if(array_key_exists($key, $all_categories)) {
				$category_states[$key] = $status;
			}
			elseif($key == 'uncategorized')	{
				$category_states[$key] = $status;
			}
		}

		$category_states = json_encode($category_states);

		update_option(self::$option_name_categories, $category_states);
	}

	public static function save_title_length($event_title_length = 50)
	{
		update_option(self::$option_name_title_length, $event_title_length);
	}

	public static function save_description_length($event_description_length = 100)
	{
		update_option(self::$option_name_description_length, $event_description_length);
	}

	public static function get_all_posts()
	{
		global $wpdb;

		$table_postmeta = $wpdb->postmeta;

		// Get post IDs which have associated events.
		$result_array = $wpdb->get_results("
			SELECT post_id
			FROM $table_postmeta
			WHERE meta_key = 'event_id'
		");

		$post_ids = array();

		if( ! empty($result_array)) {
			foreach($result_array as $result) {
				$post_ids[] = $result->post_id;
			}
		}

		return $post_ids;
	}
}
