<?php
/**
 * Plugin Name: Edit Flow extension by WTG
 * Plugin URI:
 * Description: Adds custom DH user roles and capabilities based on those roles. Need to create custom statuses; original-material, in-progress, content-review, editorial-review, approved-to-publish
 * Version: 1.0
 * Author: WTG
 * Author URI:
 * License:
 */
if (! defined('DH_CRON_JOB'))
{

	// Override the revision post in WordPress.  Makes sense to put this here as it's part of the revisionary process.
	define( 'WP_POST_REVISIONS', 100 );

	/**
	 * Create needed roles in code
	 *
	 * dh_editor
	 * dh_content_owner
	 * dh_lead_editor
	 */
	register_activation_hook( __FILE__, 'WTG_efe_add_DH_users' );
	function WTG_efe_add_DH_users()
	{
		//explicitly define role capabilities
		$all_capabilities = array(
		'delete_others_pages' => true,
		'delete_others_posts' => true,
		'delete_pages' => true,
		'delete_posts' => true,
		'delete_private_pages' => true,
		'delete_private_posts'  => true,
		'delete_published_pages' => true,
		'delete_published_posts' => true,
		'edit_dashboard' => true,
		'edit_files' => true,
		'edit_others_pages' => true,
		'edit_others_posts' => true,
		'edit_pages' => true,
		'edit_post_subscriptions' => true,
		'edit_posts' => true,
		'edit_private_pages' => true,
		'edit_private_posts' => true,
		'edit_published_pages' => true,
		'edit_published_posts' => true,
		'edit_theme_options' => true,
		'edit_themes' => true,
		'edit_usergroups' => true,
		'ef_view_calendar' => true,
		'ef_view_story_budget' => true,
		'export' => true,
		'import' => true,
		'list_roles' => true,
		'list_users' => true,
		'manage_categories' => true,
		'manage_links' => true,
		'manage_options' => true,
		'moderate_comments' => true,
		'publish_pages' => true,
		'publish_posts' => true,
		'read' => true,
		'read_private_pages' => true,
		'read_private_posts' => true,
		'restrict_content' => true,
		'unfiltered_upload' => true,
		'update_plugins' => true,
		'upload_files' => true,
		);

		//OR extend an already existing role
		//$role = get_role('contributor');
		//$default_capabilities = $role->capabilities;

		$error_msg = '';

		$result = add_role('dh_editor' ,__( 'DH Editor' ), $all_capabilities);
		if ( $result == null)
		{
			$error_msg .= 'Could not create DH Editor role.<br>';
		}

		$result = add_role(
			'dh_lead_editor', __( 'DH Lead Editor' ), $all_capabilities);
		if ( $result == null)
		{
			$error_msg .= 'Could not create DH Lead Editor role.<br>';
		}

		//remove publish posts, publish pages for dh_content_owner
		unset( $all_capabilities['publish_pages'] );
		unset( $all_capabilities['publish_posts'] );
		$result = add_role('dh_content_owner', __( 'DH Content Owner' ), $all_capabilities);
		if ( $result == null)
		{
			$error_msg .=  'Could not create DH Content Owner role.<br>';
		}
		if ($error_msg != '')
		{
			echo "<div class='error'><p>".$error_msg."</p></div>";
		}

		//chck all current roles set wp_user_level = 1 so they count as authors.
		$args = array(
			'fields' => array(
				'ID',
			)
		);		
		$roles = array('dh_editor', 'dh_lead_editor', 'dh_content_owner');
		foreach ($roles as $role) {
			$args['role'] = $role;
			$dhUsers = get_users($args);
			foreach ($dhUsers as $dhUser) {				
				$user_level = get_user_meta($dhUser->ID, 'wp_user_level', true);
				//echo ($dhUser->ID) . " user-level=".$user_level."<br>";
				if ($user_level == 0) {
					//echo "updated ".$dhUser->ID."<br>";
					update_user_meta($dhUser->ID, 'wp_user_level', '1');		
				}			
			}				
		}				
	}

	add_action('user_register', 'WTG_update_user_level');
	function WTG_update_user_level($user_id) 
	{
		if(user_can($user_id,'dh_editor') || user_can($user_id,'dh_content_owner') || user_can($user_id,'dh_lead_editor')) {
			update_user_meta($user_id, 'wp_user_level', '1');
		}
	}

	/**
	 * Deactivation
	 *
	 * remove created roles:
	 * dh_editor
	 * dh_content_owner
	 * dh_lead_editor
	 */
	register_deactivation_hook( __FILE__, 'WTG_efe_deactivate' );
	function WTG_efe_deactivate()
	{
		remove_role( 'dh_editor' );
		remove_role( 'dh_content_owner' );
		remove_role( 'dh_lead_editor' );
	}

	/**
	 * Limit custom statuses based on user role
	 *
	 * @see http://editflow.org/extend/limit-custom-statuses-based-on-user-role/
	 * @param array $custom_statuses The existing custom status objects
	 * @return array $custom_statuses Our possibly modified set of custom statuses
	 */
	add_filter( 'ef_custom_status_list', 'WTG_efe_limit_custom_statuses_by_role' );
	function WTG_efe_limit_custom_statuses_by_role( $custom_statuses )
	{
		$current_user = wp_get_current_user();
		$permitted_statuses = array();
		//don't filter statuses for admins
		//echo "test - ".$current_user->roles[0];
		if ($current_user->roles[0] != 'administrator')
		{
			// Depending on users change allowed statuses in the dropdown
			// NOTE: only tests first role in array.
			switch( $current_user->roles[0] )
			{
				case 'dh_editor':
					array_push($permitted_statuses,'draft','original-material','in-progress','content-review');
					break;

				case 'dh_content_owner':
					array_push($permitted_statuses,'content-review','editorial-review');
					break;

				case 'dh_lead_editor':
					array_push($permitted_statuses,'draft','original-material','in-progress','content-review','editorial-review','approved-to-publish');
					break;
			}
			// Remove the custom status if it's not whitelisted
			foreach( $custom_statuses as $key => $custom_status )
			{
				if ( !in_array( $custom_status->slug, $permitted_statuses ) )
				{
					unset( $custom_statuses[$key] );
				}
			}
		}
		return $custom_statuses;
	}

	/**
	 * Hide the "Publish" button until a post is ready to be published
	 *
	 * @see http://editflow.org/extend/hide-the-publish-button-for-certain-custom-statuses/
	 */
	add_action( 'admin_head', 'WTG_efe_hide_publish_button_until' );
	function WTG_efe_hide_publish_button_until()
	{
		if ( ! function_exists( 'EditFlow' ) )
			return;

		if ( ! EditFlow()->custom_status->is_whitelisted_page() )
			return;

		// Show the publish button if the post has one of these statuses
		$show_publish_button_for_status = array(
			'approved-to-publish',
			// The statuses below are WordPress' public statuses
			// TODO: test/ask if we need to consider these
			'future',
			'publish',
			'schedule',
			'private',
		);
		if ( ! in_array( get_post_status(), $show_publish_button_for_status ) )
		{
			?>
			<style>
				#publishing-action { display: none; }
			</style>
		<?php
		}
	}

	/**
	 * Show the post content as read only for Content Owners
	 */
	add_action( 'admin_head', 'WTG_efe_lock_editor_for_user' );
	function WTG_efe_lock_editor_for_user()
	{
		$current_user = wp_get_current_user();
		if ($current_user->roles[0]  == 'dh_content_owner')
		{
			// hide the whole editor box from this user
			add_action( 'edit_form_after_title', 'myprefix_edit_form_after_title' );
			function myprefix_edit_form_after_title()
			{
				//should enque style sheet
				echo '<style>#postdivrich {display:none;}</style>';
			}

			//show the post content as rendered html
			add_action( 'edit_form_after_editor', 'myprefix_edit_form_after_editor' );
			function myprefix_edit_form_after_editor()
			{
				global $post;
				echo "<div style='display:block; padding:6px 6px 0 6px; background:#444; color:#FFF; font-size: 14px;'>Preview Content</div>";
				echo "<div style='display:block; padding:10px; margin-bottom:20px; background:#FFF; border:solid 4px #444;'>";
				echo nl2br($post->post_content);
				echo "</div>";
			}
		}
	}

	/*
	 * When a post goes to content-review email the content owner specified in the editpost metadata
	 */
	add_filter( 'ef_notification_recipients', 'WTG_email_content_owner_meta', 10, 3 );
	function WTG_email_content_owner_meta( $recipients, $post, $return_as_string )
	{
		//if this posts status is in content review then also send any email notifications
		//to the user specified as the content owner in the editflow metadata.
		if ( ($post->post_status == 'content-review') || ($post->post_status == 'publish') )
		{
			$content_owner_user_id = get_post_meta( $post->ID, '_ef_editorial_meta_user_content-owner-to-review', true );
			$content_owner_user_email = get_userdata($content_owner_user_id)->user_email;
			//check if email is already in list
			if (!in_array($content_owner_user_email, $recipients))
			{
				array_push($recipients,$content_owner_user_email);
			}
		}
		return $recipients;
	}

	/**
	 * WTG Check User Role
	 *
	 * Checks if a particular user has a role. Returns true if a match was found.
	 *
	 * @access		public
	 * @param		string $role Role name.
	 * @param		int						$user_id (Optional) The ID of a user. Defaults to the current user.
	 * @return		bool
	 */
	function wtg_check_user_role( $role, $user_id = null )
	{
		// If a $user_id is specified, then get the userdata from that else wp_get_current_user().
		$user = is_numeric($user_id) ? get_userdata($user_id) : wp_get_current_user();

		// If there is no $user object, return FALSE, otherwise, check the user is in the given $role.
		return empty($user) ? FALSE : in_array($role, (array) $user->roles);
	}

	/**
	Part 1: We have a parent comment, to which the user is replying
	and the new comment, the one which user is submitting. We want
	to get the details of the parent comment, and send an email to
	the parent comment owner saying	that there's a new reply to
	his/her comment, with the new comment details.
	Part 2: When there's a reply to a page, we want	the page owner
	to be notified of the new comment, if page owner does not exist
	then revert to default functionality.
	 */
	add_action('wp_insert_comment', function()
	{
		if( ! isset($_POST))
			return;

		$parent_user_email   = NULL;
		$parent_comment_data = NULL;

		// Latest comment will be the first element in the get_comments returned array.
		$latest_comment = reset(get_comments(array('post_id' => $_POST['comment_post_ID'])));

		$latest_comment->url 	   = get_comment_link($latest_comment->comment_ID);
		$latest_comment_user_meta  = get_user_meta($latest_comment->user_id);
		$latest_comment->full_name = $latest_comment_user_meta['first_name'][0] . ' ' . $latest_comment_user_meta['last_name'][0];

		$post_details = get_post($latest_comment->comment_post_ID);

		$email_subject = '[DH Intranet] Reply to your comment: "' . $post_details->post_title . '"';

		// String used to inform a comment owner that there's a reply to their comment.
		$email_body_reply  = 'New reply to your commment on post "' . $post_details->post_title . '"' . PHP_EOL;
		// String used to inform the page owner that there's a new comment on their page.
		$email_body_page   = 'New comment on your post "' . $post_details->post_title . '"' . PHP_EOL;

		$email_body  = 'Author : ' . $latest_comment->full_name . PHP_EOL;
		$email_body .= 'Work e-mail : ' . $latest_comment->comment_author_email . PHP_EOL;
		$email_body .= 'Comment: ' . PHP_EOL . $latest_comment->comment_content . PHP_EOL;
		$email_body .= 'New comment URL: ' . $latest_comment->url . PHP_EOL . PHP_EOL;
		$email_body .= 'You can see all comments on this post here:' . PHP_EOL;
		$email_body .= get_permalink($post_details->ID) . '#comments';

		$email_body_reply .= $email_body;
		$email_body_page  .= $email_body;

		add_filter('wp_mail_from', function($email_address)
		{
			return DH_MAIL_FROM;
		});

		add_filter('wp_mail_from_name', function($name)
		{
			return DH_MAIL_FROM_NAME;
		});

		// Send comment reply notification.
		if($latest_comment->comment_parent != 0)
		{
			$parent_comment    = get_comment($latest_comment->comment_parent);
			$parent_user       = get_user_by('id', $parent_comment->user_id);
			$parent_user_email = $parent_user->data->user_email;

			wp_mail($parent_user_email, $email_subject, $email_body_reply);
		}

		$post_meta = get_post_meta($post_details->ID);

		// If a page owner is set, send the new comment notification
		// email to the page owner.
		if(isset($post_meta['page_owner_email']) && ! empty($post_meta['page_owner_email'][0]))
		{
			add_filter('comment_notification_recipients', function($emails) use ($post_meta)
			{
				return array($post_meta['page_owner_email'][0]);
			});

			add_filter('comment_notification_text', function($notify_message) use ($email_body_page)
			{
				return $email_body_page;
			});
		}
	});
}