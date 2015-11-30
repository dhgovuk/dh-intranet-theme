<?php
/**
 * WTG
 *
 * @package			wtg_security
 * @author			Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright		(C) 2014 Web Technologies Group Ltd.
 * @link			http://www.wtg.co.uk
 * @since			0.1
 */

if (! class_exists('wtg_auth'))
{
	/**
	 * Class wtg_registration
	 *
	 * This class locks down the registration form so that if the user is NOT in the whitelist IP address range, then
	 * they cannot register, but can still log in.
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		ip_security
	 * @since			0.1
	 */
	class wtg_auth extends wtg_security
	{
		/**
		 * Hook
		 *
		 * Checks that the configuration variable is set and the user is NOT in the whitelisted IP's.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 * @static
		 */
		static public function hook()
		{
			$that = new self();

			// Do the custom text filters.
			$that->do_text_filters();

			// Login via email.
			$that->login_with_email();

			// Change failed login message.
			add_filter('login_errors', array($that, 'login_error_message'));

			// Remove the username field.
			add_action('login_head', array($that, 'remove_username_from_register'));
			add_filter('registration_errors', array($that, 'remove_username_error'), 10, 3);
			add_filter('login_form_register', array($that, 'make_email_username'));
			add_filter('wp_mail', array($that, 'remove_username_from_email'));
			add_filter('login_redirect', array($that, 'login_redirect'), 10, 3);

			// If this is the production box, we need to set the from email address and name.
			if (get_home_url() === 'https://in.dh.gov.uk')
			{
				add_filter('wp_mail_from', function($email_address)
				{
					return 'noreply@dh-intranet.co.uk';
				});

				add_filter('wp_mail_from_name', function($name)
				{
					return 'Department of Health Intranet';
				});
			}

			// Is this turned on.
			if ($that->config['switch']->wtg_ip_addresses_registration)
			{
				// Fix all the links on the site to match.
				$that->fix_wp_auth_links();

				// Add in the rewrites.
				add_action('init', function() use ($that)
				{
					$that->add_rewrite_rules();
				});

				// Set the cookie expiration time.
				$that->set_cookie_time();

				// Restrict the profile update page rules for the email domain.
				// add_action('user_profile_update_errors', array($that, 'restrict_email_domain'), 10, 3);

				// Is the user in the whitelist ip address range, if not then block the registration and remember me.
				if (wtg_ip_addresses::is_whitelist_ip_address())
				{
					// Show the custom registration form.
					add_action('register_form', array($that, 'custom_register'));

					// Restrict the domain of the email to the whitelist.
					add_action('registration_errors', array($that, 'restrict_email_domain'), 10, 3);
				}
				else
				{
					// Change the "registration_blocked" message to give the user a better understanding of why.
					$that->add_get_text_filter($that->lang['registration_blocked_original'],
						$that->lang['registration_blocked']);

					// Add an action to the register form.
					add_action('register_form', function()
					{
						// Redirect the user and exit.
						wp_redirect('wp-login.php?registration=disabled');
						exit();
					});

					// Do the remember me stuff.
					if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'login')))
					{
						add_action('login_form', array($that, 'start_login_form_cache'), 99);
						add_action('login_head', array($that, 'reset_reminder_option'), 99);
					}
				}
			}
		}

		/**
		 * Login Redirect
		 *
		 * If this is not an administrator, then send the user to their profile page.
		 *
		 * @access		public
		 * @since		0.3
		 * @param		$redirect_to
		 * @param		$request
		 * @return		string
		 */
		public function login_redirect($redirect_to, $request)
		{
			return $redirect_to;

			// Sloppy, but I've left the old code here commented out as the customer may change their mind about this
			// one again.
			/*			global $user;
						if (isset($user->roles) && is_array($user->roles))
						{
							if (! in_array('administrator', $user->roles))
							{
								return site_url() . '/profile';
							}
						}
						return $redirect_to;
			*/
		}

		/**
		 * Remove Username From Email
		 *
		 * Changes the subject and removes the "Username" text from the email body.
		 *
		 * @access		public
		 * @since		0.3
		 * @param		$atts
		 * @return		mixed
		 */
		public function remove_username_from_email($atts)
		{
			if (isset($atts['subject']) && substr_count($atts['subject'], 'Your username and password' ) > 0)
			{
				$atts['subject'] = 'Your login details';
				$atts['message'] = preg_replace('/Username/', 'Email', $atts['message']);
			}

			return $atts;
		}

		/**
		 * Remove Username From Register
		 *
		 * Dirty little hack, but there's no way to remove this field from WordPress using PHP, so we're going to do
		 * it with jQuery.
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 */
		public function remove_username_from_register()
		{
			?>
			<style>
				#registerform > p:first-child{
					display:none;
				}
			</style>

			<script type="text/javascript" src="<?php echo site_url('/wp-includes/js/jquery/jquery.js'); ?>"></script>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('#registerform > p:first-child').css('display', 'none');
				});
			</script>
		<?php
		}

		/**
		 * Registration Errors
		 *
		 * Supresses registration errors associated with not having a username.
		 *
		 * @access		public
		 * @since		0.3
		 * @param		WP_Error $wp_error
		 * @param		$sanitized_user_login
		 * @param		$user_email
		 * @return		WP_Error
		 */
		public function remove_username_error($wp_error, $sanitized_user_login, $user_email)
		{
			if(isset($wp_error->errors['empty_username'])){
				unset($wp_error->errors['empty_username']);
			}

			if(isset($wp_error->errors['username_exists'])){
				unset($wp_error->errors['username_exists']);
			}
			return $wp_error;
		}

		/**
		 * Make email username
		 *
		 * Simple method that sets the user_login field to be the email when the user registers.
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 */
		public function make_email_username()
		{
			if(isset($_POST['user_login']) && isset($_POST['user_email']) && !empty($_POST['user_email'])){
				$_POST['user_login'] = $_POST['user_email'];
			}
		}

		/**
		 * Start Login Form Cache
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 */
		public function start_login_form_cache()
		{
			ob_start(array($this, 'process_login_form_cache'));
		}

		/**
		 * Process Login Form Cache
		 *
		 * @access		public
		 * @since		0.3
		 * @return		mixed
		 */
		public function process_login_form_cache($content)
		{
			$content = preg_replace( '/<p class="forgetmenot">(.*)<\/p>/', '', $content);

			return $content;
		}

		/**
		 * Reset reminder option
		 *
		 * @access		public
		 * @since		0.3
		 * @return		void
		 */
		public function reset_reminder_option()
		{
			if (isset($_POST['rememberme']))
			{
				unset($_POST['rememberme']);
			}
		}

		/**
		 * Login with email
		 *
		 * If the username entered is an email, then we log the user in using that by getting their username from their
		 * email address in the database and logging in that way.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		private function login_with_email()
		{
			// Add our own authentication filter for logging in via email.
			add_filter('authenticate', function($user, $username, $password)
			{
				// Is the $username an email address?
				if (is_email($username))
				{
					// Get the user object by the email address.
					$user = get_user_by('email', $username);

					// If get_user_by() is not FALSE, then get the user_login variable.
					if ($user)
					{
						$username = $user->user_login;
					}
				}

				// Authenticate.
				return wp_authenticate_username_password(NULL, $username, $password);
			}, 20, 3);
		}

		/**
		 * Login Error Message
		 *
		 * Check if this is the error that we're looking for and return the language string, otherwise the original
		 * error.
		 *
		 * @access		private
		 * @since		0.2
		 * @param		$error
		 * @return		string
		 */
		public function login_error_message($error)
		{
			return (is_int(strpos($error, 'incorrect')) OR is_int(strpos($error, 'Invalid work'))) ? $this->lang['auth_incorrect'] : $error;
		}

		/**
		 * Do text filters
		 *
		 * Tidies up the auth pages with the language replacements the client wants.
		 *
		 * @access		private
		 * @since		0.1
		 * @return		void
		 * @todo		This could be neater, there is duplicated code on the 'gettext' filters.
		 */
		private function do_text_filters()
		{
			// Replace text where we need to.
			add_filter('gettext', array($this, 'text_filters'));

			// A special replace just for the login page so not to interfere with any other page
			if (preg_match('/login|wp-login\.php/', $_SERVER['REQUEST_URI']))
			{
				// Get the special case replacement array from the lagnuage file to pass to the closure function.
				$username_find_replace = $this->lang['login_username_replace'];

				// Add the filter using the config from above.
				add_filter('gettext', function($text) use ($username_find_replace)
				{
					return str_replace($username_find_replace->find, $username_find_replace->replace, $text);
				});

				// Are we in the ip address range?
				if (! wtg_ip_addresses::is_whitelist_ip_address())
				{
					// Remove the message altogether if outside of IP address range.
					add_action('login_head', function()
					{
						// N.B. I don't like doing it this way, but my research indicated that this is the only way...
						echo '<style>#backtoblog {display:none} #nav a:first-child {display: none}</style>';
					});
				}
			}
		}

		/**
		 * Text Filters
		 *
		 * Put all our general text gettext filters for the auth process in here.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		string
		 */
		public function text_filters($text)
		{
			return str_replace($this->lang['auth_strings_to_replace_find'],
				$this->lang['auth_strings_to_replace_replace'], $text);
		}

		/**
		 * Set Cookie Time
		 *
		 * Simple function to set the cookie expiration time. I've left this as long hand (60 * 60 * 24) so it's obvious
		 * what's going on here.
		 *
		 * @access		private
		 * @since		0.1
		 * @return		void
		 */
		private function set_cookie_time()
		{
			// Get the config variable and convert from days to seconds.
			$expiration = $this->config['remember_me_expiration_days'] * 60 * 60 * 24;

			// Add a filter.  There must be a more elegant way of doing this... Left for now as nearing end of sprint.
			add_filter('auth_cookie_expiration', function () use ($expiration) {
				return $expiration;
			});

		}

		/**
		 * Restrict Email Domain
		 *
		 * Tests if the $user_email is in the domain whitelist, if it is, then we let them register, if not, we set an
		 * error and stop them.
		 *
		 * N.B. I know that $user_login is not used, but as this is a WordPress callback, it needs to be left in so the
		 * method will work.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		$user_login
		 * @param		$user_email
		 * @param		$errors
		 * @return		WP_Error
		 */
		public function restrict_email_domain(WP_Error $errors, $user_login, $user_email)
		{
			// If this is NOT a valid email address OR this is NOT in the domain whitelist, then trigger an error.
			if (
				! filter_var($user_email, FILTER_VALIDATE_EMAIL) ||
				! in_array(array_pop(explode('@', strtolower($user_email))), $this->config['email_domain_whitelist'])
			)
			{
				$errors->add('unauthorized_registration_email', '<strong>' . strtoupper(__('error')) . ':</strong>' .
					$this->lang['registration_email_blocked']);
			}

			return $errors;

		}

		/**
		 * Activate
		 *
		 * @access		public
		 * @since		0.1
		 * return		bool
		 * @static
		 */
		public static function activate()
		{
			$that = new self();

			// Firstly, check if the permalinks are set up, if not, display a little reminder message.
			if (! get_option('permalink_structure'))
			{
				add_action('admin_notices', function()
				{
					echo "<div id='message' class='error'><p>Please Make sure to enable " .
						"<a href='options-permalink.php'>Permalinks</a>.</p></div>";
				});
			}

			// Add the rewrites.
			$that->add_rewrite_rules();

			// Flush the rules.
			flush_rewrite_rules();

			// Return TRUE so that we don't produce any funny messages in WordPress.
			return TRUE;
		}

		/**
		 * Add rewrite rules
		 *
		 * Simply adds in the rewrite rules for clean URL's for authentication.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function add_rewrite_rules()
		{
			add_rewrite_rule('login/?$', 'wp-login.php', 'top' );
			add_rewrite_rule('sign-up/?$', 'wp-login.php?action=register', 'top' );
			add_rewrite_rule('forgot/?$', 'wp-login.php?action=lostpassword', 'top' );
		}

		/**
		 * Deactivate
		 *
		 * @access		public
		 * @since		0.1
		 * return		bool
		 * @static
		 */
		public static function deactivate()
		{
			flush_rewrite_rules();

			return TRUE;
		}

		/**
		 * Fix WP auth links
		 *
		 * Makes some friendly looking URL's for login, register and lost password.
		 *
		 * @access		private
		 * @since		0.1
		 * @return		void
		 */
		private function fix_wp_auth_links()
		{
			// Change the register links around the site to the new one.
			add_filter('register', function($link)
			{
				return str_replace(site_url('wp-login.php?action=register', 'login'),site_url('sign-up', 'login'),
					$link);
			});

			// Change the login url.
			add_filter('login_url', function ($link)
			{
				return $link;
			});

			// Change the lost password url as well while we're in here.
			add_filter('lostpassword_url', function($link) {
				return str_replace('?action=lostpassword','',str_replace(network_site_url('wp-login.php', 'login'),
					site_url('forgot', 'login'), $link));
			});

			// Site URL hack to overwrite register url
			add_filter('site_url', function ($url, $path, $orig_scheme)
			{
				if ($orig_scheme !== 'login')
				{
					return $url;
				}
				if ($path == 'wp-login.php?action=register')
				{
					return site_url('sign-up', 'login');
				}
				return $url;
			}, 10,3);
		}

		/**
		 * Custom Register
		 *
		 * This customizes the registration form.  It's a method on its own because it's highly likely that it will
		 * need to be changed in the future.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function custom_register()
		{
			if (isset($this->plugin_options['registration_message']))
			{
				echo nl2br("<p>{$this->plugin_options['registration_message']}</p>");
			}

		}
	}
}
