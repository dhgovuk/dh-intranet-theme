<?php
/*
	The MIT License

	Copyright (c) 2011 Stas Sușcov

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

class EBAPI {
	/**
	 * Application Key to access the data
	 */
	var $app_key;

	/**
	 * OAuth access token for API.
	 */
	var $oauth_token;

	/**
	 * User Key to identify
	 */
	var $user_key;

	/**
	 * User email
	 */
	var $user;

	/**
	 * User password
	 */
	var $password;

	/**
	 * API URL to webservice
	 */
	var $api_url;

	/**
	 * Error status
*/
	var $error;

	/**
	 * Default API URL
	 */
	var $default_api_url = "https://www.eventbrite.com/json/";

	/**
	 * Force secured connection over SSL
	 */
	var $secure;

	var $api_v3;

	/**
	 * Cache Folder
	 *
	 * Location of where we want to store our cache.
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis <adam.lewis@wtg.co.uk>
	 * @var			string
	 */
	var $cache_folder = "../cache/";

	var $cache_time = 120;


	/**
	 * Constructor to initialize the object
	 *
	 * @param String $app_key, your Eventbrite application key
	 * @param String $user_key, your Eventbrite user key
	 * @param String $oauth_token, Eventbrite API V3 OAuth token
	 * @param String $user_id, Eventbrite API V3 OAuth token owner id
	 */
	function EBAPI( $app_key = null, $user_key = null, $oauth_token = null, $user_id = null) {
		$this->app_key = $app_key;
		$this->user_key = $user_key;
		$this->oauth_token = $oauth_token;
		$this->setUrl( $this->default_api_url );

		$this->api_v3 = new EBAPI_V3($oauth_token, $user_id);
	}

	/**
	 * Define API URL
	 *
	 * @param String $url, the webservice uri to be used
	 */
	function setUrl( $url ) {
		$this->api_url = parse_url( $url );
		$this->checkSecure();
	}

	/**
	 * Sets user email
	 *
	 * @param String $email, the email adress to be used
	 */
	function setUser( $email ) {
		$this->user = $email;
	}

	/**
	 * Sets user password
	 *
	 * @param String $pass, the password to be used
	 */
	function setPassword( $pass ) {
		$this->password = $pass;
	}

	/**
	 * Toggle secure connection
	 *
	 * @param Boolean $value, true or false
	 */
	function checkSecure( $value = true ) {
		$this->secure = (bool) $value;

		if( !empty( $this->api_url ) && isset( $this->api_url['scheme'] ) )
			if( !$this->secure )
				$this->api_url['scheme'] = 'http';
			else
				$this->api_url['scheme'] = 'https';
	}

	/**
	 * Checks for errors
	 *
	 * @return null on no errors, Mixed data on error
	 */
	function getError() {
		return $this->error;
	}

	/**
	 * Multi Dimensional Array Implode
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis <adam.lewis@wtg.co.uk>
	 * @param		$glue
	 * @param		array		$array
	 * @return		string
	 */
	function multiDimensionalArrayImplode($glue, array $array)
	{
		$returnArray = array();

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$value = $this->multiDimensionalArrayImplode($glue, $value);
			}

			$returnArray[] = "{$glue}{$value}";
		}

		return implode($glue, $returnArray);
	}

	/**
	 * Get Cache File
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis <adam.lewis@wtg.co.uk>
	 * @param		string				$method
	 * @param		array|string		$args
	 * @return		string				Flattened out, unique filename based on arguments
	 */
	function getCacheFileVar($method, $args)
	{
		$args = is_array($args) ? $args : array($args);

		$file = __DIR__ . '/../cache/' . $method . $this->multiDimensionalArrayImplode('_', $args);

		// Double underscore bugfix.
		// multiDimensionalArrayImplode() returns something like
		// '...user_list_events__live' whereas it should be
		// '...user_list_events_live'.
		$file = str_ireplace('events__live', 'events_live', $file);

		return $file;
	}

	/**
	 * Get Cache
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis <adam.lewis@wtg.co.uk>
	 * @param		string				$method
	 * @param		array|string		$args
	 * @return		array|bool			Returns either false, or the decoded json array from the cache file.
	 */
	function getCache($method, $args)
	{
		$file = $this->getCacheFileVar($method, $args);

		if (file_exists($file) && (time() - filemtime($file)) < $this->cache_time)
		{
			return json_decode(file_get_contents($file));
		}

		return false;
	}

	/**
	 * Put Cache
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis
	 * @param		json			$data
	 * @param		string			$method
	 * @param		array|string	$args
	 * @return		void
	 */
	function putCache($data, $method, $args)
	{
		$file = $this->getCacheFileVar($method, $args);

		file_exists($file) ? unlink($file) : null;

		file_put_contents($file, $data);
	}

	/**
	 * Is Get Call
	 *
	 * @access		public
	 * @since		0.2
	 * @author		Adam Lewis <adam.lewis@wtg.co.uk>
	 * @param		string		$method
	 * @return		bool
	 */
	function isGetCall($method)
	{
		return preg_match('/new|update|copy/', $method) ? false : true;
	}

	/**
	 * Dynamic methods handler
	 */
	function __call( $method, $args )
	{
		// Is there a cached version of this request?
		if ($data = $this->getCache($method, $args)) {
			return $data;
		}
		else {
			// Reset error status
			$this->error = null;

			// Parse args
			if( is_array( $args ) )
				$args = reset( $args );

			// API V3 changes to go here.
			$response = $this->api_v3->rewrite_api_call($method, $args, $this->oauth_token);

			if( $response )	{
				// Is this a get call rather than an update or new?
				if ($this->isGetCall($method)) {
					// Set the cache as json, easier to store and decode later that way.
					$this->putCache($response, $method, $args);
				}
			}

			if( isset( $response->error ) )
				$this->error = $response->error;

			// If string and first char '{' then it is JSON, decode it.
			// If not string then it is decoded already.
			if(is_string($response) && $response[0] == '{') {
				$response = json_decode( $response );
			}

			return $response;
		}
	}

	/**
	 * Definitions for dynamic methods
	 *
	 * @link http://developer.eventbrite.com/doc/
	 */
	protected $api_methods = array(
		'discount_new'			=> array( 'event_id', 'code', 'amount_off', 'percent_off', 'tickets', 'quantity_available', 'start_date', 'end_date' ),
		'discount_update'		=> array( 'id', 'code', 'amount_off', 'percent_off', 'tickets', 'quantity_available', 'start_date', 'end_date' ),
		'event_copy'			=> array( 'event_id', 'event_name' ),
		'event_get'				=> array( 'id' ),
		'event_list_attendees'	=> array( 'id', 'count', 'page', 'do_not_display', 'show_full_barcodes' ),
		'event_list_discounts'	=> array( 'id' ),
		'event_new'				=> array( 'title', 'description', 'start_date', 'end_date', 'timezone', 'privacy', 'personalized_url', 'venue_id', 'organizer_id', 'capacity', 'currency', 'status', 'custom_header', 'custom_footer', 'background_color', 'text_color', 'link_color', 'title_text_color', 'box_background_color', 'box_text_color', 'box_border_color', 'box_header_background_color', 'box_header_text_color' ),
		'event_search'			=> array( 'keywords', 'category', 'address', 'city', 'region', 'postal_code', 'country', 'within', 'within_unit', 'latitude', 'longitude', 'date', 'date_created', 'date_modified', 'organizer', 'max', 'count_only', 'sort_by', 'page', 'since_id', 'tracking_link' ),
		'event_update'			=> array( 'event_id', 'title', 'description', 'start_date', 'end_date', 'timezone', 'privacy', 'personalized_url', 'venue_id', 'organizer_id', 'capacity', 'currency', 'status', 'custom_header', 'custom_footer', 'background_color', 'text_color', 'link_color', 'title_text_color', 'box_background_color', 'box_text_color', 'box_border_color', 'box_header_background_color', 'box_header_text_color' ),
		'organizer_list_events'	=> array( 'id' ),
		'organizer_new'			=> array( 'name', 'description' ),
		'organizer_update'		=> array( 'id', 'name', 'description' ),
		'payment_update'		=> array( 'event_id', 'accept_paypal', 'paypal_email', 'accept_google', 'google_merchant_id', 'google_merchant_key', 'accept_check', 'instructions_check', 'accept_cash', 'instructions_cash', 'accept_invoice', 'instructions_invoice' ),
		'ticket_new'			=> array( 'event_id', 'is_donation', 'name', 'description', 'price', 'quantity', 'start_sales', 'end_sales', 'include_fee', 'min', 'max' ),
		'ticket_update'			=> array( 'id', 'is_donation', 'name', 'description', 'price', 'quantity', 'start_sales', 'end_sales', 'include_fee', 'min', 'max', 'hide' ),
		'user_get'				=> array( 'user_id', 'email' ),
		'user_list_events'		=> array( 'user', 'do_not_display', 'event_statuses', 'asc_or_desc' ),
		'user_list_organizers'	=> array( 'user', 'password' ),
		'user_list_tickets'		=> array(),
		'user_list_venues'		=> array( 'user', 'password' ),
		'user_new'				=> array( 'email', 'password' ),
		'user_update'			=> array( 'new_email', 'new_password' ),
		'venue_get'             => array( 'id' ),
		'venue_new'				=> array( 'organizer_id', 'venue', 'adress', 'adress_2', 'city', 'region', 'postal_code', 'country_code' ),
		'venue_update'			=> array( 'id', 'venue', 'adress', 'adress_2', 'city', 'region', 'postal_code', 'country_code' )
	);
}