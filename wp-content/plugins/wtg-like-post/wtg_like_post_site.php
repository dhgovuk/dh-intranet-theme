<?php
/**
 * Get the like output on site
 * @param array
 * @return string
 */
function GetWtgLikePost($arg = null) {
	 global $wpdb;
	 $post_id = get_the_ID();
	 $wtg_like_post = "";
	 
	 // Get the posts ids where we do not need to show like functionality
	 $allowed_posts = explode(",", get_option('wtg_like_post_allowed_posts'));
	 $excluded_posts = explode(",", get_option('wtg_like_post_excluded_posts'));
	 $excluded_categories = get_option('wtg_like_post_excluded_categories');
	 $excluded_sections = get_option('wtg_like_post_excluded_sections');
	 
	 if (empty($excluded_categories)) {
		  $excluded_categories = array();
	 }
	 
	 if (empty($excluded_sections)) {
		  $excluded_sections = array();
	 }
	 
	 $title_text = get_option('wtg_like_post_title_text');
	 $category = get_the_category();
	 $excluded = false;
	 
	 // Checking for excluded section. if yes, then dont show the like/dislike option
	 if ((in_array('home', $excluded_sections) && is_home()) || (in_array('archive', $excluded_sections) && is_archive())) {
		  return;
	 }
	 
	 // Checking for excluded categories
	 foreach($category as $cat) {
		  if (in_array($cat->cat_ID, $excluded_categories) && !in_array($post_id, $allowed_posts)) {
			   $excluded = true;
		  }
	 }
	 
	 // If excluded category, then dont show the like/dislike option
	 if ($excluded) {
		  return;
	 }
	 
	 // Check for title text. if empty then have the default value
	 if (empty($title_text)) {
		  $title_text_like = __('Like', 'wtg-like-post');
		  $title_text_unlike = __('Unlike', 'wtg-like-post');
	 } else {
		  $title_text = explode('/', get_option('wtg_like_post_title_text'));
		  $title_text_like = $title_text[0];
		  $title_text_unlike = $title_text[1];
	 }
	 
	 // Checking for excluded posts
	 if (!in_array($post_id, $excluded_posts)) {
		// Get the nonce for security purpose and create the like and unlike urls
		$nonce = wp_create_nonce("wtg_like_post_vote_nonce");
		$ajax_like_link = htmlentities(admin_url('admin-ajax.php?action=wtg_like_post_process_vote&task=like&post_id=' . $post_id . '&nonce=' . $nonce));
		$ajax_unlike_link = htmlentities(admin_url('admin-ajax.php?action=wtg_like_post_process_vote&task=unlike&post_id=' . $post_id . '&nonce=' . $nonce));

		$like_count = GetWtgLikeCount($post_id);
		$unlike_count = GetWtgUnlikeCount($post_id);
		$msg = GetWtgVotedMessage($post_id);
		$alignment = ("left" == get_option('wtg_like_post_alignment')) ? 'align-left' : 'align-right';

		$style = (get_option('wtg_like_post_voting_style') == "") ? 'style1' : get_option('wtg_like_post_voting_style');

		$wtg_like_post .= "<div class='watch-action hidden-print'>";
		$wtg_like_post .= "<div class='watch-position " . $alignment . "'>";
/*
		$wtg_like_post .= "<div class='action-like'>";
		$wtg_like_post .= "<a class='lbg-" . $style . " like-" . $post_id . " jlk' href='" . $ajax_like_link . "' data-task='like' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow'>";
		$wtg_like_post .= "<img src='" . plugins_url( 'images/pixel.gif' , __FILE__ ) . "' title='" . __($title_text_like, 'wtg-like-post') . "' />";
		if (get_option('wtg_like_post_show_vote_count'))
		{
			$wtg_like_post .= "<span class='lc-" . $post_id . " lc'>" . $like_count . "</span>";

		}
		$wtg_like_post .= "</a></div>";
*/
         //make this a button for accessibility issues
         $wtg_like_post .= "<div class='action-like'><input type='button' class='likeButton lbg-" . $style . " like-" . $post_id . " jlk' href='" . $ajax_like_link . "' data-task='like' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow' value='This page answered my question'></div>";

		if (get_option('wtg_like_post_show_dislike'))
		{
/*
			$wtg_like_post .= "<div class='action-unlike'>";
			$wtg_like_post .= "<a class='unlbg-" . $style . " unlike-" . $post_id . " jlk' href='" . $ajax_unlike_link . "' data-task='unlike' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow'>";
			$wtg_like_post .= "<img src='" . plugins_url( 'images/pixel.gif' , __FILE__ ) . "' title='" . __($title_text_unlike, 'wtg-like-post') . "' />";
			if (get_option('wtg_like_post_show_vote_count'))
			{
				$wtg_like_post .= "<span class='unlc-" . $post_id . " unlc'>" . $unlike_count . "</span>";
			}
			$wtg_like_post .= "</a></div> ";
*/
            $wtg_like_post .= "<div class='action-unlike'><input type='button' class='unlikeButton unlbg-" . $style . " unlike-" . $post_id . " jlk' href='" . $ajax_unlike_link . "' data-task='unlike' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow'  value='This page did not answer my question'></div>";
		}
		  
		$wtg_like_post .= "</div> ";
		$wtg_like_post .= "<div class='status-" . $post_id . " status " . $alignment . "'>&nbsp;&nbsp;" . $msg . "</div>";
		$wtg_like_post .= "</div><div class='wtg-clear'></div>";
	 }


	 if ($arg == 'put')
	 {
		return $wtg_like_post;
	 }
	 else
	 {
		echo $wtg_like_post;
	 }
}

