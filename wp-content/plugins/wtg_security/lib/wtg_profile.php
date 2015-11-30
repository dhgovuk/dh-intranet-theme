<?php
/**
 * WTG
 *
 * @package			wtg_security
 * @author	 		Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright		(C) 2014 Web Technologies Group Ltd.
 * @link			http://www.wtg.co.uk
 * @since			0.3
 */

if (! class_exists('wtg_profile'))
{
	/**
	 * Class wtg_restrict
	 *
	 *
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		wtg_profile
	 * @since			0.3
	 */
	class wtg_profile extends wtg_security
	{
		/**
		 * Profile redirect
		 *
		 * Prevent people from going to the old profile page in the backend and force them to go to the new profile
		 * page.
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 */
		public function profile_redirect()
		{
			global $pagenow;

			if (is_user_logged_in() && is_admin() && $pagenow === 'profile.php' && ! isset($_REQUEST['page']))
			{
				wp_redirect('/profile');
			}
		}

		public function request_profile()
		{
			load_textdomain('default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo');
			register_admin_color_schemes();
		}

		/**
		 * Hook
		 *
		 * Hooks up
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 * @static
		 */
		public static function hook()
		{
			$that = new self();

			add_action('user_profile_update_errors', array($that, 'validate_profile'), 10, 3);

			remove_action( 'wp_head', 'feed_links',                       2 );
			remove_action( 'wp_head', 'feed_links_extra',                 3 );
			remove_action( 'wp_head', 'rsd_link'                            );
			remove_action( 'wp_head', 'wlwmanifest_link'                    );
			remove_action( 'wp_head', 'parent_post_rel_link',            10 );
			remove_action( 'wp_head', 'start_post_rel_link',             10 );
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
			remove_action( 'wp_head', 'rel_canonical'                       );

			add_action( 'login_head', 'wp_no_robots' );

			add_action('init', array($that, 'profile_redirect'));
			add_shortcode('wtg-security-profile-page', array($that, 'profileShortCode'));


            /**
             * when the user changes there password, delete the cache and log them back inwith the new password.
             */
            add_action('profile_update', function($errors)
            {
                $password = $_POST['pass1'];
                //if user has enter a new password
                if ((isset($password)) && ($password !== ''))
                {
                    global $wpdb;
                    $user_id = $_POST['user_id'];

                    $md5password = wp_hash_password($password);
                    $userdata = get_userdata($user_id);
                    $username = $userdata->user_login;

                    // use $wpdb->prepare()
                    $sql = $wpdb->prepare("UPDATE wp_users SET user_login = %s, user_pass = %s WHERE ID = %d'", $username, $md5password, $user_id);
                    //$q = "UPDATE wp_users SET user_login = '".$username."', user_pass = '{$md5password}' WHERE ID = '" . $user_id . "'";
                    $wpdb->query($sql);
                    // delete cache data and sign back into wordpress
                    wp_cache_delete($user_id, 'users');
                    wp_cache_delete($username, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
                    wp_logout();
                    wp_signon(array('user_login' => $username, 'user_password' => $password));
                }
                // will redirect to the homepage if profile successfully updates, otherwise the profile
                // page will be reloaded with an error message.
            });

			add_filter('phpmailer_init', array($that, 'home_email_reset_password'));

			if ( force_ssl_admin() && ! is_ssl() ) {
				if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
					wp_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
					exit;
				} else {
					wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
					exit;
				}
			}
		}

		/**
		 * Validate profile
		 *
		 * @access		public
		 * @since		0.3
		 * @param		WP_Error		$errors
		 * @return		WP_Error
		 */
		public function validate_profile(WP_Error $errors)
		{
			$message_bag = array();

			$current_user = wp_get_current_user();

			if (! isset($_POST['first_name']) || trim($_POST['first_name']) === '')
			{
				$message_bag[] = '<strong>ERROR</strong>: Please enter your first name.';
			}

			if (! isset($_POST['last_name']) || trim($_POST['last_name']) === '')
			{
				$message_bag[] = '<strong>ERROR</strong>: Please enter your last name';
			}

			// Skip email validation if the user is not trying to change the email.
			// Skip email validation if the admin is adding a new user.
			// Added for DH-407 and DH-408.
			if($current_user->user_email != $_POST['email'] && $_POST['action'] != 'createuser')
			{
				if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
				{
					$message_bag[] = '<strong>ERROR</strong>: The email address isn’t correct.';
				}
				elseif (! in_array(array_pop(explode('@', strtolower($_POST['email']))), $this->config['email_domain_whitelist']))
				{
					if($_SERVER['REQUEST_URI'] != '/wp-admin/user-edit.php')
					{
						$message_bag[] = '<strong>ERROR</strong>: The email address isn’t correct.';
					}
				}
			}

			empty($message_bag) ? null : $errors->add('validation_errors', implode('<br />', $message_bag));

			return $errors;
		}

		/**
		 * Home Email Reset Password
		 *
		 * Hooks into the PHPMailer to add the home_email value from the users custom meta data if it exists.
		 *
		 * @access		public
		 * @since		0.3
		 * @param		PHPMailer		$phpmailer
		 * @return		PHPMailer
		 */
		public function home_email_reset_password(PHPMailer $phpmailer)
		{
			if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword')
			{
				/*
				 * Okay, I'm not proud of this, but the version of PHPMailer that WP 3.9 uses is out of date and the
				 * method getToAddresses() doesn't exist, so there is no other way to retrieve the users email.  In
				 * theory, the $_POST data could be either the email or the username, so we can't risk using that
				 * either.  The only way to retrieve the users email is to use reflection!
				 */
				$reflector				= new \ReflectionClass($phpmailer);
				$classProperty			= $reflector->getProperty('to');
				$classProperty->setAccessible(true);
				$workEmail				= reset(reset($classProperty->getValue($phpmailer)));

				$user					= get_user_by('email', $workEmail);
				$homeEmail				= get_user_meta($user->ID, 'home_email', true);

				$homeEmail && $homeEmail !== '' ? $phpmailer->addAddress($homeEmail) : null;
			}

			return $phpmailer;
		}

		/**
		 * Profile Short code
		 *
		 * This shortcode produces the form for the user to see and edit their profile.
		 *
		 * @access		public
		 * @since		0.3
		 * @return		bool|string
		 */
		public function profileShortCode()
		{
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );

			define('IS_PROFILE_PAGE', true);

			load_textdomain('default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo');

			register_admin_color_schemes();
			wp_enqueue_script( 'user-profile' );

			$current_user = wp_get_current_user();

			$message = false;
			$errors = false;

			// Quick hack here, WordPress doesn't have any built in functions to get the URI segments?
			$segments = explode("/", parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH));

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && preg_match('/profile/', $segments[0]))
			{
				$errors = false;

				check_admin_referer('update_user_' . $current_user->ID);

				if (! current_user_can('edit_user', $current_user->ID))
				{
					wp_die('You do not have permission to edit this user.');
				}

				do_action('personal_options_update', $current_user->ID);

				// Nasty little hack due to a bug in this version of WordPress.
				require_once(ABSPATH . 'wp-admin/includes/screen.php');

				update_user_meta($current_user->ID, 'home_email', $_POST['home_email']);
				update_user_meta($current_user->ID, '_per_user_feeds_cats',array_keys($_POST['newsletter_cats']));
				update_user_meta($current_user->ID, 'news_locale', (int)$_POST['news-locale']);

                $errors = edit_user($current_user->ID);
                $errors =  is_object($errors) ? $errors : false;

				$message = $errors ? false : 'Your profile details have been updated successfully.';
			}

			$data = array(
				'current_user'				=> wp_get_current_user(),
				'profileuser'				=> get_user_to_edit( wp_get_current_user()->ID ),
				'errors'					=> $this->sort_errors($errors),
				'message'					=> $message,
				'home_email'				=> get_user_meta($current_user->ID, 'home_email', true),
				'locations'					=> get_terms('news-locale', array('hide_empty' => false)),
				'current_location'			=> $this->get_current_location(),
				'newsletter_cats'			=> $this->get_newsletter_cats(),
				'selected_newsletter_cats'	=> reset(get_user_meta($current_user->ID, '_per_user_feeds_cats'))
			);

			return $this->view('profile', $data);
		}

		/**
		 * Sort Errors
		 *
		 * This method pulls apart the WP_ERROR object and puts into a usable string for the page.
		 *
		 * @access		private
		 * @since		0.3
		 * @param		$errorsToSort
		 * @return		bool|string
		 */
		private function sort_errors($errorsToSort)
		{
			if ($errorsToSort)
			{
				$output = '';
				$wp_error = new WP_Error();

				$wp_error->add('error', $errorsToSort);

				if ($wp_error->get_error_code())
				{
					$errors = '';
					$messages = '';

					foreach($wp_error->get_error_codes() as $code)
					{
						$severity = $wp_error->get_error_data($code);

						foreach ($wp_error->get_error_messages($code) as $error)
						{
							if ($severity === 'message')
							{
								$messages .= '	' . $error->get_error_message() . "<br />\n";
							}
							else
							{
								$errors .= '	' . $error->get_error_message() . "<br />\n";
							}
						}
					}

					if ( ! empty($errors))
					{
						$output .= '<p class="form_error">' . apply_filters('login_errors', $errors) . "</p>\n";
					}
					if (! empty($messages))
					{
						$output .= '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
					}
				}
				return $output;
			}

			return false;
		}

		/**
		 * Update Home Email
		 *
		 * Checks that the logged in user can edit the home email field and if they can, updates it.
		 *
		 * @access		public
		 * @since		0.3
		 * @param		$user_id
		 * @param		$email
		 * @return		bool|void
		 */
		public function update_home_email($user_id, $email)
		{
			if ( ! current_user_can('edit_user', $user_id))
			{
				return false;
			}

			update_user_meta($user_id, 'home_email', $email);
		}

		/**
		 * Change Location
		 *
		 * @access		private
		 * @since		0.3
		 * @param		$location
		 * @param		$user_id
		 * @return		void
		 */
		private function change_location($location, $user_id)
		{
			$term = get_term_by('id', (int) $location, 'news-locale');

			$term === false ? wp_die('{"error": "location not found"}') : null;

			update_user_meta($user_id, 'news_locale', (int) $term->term_id);
		}

		/**
		 * Get Newsletter Categories
		 *
		 * This is in a separate method as it's most likely required to be changed again in the future to be slightly
		 * more selective.  Warning, this is a bit dirty, the code is duplicated from the newsletter plugin, but it
		 * made more sense at the time to put it in here to keep the methods for the profile page all in one place.
		 *
		 * @access		private
		 * @since		0.3
		 * @return		array|WP_Error
		 * @todo		Revise this at some point, this code should be in one place, possibly the theme functions.
		 */
		private function get_newsletter_cats()
		{
			$args = array(
				'type' => 'post',
				'child_of' => 0,
				'parent' => '',
				'orderby' => 'name',
				'order' => 'ASC',
				'hide_empty' => false,
				'hierarchical' => true,
				'exclude' => '',
				'include' => '',
				'number' => '',
				'taxonomy' => 'category',
				'pad_counts' => false
			);

			$categories = get_categories($args);

			foreach ($categories as $key => $cat)
			{
				$flag = get_field('include_in_newsletter', $cat);

				if ($flag === 'never')
				{
					unset($categories[$key]);
				}
				if ($flag === 'always')
				{
					$categories[$key]->disabled = true;
				}
			}

			return $categories;
		}

		/**
		 * Get Current Location
		 *
		 * Warning: This is dirty.  This code is duplicated from the dh-intranet theme files, but we wanted to keep this
		 * all in one place to prevent possible errors.
		 *
		 * @access		private
		 * @since		0.3
		 * @return		bool|int
		 * @todo		Consider moving all this code into the theme libraries.  Kept in wtg_security code due to
		 * 				time restraints and trying to keep everything in one place for the profile/user forms.
		 */
		private function get_current_location()
		{
			if (is_user_logged_in())
			{
				$location = (int) get_user_meta(get_current_user_id(), 'news_locale', true);

				if ($location === 0)
				{
					return $this->default_location();
				}

				return $location;
			}
			else
			{
				if (isset($_COOKIE['news_locale']))
				{
					return (int) $_COOKIE['news_locale'];
				}
				elseif (isset($_SESSION['news_locale']))
				{
					return (int) $_SESSION['news_locale'];
				}

				return $this->default_location();
			}
		}

		/**
		 * Default Location
		 *
		 * Warning: This is dirty.  This code is duplicated from the dh-intranet theme files, but we wanted to keep this
		 * all in one place to prevent possible errors if the theme is swapped, etc.
		 *
		 * @access		private
		 * @since		0.3
		 * @return		bool|int
		 * @todo		Consider moving this whole library to the theme, see Get Current Location method above.
		 */
		private function default_location()
		{
			// Go through all the terms
			// Pick the first with a "default" checkbox checked
			$terms = get_terms('news-locale', array('hide_empty' => false));

			foreach ($terms as $term)
			{
				$default = get_field('default', "news-locale_{$term->term_id}");
				if ($default === array('default'))
				{
					return (int)$term->term_id;
				}
			}

			return isset($term->term_id) ? (int)$term->term_id : FALSE;
		}
	}
}
