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

if (! class_exists('wtg_ip_addresses'))
{
	/**
	 * Class wtg_ip_addresses
	 *
	 * This class will return the ip address of the current session from the $_SERVER variable after it has validated the
	 * returned variable as a correct IP address.
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @package			wtg_security
	 * @subpackage		ip_security
	 * @since			0.1
	 */
	class wtg_ip_addresses extends wtg_security
	{
		/**
		 * IP Adresss
		 *
		 * @access		protected
		 * @since		0.1
		 * @var			bool
		 */
		protected $ip_address		= FALSE;

		/**
		 * Class constructor
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 */
		public function __construct()
		{
			// Call parent.
			parent::__construct();

			// Ip address we are going to get from the REMOTE_ADDR $_SERVER array
			$this->ip_address		= $this->get_real_ip();
		}

		/**
		 * Get Real IP
		 *
		 * This function deals with more complex retrieval of IP Addresses.
		 *
		 * @access		private
		 * @since		0.1
		 * @return		bool
		 */
		private function get_real_ip()
		{
			// Go through all possible IP address $_SERVER values in order and see if they have a value.
			foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
						 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
			{
				// Does that key exist?
				if (array_key_exists($key, $_SERVER))
				{
					// There may be more than one value here, so go through them one by one and see if one is valid.
					foreach (explode(',', $_SERVER[$key]) as $ip)
					{
						if ($this->validate_ip_address($ip))
						{
							return $ip;
						}
					}
				}
			}

			// Our advanced checks haven't returned good, so let's just try and return the standard value.
			return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : FALSE;
		}

		/**
		 * Is whitelist ip_address
		 *
		 * Can be called with an IP address or without.  If no IP address is given, then we go and get it from the
		 * $_SERVER array.  Checks that the IP address is valid to prevent spoofing or malicious requests and then
		 * returns TRUE or FALSE depending on whether the ip address is in the whitelist file or not.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		bool		$ip_address
		 * @return		bool
		 * @static
		 */
		static public function is_whitelist_ip_address($ip_address = FALSE)
		{
			$that = new self();

			$ip_address = $ip_address ? $ip_address : $that->get_ip_address();

			if ($that->validate_ip_address($ip_address))
			{
				return $that->is_ip_in_whitelist($ip_address);
			}

			// Return FALSE if we get this far.
			return FALSE;
		}

		/**
		 * Check IP address in whitelist
		 *
		 * This goes through the whitelist ip's and checks if there is a CIDR on the end.  If there is, then we do a bit
		 * of extra checking.
		 *
		 * @access		private
		 * @since		0.1
		 * @param		string		$ip
		 * @return		bool
		 */
		private function is_ip_in_whitelist($ip)
		{
			// Go through the configuration variables.
			foreach ($this->config['ip_whitelist'] as $white_ip)
			{
				// If the string contains a slash, then it has a shorthand subnet to check as well.
				if (strpos($white_ip, '/'))
				{
					// Use our cidr match function to check the ip against the range.
					if ($this->cidr_match($ip, $white_ip))
					{
						return TRUE;
					}
				}
				else
				{
					// Standard IP, see if it matches.
					if ($ip === $white_ip)
					{
						return TRUE;
					}
				}
			}

			// No match, return FALSE.
			return FALSE;
		}

		/**
		 * CIDR Match
		 *
		 * Quite a complicated method, but this is the most efficient way of doing it on PHP 5.3 for 64/32 bit systems.
		 * Splits out the IP address and range into
		 *
		 * @access		private
		 * @since		0.1
		 * @param		string		$ip				Standard IP address such as 121.27.92.11
		 * @param		string		$range			IP address with shorthand subnet such as 234.22.17.2/24
		 * @return		bool
		 */
		private function cidr_match($ip, $range)
		{
			// Get the $net and $mask variables from the $range.
			list ($subnet, $bits)	= explode("/", $range);

			// Expand out the $net and $ip using ip2long().
			$ip						= ip2long($ip);
			$subnet					= ip2long($subnet);

			// Get the mask.
			$mask					= ~((1 << (32 - $bits)) - 1);

			// In case the supplied subnet wasn't correctly aligned.
			$subnet &= $mask;

			// Return if (bits are set in the $ip and $mask) and are boolean equal to the $subnet.
			return ($ip & $mask) == $subnet;
		}

		/**
		 * Get IP Address
		 *
		 * Validates the IP address and returns it if it is valid.
		 *
		 * @access		public
		 * @since		0.1
		 * @return		string|bool
		 */
		public function get_ip_address()
		{
			if ($this->validate_ip_address($this->ip_address))
			{
				return $this->ip_address;
			}

			return FALSE;
		}

		/**
		 * Validate IP address
		 *
		 * Works out if the IP address is ipv6 or ipv4 and runs the relevant validator. Left as a public function so that
		 * it can be used to validate an IP outside of $this->get_ip_address() method in the future.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		bool		$ip_address
		 * @return		bool
		 */
		public function validate_ip_address($ip_address = FALSE)
		{
			$ip_address = $ip_address ? $ip_address : $this->ip_address;

			if (strpos($ip_address, ':'))
			{
				return $this->valid_ipv6($ip_address);
			}
			elseif (strpos($ip_address, '.'))
			{
				return $this->valid_ipv4($ip_address);
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Valid IPV4
		 *
		 * Checks that the protected class variable SELF::ip_address has 4 segments, does not start with a '0' and that
		 * each segment is between 0-255.  Returns false if not.
		 *
		 * @access		protected
		 * @since		0.1
		 * @return		bool
		 */
		protected function valid_ipv4($ip_address = FALSE)
		{
			$ip_address = $ip_address ? $ip_address : $this->ip_address;

			$ip_segments = explode('.', $ip_address);

			// Always 4 segments needed
			if (count($ip_segments) !== 4)
			{
				return FALSE;
			}

			// IP can not start with 0
			if ($ip_segments[0][0] == '0')
			{
				return FALSE;
			}

			// Check each segment
			foreach ($ip_segments as $segment)
			{
				// IP segments must be digits and can not be
				// longer than 3 digits or greater then 255
				if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
				{
					return FALSE;
				}
			}

			return TRUE;
		}

		/**
		 * Valid IPV6
		 *
		 * Splits the protected class variable SELF::ip_address into chunks and checks the following:
		 * - The first or last "chunk" is not a colon
		 * - If the ip address is IPV4-mapped then check the  IPV4 address is valid using SELF::valid_ipv4, if it is
		 *	  then reduce the $groups variable by one.
		 * - Checks that there are the right amount of groups
		 * - Checks that the separator is not too long
		 * - Figures out if the separator is collapsed and if there are multiple collapsed separators then return FALSE
		 * - Checks that all the chunks contain alpha numeric strings, if not, then return FALSE
		 *
		 * @access		protected
		 * @since		0.1
		 * @return		bool
		 */
		protected function valid_ipv6($ip_address = FALSE)
		{
			$ip_address = $ip_address ? $ip_address : $this->ip_address;

			// 8 groups, separated by :
			// 0-ffff per group
			// one set of consecutive 0 groups can be collapsed to ::

			$groups = 8;
			$collapsed = FALSE;

			$chunks = array_filter(
				preg_split('/(:{1,2})/', $ip_address, NULL, PREG_SPLIT_DELIM_CAPTURE)
			);

			// Rule out easy nonsense
			if (current($chunks) == ':' OR end($chunks) == ':')
			{
				return FALSE;
			}

			// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
			if (strpos(end($chunks), '.') !== FALSE)
			{
				$ipv4 = array_pop($chunks);

				if ( ! $this->valid_ipv4($ipv4))
				{
					return FALSE;
				}

				$groups--;
			}

			while ($seg = array_pop($chunks))
			{
				if ($seg[0] == ':')
				{
					if (--$groups == 0)
					{
						return FALSE;	// too many groups
					}

					if (strlen($seg) > 2)
					{
						return FALSE;	// long separator
					}

					if ($seg == '::')
					{
						if ($collapsed)
						{
							return FALSE;	// multiple collapsed
						}

						$collapsed = TRUE;
					}
				}
				elseif (preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4)
				{
					return FALSE; // invalid segment
				}
			}

			return $collapsed OR $groups == 1;
		}
	}
}