/**
 * Show the like content
 * @param $content string
 * @param $param string
 * @return string
 */
function PutWtgLikePost($content) {
    // Do not display on policy-article pages
    $cats = get_the_category();
    $isPolicyArticle = false;
    foreach ($cats as $cat) {
        if ($cat->slug === 'policy-article') {
            $isPolicyArticle = true;
            break;
        }
    }
    if ($isPolicyArticle) {
        return $content;
    }
    // Do not display on "Fullwidth Page" pages
    if (get_page_template_slug(get_the_ID()) === 'template-fullwidth.php') {
        return $content;
    }
    // Do not display on "Policy Dashboard" pages
    if (get_page_template_slug(get_the_ID()) === 'template-policy-dashboard.php') {
        return $content;
    }

	 $show_on_pages = false;
	 
	 if ((is_page() && get_option('wtg_like_post_show_on_pages')) || (!is_page())) {
		  $show_on_pages = true;
	 }
  
	 if (!is_feed() && $show_on_pages) {	 
		  $wtg_like_post_content = GetWtgLikePost('put');
		  $wtg_like_post_position = get_option('wtg_like_post_position');
		  
		  if ($wtg_like_post_position == 'top') {
			   $content = $wtg_like_post_content . $content;
		  } elseif ($wtg_like_post_position == 'bottom') {
			   $content = $content . $wtg_like_post_content;
		  } else {
			   $content = $wtg_like_post_content . $content . $wtg_like_post_content;
		  }
	 }
	 
	 return $content;
}

/**
 * Filter the content to add voting to every post / page
 * and turns it on by default
 */

function WtgAddVoteToContent()
{
    add_filter('the_content', 'PutWtgLikePost');
}
//add voting by default.
WtgAddVoteToContent();

/**
 * Disables voting on posts/pages.
 */
function WtgRemoveVoteFromContent()
{
    remove_filter('the_content', 'PutWtgLikePost');
}

/**
 * Get already voted message
 * @param $post_id integer
 * @param $ip string
 * @return string
 */
function GetWtgVotedMessage($post_id, $ip = null) {
	return 'Did this page answer your question?';
//	 global $wpdb;
//	 $wtg_voted_message = '';
//	 $voting_period = get_option('wtg_like_post_voting_period');
//
//	 if (null == $ip) {
//		  $ip = WtgGetRealIpAddress();
//	 }
//
//	 $query = "SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wtg_like_post WHERE post_id = '$post_id' AND ip = '$ip'";
//
//	 if ($voting_period != 0 && $voting_period != 'once') {
//		  // If there is restriction on revoting with voting period, check with voting time
//		  $last_voted_date = GetWtgLastDate($voting_period);
//		  $query .= " AND date_time >= '$last_voted_date'";
//	 }
//
//	 $wtg_has_voted = $wpdb->get_var($query);
//
//	 if ($wtg_has_voted > 0) {
//		  $wtg_voted_message = get_option('wtg_like_post_voted_message');
//	 }
//
//	 return $wtg_voted_message;
}

add_shortcode('most_liked_posts', 'WtgMostLikedPostsShortcode');

/**
 * Most liked posts shortcode
 * @param $args array
 * @return string
 */
