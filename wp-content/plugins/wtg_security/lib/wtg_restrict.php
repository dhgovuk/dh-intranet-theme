<?php
/**
 * WTG
 *
 * @package			wtg_security
 * @author	 		Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright		(C) 2014 Web Technologies Group Ltd.
 * @link			http://www.wtg.co.uk
 * @since			0.1
 */

if (! class_exists('wtg_restrict'))
{
	/**
	 * Class wtg_restrict
	 *
	 * This Class restricts visibility of the site to logged in users only when the request is made outside of the safe
	 * IP address range.
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		ip_security
	 * @since			0.1
	 */
	class wtg_restrict extends wtg_security
	{
		/**
		 * Hook
		 *
		 * Hooks up
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 * @static
		 */
		public static function hook()
		{
			$that = new self();

			if ($that->config['switch']->wtg_restrict)
			{
				add_action('init', array($that, 'check'));
			}
		}

		/**
		 * Check
		 *
		 * If the user is NOT logged in and their IP address is NOT in the whitelist and the user is NOT on the login
		 * page, then run the redirect to login method.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function check()
		{
			// Is the user NOT logged in, their IP address NOT in the whitelist and they are NOT on the login page.
			if (! is_user_logged_in() && ! wtg_ip_addresses::is_whitelist_ip_address() &&
				! $this->on_login_register_page())
			{
				// Send them to the log in page.
				$this->redirect_to_login();
			}

            // if user is logged in but IP is not in whitelist then don't server cached pages.
            if (is_user_logged_in() && ! wtg_ip_addresses::is_whitelist_ip_address())
            {
                define('DONOTCACHEPAGE', true);
                define('DONOTCACHEDB', true);
                define('DONOTMINIFY', true);
                define('DONOTCDN', true);
                define('DONOTCACHEOBJECT', true);
            }
            else
            {
                define('DONOTCACHEPAGE', false);
                define('DONOTCACHEDB', false);
                define('DONOTMINIFY', false);
                define('DONOTCDN', false);
                define('DONOTCACHEOBJECT', false);
            }
		}

		/**
		 * On login page
		 *
		 * Doesn't really need a description for this one.
		 *
		 * @access		private
		 * @since		0.1
		 * @return		bool
		 */
		private function on_login_register_page()
		{
			return preg_match('/login|wp-login\.php|wp-admin|sign-up|register|forgot/', $_SERVER['REQUEST_URI']);
		}

		/**
		 * Redirect to login
		 *
		 * @access		private
		 * @since		0.1
		 * @return		void
		 */
		private function redirect_to_login()
		{
			// Is the status_header function available?  Then use it.
			function_exists('status_header') ? status_header(302) : NULL;

			// Do the headers and exit.
			header("HTTP/1.1 302 Temporary Redirect");
			header("Location: /wp-login.php" . (isset($_GET['redirect_to']) ? NULL :
				'?redirect_to=' . urlencode($_SERVER['REQUEST_URI'])));
			exit();
		}
	}
}