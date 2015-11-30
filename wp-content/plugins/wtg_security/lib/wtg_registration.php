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

if (! class_exists('wtg_registration'))
{
	/**
	 * Class wtg_register
	 *
	 *
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_registration
	 * @subpackage		registration
	 * @since			0.3
	 */
	class wtg_registration extends wtg_security
	{
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
		}
		
		private function doView()
		{
			
		}
	}
}