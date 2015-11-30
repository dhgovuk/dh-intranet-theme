<?php
/*
Plugin Name: WTG Like Post
Plugin URI: http://www.wtg.co.uk
Description: Direct lift of "Wtg Like Post" and then altered to fit our needs.  All credit to the original author http://webtechideas.com
Version: 0.1
Author: Adam Lewis <adam.lewis@wtg.co.uk>
Author URI: http://wtg.co.uk
*/


global $wtg_like_post_db_version;
$wtg_like_post_db_version = "0.1";

add_action('init', 'WtgLoadPluginTextdomain');
add_action('admin_init', 'WtgLikePostPluginUpdateMessage');

/**
 * Load the language files for this plugin
 * @param void
 * @return void
 */
function WtgLoadPluginTextdomain() {
	load_plugin_textdomain('wtg-like-post', false, 'wtg-like-post/lang');
}

/**
 * Hook the auto update message
 * @param void
 * @return void
 */
function WtgLikePostPluginUpdateMessage() {
    add_action( 'in_plugin_update_message-' . basename( dirname( __FILE__ ) ) . '/wtg_like_post.php', 'WtgLikePostUpdateNotice' );
}

/**
 * Create the settings link for this plugin
 * @param $links array
 * @param $file string
 * @return $links array
 */
