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

if (! class_exists('wtg_security'))
{
	/**
	 * Class wtg_security
	 *
	 * This class is an base abstract class for the other clasess to inherit and get common data from.  As these packages
	 * should never get too big and complicated, I thought I'd keep it simple for now and haven't employed any
	 * interfaces or complicated patterns.
	 *
	 * @since			0.1
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		ip_security
	 */
	abstract class wtg_security
	{
		/**#@+
		 * Plugin Locations
		 *
		 * @access		protected
		 * @since		0.1
		 * @var			bool|string
		 */
		protected $plugin_location		= FALSE;
		protected $plugin_ur			= FALSE;

		/**
		 * Language
		 *
		 * @access		protected
		 * @since		0.1
		 * @var			array
		 */
		protected $lang					= array();

		/**#@+
		 * Config
		 *
		 * @access		protected
		 * @since		0.1
		 * @var			bool|StdClass
		 */
		protected $config				= FALSE;
		protected $plugin_options		= FALSE;

		/**#@+
		 * Files
		 *
		 * @access		private
		 * @since		0.1
		 * @var			string
		 */
		private $config_file			= 'config/wtg_security.json';
		private $lang_file				= 'lang/wtg_security.json';
		protected $view_folder			= 'views/';
		protected $json_ext				= '.json';

		/**
		 * Class constructor
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function __construct()
		{
			// Set the plugin locations.
			$this->plugin_location	= plugin_dir_path(__DIR__);

			// If this is an ajax request, we don't need the site url, if it isn't we do.
			$this->plugin_url		= function_exists('site_url') ? site_url() . '/wp-content/plugins/wtg_security/' :
				FALSE;

			// Add in the plugin options if this is not an ajax request.
			$this->plugin_options	= function_exists('get_option') ? get_option('wtg_security'): FALSE;

			// Pull in the global configuration and language files and set them as objects.
			$this->config			= $this->get_json_data($this->plugin_location . $this->config_file);
			$this->lang				= $this->get_json_data($this->plugin_location . $this->lang_file);

			// Update the child config and lang, if there is any.
			$this->update_child_config_lang('config');
			$this->update_child_config_lang('lang');

		}

		/**
		 * Get JSON Data
		 *
		 * If the file exists, then decode the JSON contained and return an array.
		 *
		 * @access		private
		 * @since		0.1
		 * @param		string		$file
		 * @return		array
		 */
		private function get_json_data($file)
		{
			if (file_exists($file))
			{
				return (array) json_decode(file_get_contents($file));
			}
			return array();
		}

		/**
		 * Update Child Config Lang
		 *
		 * Adds the config OR language data from the JSON files to $this->lang or $this->config class variable for the
		 * child class that we're in.
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		$type
		 * @return		void
		 */
		private function update_child_config_lang($type)
		{
			$this->$type = array_merge($this->$type,
				$this->get_json_data($this->plugin_location . $type .'/' . get_class($this) . $this->json_ext));
		}

		/**
		 * View
		 *
		 * A simple view function to allow us to call in HTML with an array of variables.
		 *
		 * @access		protected
		 * @since		0.1
		 * @param		$view
		 * @param		array		$data
		 * @return		bool|string
		 */
		protected function view($view, $data = array())
		{
			// Set up the view file varable.
			$view_file = $this->plugin_location . $this->view_folder . $view . '.php';

			// If the file doesn't exist, then bail.
			if (! file_exists($view_file))
			{
				return FALSE;
			}

			// Start an object buffer.
			ob_start();

			// Pull all the variables we need into this object space.
			extract(is_array($data) ? $data : (array) $data);

			// Pull in the file, not "include_once" on purpose so that the same file can be used more than once.
			include($view_file);

			// Get the buffer contents and clean it up. Again, clean the buffer so it can be used again.
			$buffer = ob_get_contents();
			@ob_end_clean();

			// Return the buffer contents.
			return $buffer;
		}

		/**
		 * Ajax View
		 * 
		 * A simple shortcut static method to return the view with the child class langauge files included 
		 * automatically.
		 * 
		 * @access		public
		 * @since		0.1
		 * @param		string		$view
		 * @return		wtg_security::view
		 */
		public static function ajax_view($view)
		{
			$that = new static();
			return $that->view($view, $that->lang);
		}

		/**
		 * Get Modal Data
		 *
		 * A simple static function for the view so that we can keep all the language information in a file.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		array|mixed
		 * @static
		 */
		public static function get_lang_data()
		{
			// Late static binding so that we can use our child information.
			$that = new static();
			return $that->lang;
		}

		/**
		 * Add Get Text Filter
		 *
		 * Rather than writing this out in the methods, I thought I'd abstract this out to something repeatable.
		 *
		 * @access		protected
		 * @since		0.1
		 * @param		string		$from
		 * @param		string		$to
		 * @return		void
		 */
		protected function add_get_text_filter($from, $to)
		{
			// Adds a "gettext" filter in WordPress.
			add_filter('gettext',
				function ($text) use ($from, $to)
				{
					return strpos(trim($text), trim($from)) !== FALSE ? $to : $text;
				}
			);
		}

		/**
		 * Remove HTML wrapper
		 *
		 * A small global function to remove the HTML wrapper that DOMDocument adds by default when saveHTML() is run.
		 * This looks nasty, but PHP doesn't have a flag to turn it off for some reason.
		 *
		 * @access		protected
		 * @since		0.1
		 * @param		string		$string
		 * @return		string
		 */
		protected function remove_html_wrapper($string)
		{
			return preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $string);
		}

		/**
		 * Sanitize text field keep newline
		 *
		 * This might seem like overkill, but it makes sense to keep parsing our input through some kind of santizer
		 * to prevent horrible characters getting in from copy and paste.  WordPress doesn't have a sanitizer that
		 * doesn't strip out the carriage returns, so here is a work around.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		string		$text
		 * @return		string
		 */
		public function sanitize_text_field_keep_newline($text)
		{
			// Random unique token.
			$unique_token		= "--96d8ec97e75bb294177ee4d1f7ffcb65--";

			// Change all the carriage returns for the token.
			$pre_sanitize		= str_replace("\n", $unique_token, $text);

			// Santize.
			$sanitized			= sanitize_text_field($pre_sanitize);

			// Return the string with the carriage returns put back in.
			return str_replace($unique_token, "\n", $sanitized);
		}
	}
}