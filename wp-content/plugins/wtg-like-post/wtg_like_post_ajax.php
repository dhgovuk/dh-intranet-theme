<?php
function WtgLikePostProcessVote() {
	global $wpdb;
	
	// Get request data
	$post_id = (int)$_REQUEST['post_id'];
	$task = $_REQUEST['task'];
	$ip = WtgGetRealIpAddress();
	
	// Check for valid access
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'wtg_like_post_vote_nonce' ) ) {
		$error = 1;
		$msg = __( 'Invalid access', 'wtg-like-post' );
	} else {
		// Get setting data
		$is_logged_in = is_user_logged_in();
		$login_required = get_option( 'wtg_like_post_login_required' );
		$can_vote = false;

		if ( $login_required && !$is_logged_in ) {
			// User needs to login to vote but has not logged in
			$error = 1;
			$msg = get_option( 'wtg_like_post_login_message' );
		} else {
			$has_already_voted = HasWtgAlreadyVoted( $post_id, $ip );
			$voting_period = get_option( 'wtg_like_post_voting_period' );
			$datetime_now = date( 'Y-m-d H:i:s' );
			
			if ( "once" == $voting_period && $has_already_voted ) {
				// User can vote only once and has already voted.
				$error = 1;
				$msg = get_option( 'wtg_like_post_voted_message' );
			} elseif ( '0' == $voting_period ) {
				// User can vote as many times as he want
				$can_vote = true;
			} else {
				if ( !$has_already_voted ) {
					// Never voted befor so can vote
					$can_vote = true;
				} else {
					// Get the last date when the user had voted
					$last_voted_date = GetWtgLastVotedDate( $post_id, $ip );
					
					// Get the bext voted date when user can vote
					$next_vote_date = GetWtgNextVoteDate( $last_voted_date, $voting_period );
					
					if ( $next_vote_date > $datetime_now ) {
						$revote_duration = ( strtotime( $next_vote_date ) - strtotime( $datetime_now ) ) / ( 3600 * 24 );
						
						$can_vote = false;
						$error = 1;
						$msg = __( 'You can vote after', 'wtg-like-post' ) . ' ' . ceil( $revote_duration ) . ' ' . __( 'day(s)', 'wtg-like-post' );
					} else {
						$can_vote = true;
					}
				}
			}
		}
		
		if ( $can_vote ) {
			$current_user = wp_get_current_user();
			$user_id = (int)$current_user->ID;
			
			if ( $task == "like" ) {
				if ( $has_already_voted ) {
                                        $query = $wpdb->prepare(
                                              "UPDATE {$wpdb->prefix}wtg_like_post SET " .
                                              "value = value + 1, " .
                                              "date_time = %s " .
                                              "WHERE post_id = %s AND " .
                                              "ip = %s",
                                              date( 'Y-m-d H:i:s' ),
                                              $post_id,
                                              $ip
                                        );
				} else {			
                                        $query = $wpdb->prepare(
                                              "INSERT INTO {$wpdb->prefix}wtg_like_post SET " .
                                              "post_id = %s, " .
                                              "value = '1', " .
                                              "date_time = %s, " .
                                              "ip = %s",
                                              $post_id,
                                              date( 'Y-m-d H:i:s' ),
                                              $ip
                                        );
				}
			} else {
				if ( $has_already_voted ) {
                                        $query = $wpdb->prepare(
                                              "UPDATE {$wpdb->prefix}wtg_like_post SET " .
                                              "dislike_value = value + 1, " .
                                              "date_time = %s " .
                                              "WHERE post_id = %s AND " .
                                              "ip = %s",
                                              date( 'Y-m-d H:i:s' ),
                                              $post_id,
                                              $ip
                                        );
				} else {
                                        $query = $wpdb->prepare(
                                              "INSERT INTO {$wpdb->prefix}wtg_like_post SET " .
                                              "post_id = %s, " .
                                              "dislike_value = '1', " .
                                              "date_time = %s, " .
                                              "ip = %s" .
                                              $post_id,
                                              date( 'Y-m-d H:i:s' ),
                                              $ip
                                            );
				}
			}
			
			$success = $wpdb->query( $query );
			
			if ($success) {
				$error = 0;
				$msg = get_option( 'wtg_like_post_thank_message' );
			} else {
				$error = 1;
				$msg = __( 'Could not process your vote.', 'wtg-like-post' );
			}
		}
		
		$options = get_option( 'wtg_most_liked_posts' );
		$number = $options['number'];
		$show_count = $options['show_count'];
		
		$wtg_like_count = GetWtgLikeCount( $post_id );
		$wtg_unlike_count = GetWtgUnlikeCount( $post_id );
	}
	
	// Check for method of processing the data
	if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
		$result = array(
					"msg" => $msg,
					"error" => $error,
					"like" => $wtg_like_count,
					"unlike" => $wtg_unlike_count
				);
		
		echo json_encode($result);
	} else {
		header( "location:" . $_SERVER["HTTP_REFERER"] );
	}
	
	exit;
}