function WtgLikePostPluginLinks($links, $file) {
	static $this_plugin;

	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin) {
		$settings_link = '<a href="' . admin_url('options-general.php?page=WtgLikePostAdminMenu') . '">' . __('Settings', 'wtg-like-post') . '</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

register_activation_hook(__FILE__, 'SetOptionsWtgLikePost');

/**
 * Basic options function for the plugin settings
 * @param no-param
 * @return void
 */
function SetOptionsWtgLikePost() {
	global $wpdb, $wtg_like_post_db_version;

	// Creating the like post table on activating the plugin
	$wtg_like_post_table_name = $wpdb->prefix . "wtg_like_post";
	
        if ($wpdb->get_var($wpdb->prepare("show tables like %s", $wtg_like_post_table_name)) != $wtg_like_post_table_name) {
		$sql = "CREATE TABLE " . $wtg_like_post_table_name . " (
			`id` bigint(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`value` int(11) NOT NULL DEFAULT '0',
			`dislike_value` int(11) NOT NULL DEFAULT '0',
			`date_time` datetime NOT NULL,
			`ip` varchar(40) NOT NULL,
			`user_id` int(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	// Adding options for the like post plugin
	add_option('wtg_like_post_drop_settings_table', '0', '', 'yes');
	add_option('wtg_like_post_voting_period', '0', '', 'yes');
	add_option('wtg_like_post_show_vote_count', '0', '', 'no');
	add_option('wtg_like_post_voting_style', 'style1', '', 'yes');
	add_option('wtg_like_post_alignment', 'left', '', 'yes');
	add_option('wtg_like_post_position', 'bottom', '', 'yes');
	add_option('wtg_like_post_login_required', '0', '', 'yes');
	add_option('wtg_like_post_login_message', __('Please login to vote.', 'wtg-like-post'), '', 'yes');
	add_option('wtg_like_post_thank_message', __('Thanks for your vote.', 'wtg-like-post'), '', 'yes');
	add_option('wtg_like_post_voted_message', __('You have already voted.', 'wtg-like-post'), '', 'yes');
	add_option('wtg_like_post_allowed_posts', '', '', 'yes');
	add_option('wtg_like_post_excluded_posts', '', '', 'yes');
	add_option('wtg_like_post_excluded_categories', '', '', 'yes');
	add_option('wtg_like_post_excluded_sections', '', '', 'yes');
	add_option('wtg_like_post_show_on_pages', '0', '', 'yes');
	add_option('wtg_like_post_show_on_widget', '1', '', 'yes');
	add_option('wtg_like_post_show_symbols', '1', '', 'yes');
	add_option('wtg_like_post_show_dislike', '1', '', 'yes');
	add_option('wtg_like_post_title_text', 'Like/Unlike', '', 'yes');
	add_option('wtg_like_post_db_version', $wtg_like_post_db_version, '', 'yes');
}

/**
 * For dropping the table and removing options
 * @param no-param
 * @return no-return
 */
function UnsetOptionsWtgLikePost() {
	global $wpdb;

	// Check the option whether to drop the table on plugin uninstall or not
	$drop_settings_table = get_option('wtg_like_post_drop_settings_table');
	
	if ($drop_settings_table == 1) {
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wtg_like_post");
	
		// Deleting the added options on plugin uninstall
		delete_option('wtg_like_post_drop_settings_table');
		delete_option('wtg_like_post_voting_period');
		delete_option('wtg_like_post_show_vote_count');
		delete_option('wtg_like_post_voting_style');
		delete_option('wtg_like_post_alignment');
		delete_option('wtg_like_post_position');
		delete_option('wtg_like_post_login_required');
		delete_option('wtg_like_post_login_message');
		delete_option('wtg_like_post_thank_message');
		delete_option('wtg_like_post_voted_message');
		delete_option('wtg_like_post_db_version');
		delete_option('wtg_like_post_allowed_posts');
		delete_option('wtg_like_post_excluded_posts');
		delete_option('wtg_like_post_excluded_categories');
		delete_option('wtg_like_post_excluded_sections');
		delete_option('wtg_like_post_show_on_pages');
		delete_option('wtg_like_post_show_on_widget');
		delete_option('wtg_like_post_show_symbols');
		delete_option('wtg_like_post_show_dislike');
		delete_option('wtg_like_post_title_text');
	}
}

register_uninstall_hook(__FILE__, 'UnsetOptionsWtgLikePost');

function WtgLikePostAdminRegisterSettings() {
	// Registering the settings
	register_setting('wtg_like_post_options', 'wtg_like_post_drop_settings_table');
	register_setting('wtg_like_post_options', 'wtg_like_post_voting_period');
	register_setting('wtg_like_post_options', 'wtg_like_post_show_vote_count');
	register_setting('wtg_like_post_options', 'wtg_like_post_voting_style');
	register_setting('wtg_like_post_options', 'wtg_like_post_alignment');
	register_setting('wtg_like_post_options', 'wtg_like_post_position');
	register_setting('wtg_like_post_options', 'wtg_like_post_login_required');
	register_setting('wtg_like_post_options', 'wtg_like_post_login_message');
	register_setting('wtg_like_post_options', 'wtg_like_post_thank_message');
	register_setting('wtg_like_post_options', 'wtg_like_post_voted_message');
	register_setting('wtg_like_post_options', 'wtg_like_post_allowed_posts');
	register_setting('wtg_like_post_options', 'wtg_like_post_excluded_posts');
	register_setting('wtg_like_post_options', 'wtg_like_post_excluded_categories');
	register_setting('wtg_like_post_options', 'wtg_like_post_excluded_sections');
	register_setting('wtg_like_post_options', 'wtg_like_post_show_on_pages');
	register_setting('wtg_like_post_options', 'wtg_like_post_show_on_widget');
	register_setting('wtg_like_post_options', 'wtg_like_post_db_version');	
	register_setting('wtg_like_post_options', 'wtg_like_post_show_symbols');
	register_setting('wtg_like_post_options', 'wtg_like_post_show_dislike');
	register_setting('wtg_like_post_options', 'wtg_like_post_title_text');	
}

add_action('admin_init', 'WtgLikePostAdminRegisterSettings');

if (is_admin()) {
	// Include the file for loading plugin settings
	require_once('wtg_like_post_admin.php');
} else {
	// Include the file for loading plugin settings for
	require_once('wtg_like_post_site.php');

	// Load the js and css files
	add_action('init', 'WtgLikePostEnqueueScripts');
	add_action('wp_head', 'WtgLikePostAddHeaderLinks');
}

/**
 * Get the actual ip address
 * @param no-param
 * @return string
 */
function WtgGetRealIpAddress() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	} elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	} elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;
}

/**
 * Check whether user has already voted or not
 * @param $post_id integer
 * @param $ip string
 * @return integer
 */
function HasWtgAlreadyVoted($post_id, $ip = null) {
	global $wpdb;
	
	if (null == $ip) {
		$ip = WtgGetRealIpAddress();
	}
	
        $Wtg_has_voted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wtg_like_post WHERE post_id = %s AND ip = %s", $post_id, $ip));
	
	return $Wtg_has_voted;
}

/**
 * Get last voted date for a given post by ip
 * @param $post_id integer
 * @param $ip string
 * @return string
 */
function GetWtgLastVotedDate($post_id, $ip = null) {
	global $wpdb;
	
	if (null == $ip) {
		$ip = WtgGetRealIpAddress();
	}
	
        $Wtg_has_voted = $wpdb->get_var($wpdb->prepare("SELECT date_time FROM {$wpdb->prefix}wtg_like_post WHERE post_id = %s AND ip = %s", $post_id, $ip));

	return $Wtg_has_voted;
}

/**
 * Get next vote date for a given user
 * @param $last_voted_date string
 * @param $voting_period integer
 * @return string
 */
function GetWtgNextVoteDate($last_voted_date, $voting_period) {
	switch($voting_period) {
		case "1":
			$day = 1;
			break;
		case "2":
			$day = 2;
			break;
		case "3":
			$day = 3;
			break;
		case "7":
			$day = 7;
			break;
		case "14":
			$day = 14;
			break;
		case "21":
			$day = 21;
			break;
		case "1m":
			$month = 1;
			break;
		case "2m":
			$month = 2;
			break;
		case "3m":
			$month = 3;
			break;
		case "6m":
			$month = 6;
			break;
		case "1y":
			$year = 1;
		  break;
	}
	
	$last_strtotime = strtotime($last_voted_date);
	$next_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
				date('m', $last_strtotime) + $month, date('d', $last_strtotime) + $day, date('Y', $last_strtotime) + $year);
	
	$next_voting_date = date('Y-m-d H:i:s', $next_strtotime);
	
	return $next_voting_date;
}

