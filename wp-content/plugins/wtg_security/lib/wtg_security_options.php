<?php
/**
 * WTG
 *
 * @package		wtg_security
 * @author		Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright	(C) 2014 Web Technologies Group Ltd.
 * @link		http://www.wtg.co.uk
 * @since		0.1
 */

if (! class_exists('wtg_security_options'))
{
	/**
	 * Class wtg_security_options
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		options
	 * @since			0.1
	 */
	class wtg_security_options extends wtg_security
	{
		/**
		 * Class constructor
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function __construct()
		{
			parent::__construct();

			// Add the plugin page to the menu.
			add_action('admin_menu', array($this, 'add_plugin_page'));

			// Get all the current options and set the class variable.
			$this->plugin_options = get_option('wtg_security');

			// Make the page.
			add_action( 'admin_init', array( $this, 'page_init' ) );
		}

		/**
		 * Activate
		 *
		 * Runs at plugin registration and adds in the default options if they don't already exist.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 * @static
		 */
		public static function activate()
		{
			$that = new self();

			if (! get_option('wtg_security'))
			{
				add_option('wtg_security',
					array('registration_message' => $that->lang['default_messages']->registration_message));
			}
		}


		/**
		 * Add Plugin Page
		 *
		 * Adds our page to the settings area.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function add_plugin_page()
		{
			// This page will be under "Settings".
			add_options_page('WTG Security', 'WTG Security', 'manage_options', 'wtg-security',
				array($this, 'settings_page'));
		}

		/**
		 * Settings page
		 *
		 * This is the page itself, not a lot of content to show here at the moment.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function settings_page()
		{
			// Output some HTML for our options page.
			?>
			<div class="wrap">
				<h2>My Settings</h2>
				<form method="post" action="options.php">
					<?php
					// This prints out all hidden setting fields
					settings_fields( 'wtg_security_registration' );
					do_settings_sections( 'wtg-security' );
					submit_button();
					?>
				</form>
			</div>
		<?php
		}

		/**
		 * Page Init
		 *
		 * Sets the page up with the settings that we want to adjust.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function page_init()
		{
			register_setting(
				'wtg_security_registration', // Option group
				'wtg_security', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				'setting_section_id', // ID
				'Registration settings', // Title
				FALSE, // Callback
				'wtg-security' // Page
			);

			add_settings_field(
				'registration_message',
				'Registration message',
				array($this, 'option_text_area'),
				'wtg-security',
				'setting_section_id',
				array('id' => 'registration_message', 'name' => 'registration_message')
			);
		}

		/**
		 * Is set or default
		 *
		 * Checks if the field is set, if it isn't, it looks for a default setting in the config file and uses that
		 * instead.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		$field
		 * @return		void
		 */
		protected function is_set($field)
		{
			// Is the field set in the options and not set to '' (nothing).
			return isset($this->plugin_options[$field]) && $this->plugin_options[$field] !== '' ?
				$this->plugin_options[$field] : NULL;
		}

		/**
		 * Option Text Area
		 *
		 * @access		public
		 * @since		0.1
		 * @param		array		$args
		 * @return		void
		 */
		public function option_text_area($args)
		{
			echo "<textarea id='{$args['id']}' name='wtg_security[${args['name']}]' rows='5' cols='60' >" .
				$this->is_set($args['id']) . "</textarea>";

			if (isset($args['description']))
			{
				echo "<p>{$args['description']}</p>";
			}
		}

		/**
		 * Sanitize
		 *
		 * Cleans up the data before pushing it into the DB.
		 *
		 * N.B. I'm not sure this is the right way of doing this, it would be nicer if there was an automatic way of
		 * declaring the variables and having them checked, but whilst this area is so small, it seems like a waste of
		 * effort.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		string		$input
		 * @return		array
		 */
		public function sanitize($input)
		{
			$new_input = array();

			if(isset($input['registration_message']))
			{
				$new_input['registration_message'] =
					$this->sanitize_text_field_keep_newline($input['registration_message']);
			}

			return $new_input;
		}
	}
}