<?php

/**
 * @package DH_News_Aggregation
 * @version 0.1
 */
/*
Plugin Name: WTG Category Newsletter Subscribe Button
Plugin URI: 
Description: Allows users to subscriber to category based newsletters via a simple button...
Author: WTG: Rob Waller
Version: 0.1
Author URI: http://wtg.co.uk
*/

function subscribeToCategory($db,$userid,$newsletters) {

	$c_ID = (int) $_GET['cSbs'];
	
	if (empty($newsletters)) {
		$db->createMeta($userid,array($c_ID))->add();
	}
	else {
		if (!in_array($c_ID, unserialize($newsletters['meta_value']))) {
			$nl = unserialize($newsletters['meta_value']);

			array_push($nl,$c_ID);

			$db->updateMeta($userid,$nl)->add();
		}
	}

}

function unSubscribeFromCategory($db,$userid,$newsletters) {
	$c_ID = (int) $_GET['cSbs'];

	$nl = unserialize($newsletters['meta_value']);

	foreach ($nl as $i => $cat) {
		if ($cat == $c_ID) {
			unset($nl[$i]);
		}

		$db->updateMeta($userid,$nl)->add();
	}
}

function createSubscribeButton($db,$post,$userid,$newsletters) {

		$cats = $db->get_newsletter_cats($post->ID);
		
		require_once('view/buttons.php');
}

function initSubscribeButton($post) {

	global $wpdb;

	require_once('models/db_subscribe.php');

	$db = new db_subscribe($wpdb);

	$userid = get_current_user_id();
	
	$newsletters = $db->getMeta($userid)->get();

	if (isset($_GET['cSbs'])&&!empty($_GET['cSbs'])) {
		if ($_GET['s']==1) {
			subscribeToCategory($db,$userid,$newsletters->result);
		}
		elseif ($_GET['s']==0) {
			unSubscribeFromCategory($db,$userid,$newsletters->result);
		}
	}

	$newsletters2 = $db->getMeta($userid)->get();
	
	createSubscribeButton($db,$post,$userid,$newsletters2->result);
}

add_shortcode('subscribe_button','initSubscribeButton');

?>