/**
 * Get last voted date as per voting period
 * @param $post_id integer
 * @return string
 */
function GetWtgLastDate($voting_period) {
	switch($voting_period) {
		case "1":
			$day = 1;
			break;
		case "2":
			$day = 2;
			break;
		case "3":
			$day = 3;
			break;
		case "7":
			$day = 7;
			break;
		case "14":
			$day = 14;
			break;
		case "21":
			$day = 21;
			break;
		case "1m":
			$month = 1;
			break;
		case "2m":
			$month = 2;
			break;
		case "3m":
			$month = 3;
			break;
		case "6m":
			$month = 6;
			break;
		case "1y":
			$year = 1;
		  break;
	}
	
	$last_strtotime = strtotime(date('Y-m-d H:i:s'));
	$last_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
				date('m', $last_strtotime) - $month, date('d', $last_strtotime) - $day, date('Y', $last_strtotime) - $year);
	
	$last_voting_date = date('Y-m-d H:i:s', $last_strtotime);
	
	return $last_voting_date;
}

/**
 * Get like count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtgLikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wtg_like_post_show_symbols');
        $wtg_like_count = $wpdb->get_var($wpdb->prepare("SELECT SUM(value) FROM {$wpdb->prefix}wtg_like_post WHERE post_id = %s AND value >= 0", $post_id));
	
	if (!$wtg_like_count) {
		$wtg_like_count = 0;
	} else {
		if ($show_symbols) {
			$wtg_like_count = "+" . $wtg_like_count;
		} else {
			$wtg_like_count = $wtg_like_count;
		}
	}
	
	return $wtg_like_count;
}

/**
 * Get unlike count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtgUnlikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wtg_like_post_show_symbols');
        $Wtg_unlike_count = $wpdb->get_var($wpdb->prepare("SELECT SUM(value) FROM {$wpdb->prefix}wtg_like_post WHERE post_id = %s AND value <= 0", $post_id));
	
	if (!$Wtg_unlike_count) {
		$Wtg_unlike_count = 0;
	} else {
		if ($show_symbols) {
		} else {
			$Wtg_unlike_count = str_replace('-', '', $Wtg_unlike_count);
		}
	}
	
	return $Wtg_unlike_count;
}

// Load the widgets
require_once('wtg_like_post_widgets.php');

// Include the file for ajax calls
require_once('wtg_like_post_ajax.php');

// Associate the respective functions with the ajax call
add_action('wp_ajax_wtg_like_post_process_vote', 'WtgLikePostProcessVote');
add_action('wp_ajax_nopriv_wtg_like_post_process_vote', 'WtgLikePostProcessVote');
