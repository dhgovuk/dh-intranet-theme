<?php
/**
 * WTG
 *
 * @package			wtg_security
 * @subpackage		passwords
 * @author			Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright		(C) 2014 Web Technologies Group Ltd.
 * @link			http://www.wtg.co.uk
 * @since			version 1.0
 */
/*
Plugin Name: WTG Security
Plugin URI: http://wtg.co.uk
Description: This plugin is intended to provide extra security measures such as IP address blocking, enforced password complexity, removal of sensitive URL's, etc.
Author: Adam Lewis <adam.lewis@wtg.co.uk>
Version: 0.1
Author URI: http://adamlewis.me.uk
*/

define('DH_MAIL_FROM', 'noreply@dh-intranet.co.uk');
define('DH_MAIL_FROM_NAME', 'Department of Health Intranet');

if ( ! defined('DH_CRON_JOB'))
{
	require_once(plugin_dir_path(__FILE__).'/wtg_autoloader.php');

	// Do all the activation hooks.
	register_activation_hook(__FILE__, function()
	{
		// We have to add the action here, it doesn't work from the class.
		add_action('init', wtg_auth::activate());

		// Add in the default data for this plugin.
		wtg_security_options::activate();
	});

	// Do all the deactivation hooks.
	register_deactivation_hook(__FILE__, function()
	{
		wtg_auth::deactivate();
	});

	// Fire up the plugin options if the user is an administrator.
	is_admin() ? new wtg_security_options() : NULL;

	if (! function_exists('is_ip_whitelisted'))
	{
		/**
		 * Is IP whitelisted
		 *
		 * A function is better for templating than a static function, so created this helper function that just calls
		 * wtg_ip_addresses::is_whitelist_ip_address method
		 *
		 * @return bool
		 */
		function is_ip_whitelisted()
		{
			// Return if the IP is whitelisted or not.
			return wtg_ip_addresses::is_whitelist_ip_address();
		}
	}

	// Use the register_hook method of wtg_ip_addressess to block registration out of the IP whitelist
	wtg_auth::hook();

	// Hook in the password strength stuff.
	wtg_passwords::hook();

	// Hook in the restriction work.
	wtg_restrict::hook();

	// Hook in the strip_links library.
	wtg_strip_links::hook();

	// Registration hook.
	wtg_registration::hook();

	// Profile hook.
	wtg_profile::hook();
}
else
{
	add_filter('wp_mail_from', function($email_address)
	{
		return DH_MAIL_FROM;
	});

	add_filter('wp_mail_from_name', function($name)
	{
		return DH_MAIL_FROM_NAME;
	});
}