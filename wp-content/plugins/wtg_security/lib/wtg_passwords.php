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

if (! class_exists('wtg_passwords'))
{
	/**
	 * Class wtg_ip_addresses
	 *
	 * This class will return the ip address of the current session from the $_SERVER variable after it has validated the
	 * returned variable as a correct IP address.
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		passwords
	 * @since			0.1
	 */
	class wtg_passwords extends wtg_security
	{
		/**
		 * Message bag
		 *
		 * @access		protected
		 * @since		0.1
		 * @var			array
		 */
		protected $message_bag = array();

		/**
		 * Hook
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 * @static
		 */
		public static function hook()
		{
			$that = new self();

			// Is this turned on?
			if ($that->config['switch']->wtg_passwords)
			{
				// Set the actions and the filter.
				$that->add_get_text_filter($that->lang['original_hint_start'], $that->lang['hint']);
				add_action('user_profile_update_errors', array($that, 'handle_errors'), 0, 3);
				add_action('validate_password_reset', array($that, 'handle_errors'), 10, 2);
				add_action('registration_errors', array($that, 'handle_errors'), 10, 3);
			}
		}

		/**
		 * Handle Errors
		 *
		 * Uses the WordPress errors
		 * @access		public
		 * @since		0.1
		 * @param		$errors
		 * @return		mixed
		 */
		public function handle_errors(WP_Error $errors)
		{
			// If there are NO errors already set AND the pass1 variable is in the post and the validate_password method
			// does NOT pass.
			if (! $errors->get_error_data("pass") && $_POST['pass1'] && ! $this->validate_password($_POST['pass1']))
			{
				// Add the message bag to the WP_Error object.
				$errors->add('pass', implode('<br /> ', $this->message_bag));
			}

			// Return.
			return $errors;
		}

		/**
		 * Validate password
		 *
		 * Rather than one, hard to read, regular expression, this method calls them one by one so that we can fine tune
		 * the rules in the future.
		 *
		 * The password HAS to be 8 characters or longer and MUST meet 3 of the following 4 criteria:
		 * - at least one upper case character
		 * - at least one lower case character
		 * - at least one numeric character
		 * - at least one special character
		 *
		 * @access		private
		 * @since		0.1
		 * @param		$password
		 * @return		bool
		 */
		private function validate_password($password)
		{
			// Check that the password is equal or more than 8 characters.
			if (strlen(utf8_decode($password)) < 8)
			{
				$this->message_bag[] = $this->lang['length'];
			}

			// Set up a temporary message bag so we can check for 3/4 errors.
			$temp_message_bag = array();

			// Check for one upper case character.
			if (! preg_match('/[A-Z]/', $password))
			{
				$temp_message_bag[] = $this->lang['upper_case'];
			}

			// Check for one lower case character.
			if (! preg_match('/[a-z]/', $password))
			{
				$temp_message_bag[] = $this->lang['lower_case'];
			}

			// Check for a number.
			if (! preg_match('/[0-9]/', $password))
			{
				$temp_message_bag[] = $this->lang['number'];
			}

			// Check for a special character.
			if (! preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password))
			{
				$temp_message_bag[] = $this->lang['special'];
			}

			// Check the temporary message bag to see if it contains only one message, one's okay.
			$this->message_bag = count($temp_message_bag) > 1 ? array_merge($temp_message_bag, $this->message_bag) :
				$this->message_bag;

			// This is a little bit hacky but the client wants a single error message to appear and I don't really want
			// to remove all the code above in case they change their minds again.
			$this->message_bag = empty($this->message_bag) ? $this->message_bag : array($this->lang['not_met_requirements']);

			// If the message bag is empty, then return TRUE, if not return the message bag.
			return empty($this->message_bag) ? TRUE : FALSE;
		}
	}
}