function WtgMostLikedPostsShortcode($args) {
	 global $wpdb;
	 $most_liked_post = '';
	 
	 if ($args['limit']) {
		  $limit = $args['limit'];
	 } else {
		  $limit = 10;
	 }
	 
	 if (!empty($args['time']) && $args['time'] != 'all') {
		  $last_date = GetWtgLastDate($args['time']);
                  $where .= $wpdb->prepare(" AND date_time >= %s", $last_date);
	 }
	 
	 // Getting the most liked posts
	 $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wtg_like_post` L, {$wpdb->prefix}posts P ";
         $query .= $wpdb->prepare("WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0 $where GROUP BY post_id ORDER BY like_count DESC, post_title ASC LIMIT %d", $limit);

	 $posts = $wpdb->get_results($query);
 
	 if (count($posts) > 0) {
		  $most_liked_post .= '<table class="most-liked-posts-table">';
		  $most_liked_post .= '<tr>';
		  $most_liked_post .= '<td>' . __('Title', 'wtg-like-post') .'</td>';
		  $most_liked_post .= '<td>' . __('Like Count', 'wtg-like-post') .'</td>';
		  $most_liked_post .= '</tr>';
	   
		  foreach ($posts as $post) {
			   $post_title = stripslashes($post->post_title);
			   $permalink = get_permalink($post->post_id);
			   $like_count = $post->like_count;
			   
			   $most_liked_post .= '<tr>';
			   $most_liked_post .= '<td><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></td>';
			   $most_liked_post .= '<td>' . $like_count . '</td>';
			   $most_liked_post .= '</tr>';
		  }
	   
		  $most_liked_post .= '</table>';
	 } else {
		  $most_liked_post .= '<p>' . __('No posts liked yet.', 'wtg-like-post') . '</p>';
	 }
	 
	 return $most_liked_post;
}

add_shortcode('recently_liked_posts', 'WtgRecentlyLikedPostsShortcode');

/**
 * Get recently liked posts shortcode
 * @param $args array
 * @return string
 */
function WtgRecentlyLikedPostsShortcode($args) {
	 global $wpdb;
	 $recently_liked_post = '';
	 
	 if ( $args['limit'] ) {
		  $limit = $args['limit'];
	 } else {
		  $limit = 10;
	 }
	 
	 $show_excluded_posts = get_option('wtg_like_post_show_on_widget');
	 $excluded_post_ids = explode(',', get_option('wtg_like_post_excluded_posts'));
	 
	 if ( !$show_excluded_posts && count( $excluded_post_ids ) > 0 ) {
		  $where = "AND post_id NOT IN (" . implode(',', array_map('absint', explode(',', get_option('wtg_like_post_excluded_posts')))) . ")";
	 }

	 // Get the post IDs recently voted
	 $recent_ids = $wpdb->get_col("SELECT DISTINCT(post_id) FROM `{$wpdb->prefix}wtg_like_post`
								  WHERE value > 0 $where GROUP BY post_id ORDER BY MAX(date_time) DESC");

	 if ( count( $recent_ids ) > 0 ) {
                  $where = "AND post_id IN(" . implode(",", array_map('absint', $recent_ids)) . ")";
	 
		  // Getting the most liked posts
		  $query = $wpdb->prepare("SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wtg_like_post` L, {$wpdb->prefix}posts P 
					WHERE L.post_id = P.ID AND post_status = 'publish' $where GROUP BY post_id
                                        ORDER BY FIELD(post_id, " . implode(",", array_map('absint', $recent_ids)) . ") ASC LIMIT %d", $limit);
	 
		  $posts = $wpdb->get_results($query);
	 
		  if ( count( $posts ) > 0 ) {
			   $recently_liked_post .= '<table class="recently-liked-posts-table">';
			   $recently_liked_post .= '<tr>';
			   $recently_liked_post .= '<td>' . __('Title', 'wtg-like-post') .'</td>';
			   $recently_liked_post .= '</tr>';
			
			   foreach ( $posts as $post ) {
					$post_title = stripslashes($post->post_title);
					$permalink = get_permalink($post->post_id);
					
					$recently_liked_post .= '<tr>';
					$recently_liked_post .= '<td><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></td>';
					$recently_liked_post .= '</tr>';
			   }
			
			   $recently_liked_post .= '</table>';
		  }
	 } else {
		  $recently_liked_post .= '<p>' . __('No posts liked yet.', 'wtg-like-post') . '</p>';
	 }
	 
	 return $recently_liked_post;
}

/**
 * Add the javascript for the plugin
 * @param no-param
 * @return string
 */
function WtgLikePostEnqueueScripts() {
	 wp_register_script( 'wtg_like_post_script', plugins_url( 'js/wtg_like_post.js', __FILE__ ), array('jquery') );
	 wp_localize_script( 'wtg_like_post_script', 'wtglp', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

	 wp_enqueue_script( 'jquery' );
	 wp_enqueue_script( 'wtg_like_post_script' );
}

/**
 * Add the required stylesheet
 * @param void
 * @return void
 */
function WtgLikePostAddHeaderLinks() {
	 echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'css/wtg_like_post.css', __FILE__) . '" media="screen" />';
}
