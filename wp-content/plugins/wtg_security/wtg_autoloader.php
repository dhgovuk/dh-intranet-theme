<?php
/**
 * WTG
 *
 * @package		wtg_security
 * @author		Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright   (C) 2014 Web Technologies Group Ltd.
 * @link		http://www.wtg.co.uk
 * @since		version 0.1
 */
	
// No ABSPATH?  That's because we're doing an ajax call, set it manually here.
if ( ! defined('ABSPATH') )
{
	define('ABSPATH', dirname(__FILE__) . "/../../../" );
}

// Get the pluggable.php file.
require_once( ABSPATH . "wp-includes/pluggable.php" );

// Check this hasn't already been run (hint: it shouldn't have been!)
if (! function_exists('wtg_wp_autoloader'))
{
	/**
	* Autoloader
	*
	* A short spl autoloader for this plugin.
	*
	* @param	   $name
	* @return	  void
	*/
	function wtg_wp_autoloader($name)
	{
		// Library file autoloader.
		$file = sprintf('%slib/%s.php', dirname(__FILE__) . '/', str_replace('-', '_', strtolower($name)));
		file_exists($file) ? include_once($file) : NULL;
	}

	// Register it up.
	spl_autoload_register('wtg_wp_autoloader');
}