<?php
/**
 * Registration Email
 *
 * Emails new users alternative text when they register.
 *
 * Plugin Name:		Alternative Registration Email
 * Description:		Changes the default user emails for alternative text.
 * Version			1.0
 * Author:			Adam Lewis <adam.lewis@wtg.co.uk>
 * Author URI:		http://wtg.co.uk
 */
if (! function_exists('wp_new_user_notification'))
{
	function wp_new_user_notification($user_id, $plaintext_pass = '')
	{
		add_filter('wp_mail_content_type', function()
		{
			return 'text/html';
		});

		$user = get_userdata($user_id);

		$subject = '[DH Intranet] Here is your temporary intranet password.';

//		$headers = "From: " . get_option('blogname') . " <" . get_option('admin_email') . ">";

		$message = "<p>Thanks for registering for the intranet.  Here is confirmation of the email address that you " .
			"use to sign in, and a temporary password which you'll need to reset to make it more memorable.  You can " .
			"do this in the 'My profile' area from the left-hand menu.</p>\r\n\r\n";
		$message .= sprintf('<p>Work email address: %s', stripslashes($user->user_email)) . "</p>\r\n\r\n";
		$message .= sprintf('<p>Temporary password: %s', stripslashes($plaintext_pass)) . "</p>\r\n\r\n";
		$message .= "<p><a href='" . home_url() . "/login'>Click here to sign in.</a></p>\r\n\r\n";
		$message .= "<p>Thanks <br />\r\n\r\n The DH intranet team <br />\r\n\r\n intranet@dh.gsi.gov.uk <br />\r\n\r\n";

		$admin_message = "<p>New user registration on your site DH Intranet:</p>";
		$admin_message .= sprintf("<p>Work e-mail address: %s</p>", stripslashes($user->user_email));

		wp_mail(get_option('admin_email'), 'New user created', $admin_message, $headers);

		wp_mail(stripslashes($user->user_email), $subject, $message, $headers);

		remove_filter ( 'wp_mail_content_type', 'wpmail_content_type' );
	}
}
