<?php

class EBAPI_V3 {
	/**
	 * Application Key to access the data
	 */
	private $venue_id;

	private $event_id;

	private $oauth_token;

	private $user_id;

	private $publish_status;

	private $api_url = 'https://www.eventbriteapi.com/v3/';

	/**
	 * Constructor to initialize the object
	 *
	 * @param String $app_key, your Eventbrite application key
	 * @param String $user_key, your Eventbrite user key
	 */
	function __construct( $oauth_token = null, $user_id = null) {
		$this->oauth_token = $oauth_token;
		$this->user_id     = $user_id;
	}

	/**
	 * Main class method. This method takes the old API V1 request
	 * and rewrites it into API V3 request. This was done in order
	 * to keep the existing codebase which is written around API V1.
	 * This method also calls rewrite_api_response(). These two method
	 * use the rest of the private methods.
	 *
	 * @param string $method
	 * @param array $args
	 * @return stdClass API V3 response.
	 */
	public function rewrite_api_call($method = NULL, $args = NULL)
	{
		$url = '';

		switch($method) {
			case 'user_list_organizers':
				$url = $this->api_url . 'users/me/organizers/?token=' . $this->oauth_token;
				break;
			case 'user_list_venues':
				$url = $this->api_url . 'users/' . $this->user_id . '/venues/?token=' . $this->oauth_token;
				break;
			case 'venue_new':
				break;
			case 'event_new':
				break;
			case 'event_update':
				break;
			case 'ticket_new':
				break;
			case 'ticket_update':
				break;
			case 'user_list_events':
				break;
			case 'organizer_new':
				break;
			case 'organizer_update':
				break;
			default:
				return NULL;
				break;
		}

		if($url) {
			$response = file_get_contents($url);
			$response = json_decode($response);
		}

		if(isset($response->error)) {
			return $response;
		} else {
			$response = $this->rewrite_api_response($method, $response, $args);
		}

		// Pagination added in API V3. We don't need it.
		if(isset($response->pagination)) {
			unset($response->pagination);
		}

		return $response;
	}

	/**
	 * The second major method of the class.
	 * Makes API V3 calls and rewrites the responses
	 * to look the same as old API V3 responses.
	 *
	 * @param string $method
	 * @param stdClass $response
	 * @param array $args
	 * @return stdClass rewritten API response
	 */
	private function rewrite_api_response($method = NULL, $response = NULL, $args = NULL)
	{
		$rewritten_response = NULL;

		$publish_flag = FALSE;

		switch($method) {
			case 'user_list_organizers':
				$rewritten_response = $this->rewrite_user_organizers($response);
				break;

			case 'user_list_venues':
				$rewritten_response = $this->rewrite_user_venues($response);
				break;

			case 'venue_new':
				$venue = $this->add_new_venue($args);
				if($venue->id) $this->venue_id = $venue->id;
				$rewritten_response = $venue;
				break;

			case 'event_new':
				$args['venue_id'] = $this->venue_id;
				$rewritten_response = $this->add_new_event($args);
				$this->event_id = $rewritten_response->id;
				break;

			case 'event_update':
				$rewritten_response = $this->event_update($args);
				$publish_flag = TRUE;
				break;

			case 'ticket_new':
				$rewritten_response = $this->add_new_ticket_class($args);
				$publish_flag = TRUE;
				break;

			case 'ticket_update':
				$rewritten_response = $this->update_ticket_class($args);
				break;

			case 'user_list_events':
				$rewritten_response = $this->get_user_events();
				break;

			case 'organizer_new':
				$rewritten_response = $this->organizer_new($args);
				break;

			case 'organizer_update':
				$rewritten_response = $this->organizer_update($args);
				break;

			default:
				break;
		}

		if($publish_flag && $this->publish_status == 'publish') {
			$this->event_publish($args['event_id']);
		}

		return $rewritten_response;
	}

	/**
	 * Add new event.
	 *
	 * @param array $data
	 * @return stdClass API result
	 */
	private function add_new_event($data = array())
	{
		$url = $this->api_url . 'events/?token=' . $this->oauth_token;

		if(in_array($data['timezone'], timezone_identifiers_list())) {
			date_default_timezone_set($data['timezone']);
			$timezone = $data['timezone'];
		} else {
			date_default_timezone_set('Europe/London');
			$timezone = 'Europe/London';
		}

		$event_array = array(
			'event.name.html'        => $data['title'],
			'event.description.html' => $data['description'],
			'event.organizer_id'     => $data['organizer_id'],
			'event.start.utc'        => $this->make_date_utc($data['start_date']),
			'event.end.utc'          => $this->make_date_utc($data['end_date']),
			'event.start.timezone'   => $timezone,
			'event.end.timezone'     => $timezone,
			'event.currency'         => $data['currency'],
			'event.venue_id'         => $data['venue_id'],
			'event.listed'           => 'false'
		);

		$result = $this->api_call_post_curl($url, $event_array);

		if($_POST['post_status'] == 'publish') {
			$this->publish_status = 'publish';
		}

		return $result;
	}

	/**
	 * Update an existing event.
	 *
	 * @param array $data
	 * @return stdClass API response
	 */
	private function event_update($data = array())
	{
		$event_id = $data['event_id'];

		$url = $this->api_url . 'events/' . $event_id . '/?token=' . $this->oauth_token;

		if(in_array($data['timezone'], timezone_identifiers_list())) {
			date_default_timezone_set($data['timezone']);
			$timezone = $data['timezone'];
		} else {
			date_default_timezone_set('Europe/London');
			$timezone = 'Europe/London';
		}

		$event_array = array(
			'event.name.html'        => $data['title'],
			'event.description.html' => $data['description'],
			'event.organizer_id'     => $data['organizer_id'],
			'event.start.utc'        => $this->make_date_utc($data['start_date']),
			'event.end.utc'          => $this->make_date_utc($data['end_date']),
			'event.start.timezone'   => $timezone,
			'event.end.timezone'     => $timezone,
			'event.currency'         => $data['currency'],
			'event.venue_id'         => $data['venue_id'],
		);

		$result = $this->api_call_post_curl($url, $event_array);

		if($_POST['post_status'] == 'publish') {
			$this->event_publish($event_id);
		} elseif($_POST['post_status'] == 'draft' || $_POST['post_status'] == 'pending') {
			$this->event_unpublish($event_id);
		}

		return $result;
	}

	/**
	 * Add a new ticket class.
	 *
	 * @param array $data
	 * @return stdClass API response.
	 */
	private function add_new_ticket_class($data = array())
	{
		$event_id = $data['event_id'];

		$url = $this->api_url . 'events/' . $event_id . '/ticket_classes/?token=' . $this->oauth_token;

		// Convert 19.99 into 1999.
		$price = str_ireplace('.', '', $data['price']);

		$ticket_array = array(
			'ticket_class.name' 			 => $data['name'],
			'ticket_class.description' 		 => $data['description'],
			'ticket_class.quantity_total' 	 => $data['quantity'],
			'ticket_class.cost.currency' 	 => 'GBP',
			'ticket_class.cost.value' 		 => $price,
			'ticket_class.donation' 		 => '',
			'ticket_class.include_fee' 		 => $data['include_fee'],
			'ticket_class.split_fee' 		 => '',
			'ticket_class.hide_description'  => '',
			'ticket_class.sales_start' 		 => $this->make_date_utc($data['start_sales']),
			'ticket_class.sales_end' 		 => $this->make_date_utc($data['end_sales']),
			'ticket_class.sales_start_after' => '',
			'ticket_class.minimum_quantity'  => $data['min'],
			'ticket_class.maximum_quantity'  => $data['max'],
			'ticket_class.auto_hide' 		 => '',
			'ticket_class.auto_hide_before'  => '',
			'ticket_class.auto_hide_after' 	 => '',
			'ticket_class.hidden' 			 => ''
		);

		if($data['price'] == 0) {
			$ticket_array['ticket_class.free'] = 1;
		}

		$result = $this->api_call_post_curl($url, $ticket_array);

		return $result;
	}

	/**
	 * Update an existing ticket class.
	 *
	 * @param array $data
	 * @return stdClass API response
	 */
	private function update_ticket_class($data = array())
	{
		$event_id = $data['event_id'];
		$ticket_class_id = $data['id'];

		$url = $this->api_url . 'events/' . $event_id . '/ticket_classes/' . $ticket_class_id . '/?token=' . $this->oauth_token;

		$ticket_array = array(
			'ticket_class.name' 			 => $data['name'],
			'ticket_class.description' 		 => $data['description'],
			'ticket_class.quantity_total' 	 => $data['quantity'],
			'ticket_class.cost.currency' 	 => 'GBP',
			'ticket_class.cost.value' 		 => $data['price'],
			'ticket_class.donation' 		 => '',
			'ticket_class.include_fee' 		 => '',
			'ticket_class.split_fee' 		 => '',
			'ticket_class.hide_description'  => '',
			'ticket_class.sales_start' 		 => $this->make_date_utc($data['start_sales']),
			'ticket_class.sales_end' 		 => $this->make_date_utc($data['end_sales']),
			'ticket_class.sales_start_after' => '',
			'ticket_class.minimum_quantity'  => $data['min'],
			'ticket_class.maximum_quantity'  => $data['max'],
			'ticket_class.auto_hide' 		 => '',
			'ticket_class.auto_hide_before'  => '',
			'ticket_class.auto_hide_after' 	 => '',
			'ticket_class.hidden' 			 => ''
		);

		if($data['price'] == 0) {
			$ticket_array['ticket_class.free'] = TRUE;
		} else {
			$ticket_array['ticket_class.free'] = FALSE;
		}

		$result = $this->api_call_post_curl($url, $ticket_array);

		return $result;
	}

	/**
	 * Set an existing event status to 'published'.
	 *
	 * @param int $event_id
	 * @return stdClass API response
	 */
	private function event_publish($event_id)
	{
		$url = $this->api_url . 'events/' . $event_id . '/publish/?token=' . $this->oauth_token;

		$result = $this->api_call_post_curl($url, array());

		return $result;
	}

	/**
	 * Set an existing event status to 'unpublished'. Accessible from outside the class
	 * when deleting an event (eventbrite.class.php->save()).
	 *
	 * @param int $event_id
	 * @param string $oauth_token
	 * @return stdClass API response
	 */
	public function event_unpublish($event_id = '', $oauth_token = '')
	{
		if( ! $oauth_token) {
			$oauth_token = $this->oauth_token;
		}

		$url = $this->api_url . 'events/' . $event_id . '/unpublish/?token=' . $oauth_token;

		$result = $this->api_call_post_curl($url, array());

		return $result;
	}

	/**
	 * Add a new venue.
	 *
	 * @param array $data
	 * @return stdClass API response
	 */
	private function add_new_venue($data = array())
	{
		$url  = $this->api_url . 'venues/?token=' . $this->oauth_token;

		$address_array = array (
			'venue.name'				  => $data['venue'],
			'venue.address.address_1'	  => $data['adress'],
			'venue.address.address_2'	  => '',
			'venue.address.city'		  => $data['city'],
			'venue.address.region'	  	  => $data['region'],
			'venue.address.postal_code'   => $data['postal_code'],
			'venue.address.country'	      => $data['country_code'],
			'venue.address.latitude'	  => '0',
			'venue.address.longitude'	  => '0'
		);

		$result = $this->api_call_post_curl($url, $address_array);

		$result = json_decode($result);

		return $result;
	}

	/**
	 * Get user owned events.
	 *
	 * @return stdClass API response
	 */
	private function get_user_events()
	{
		$url  = $this->api_url . 'users/' . $this->user_id . '/owned_events/?token=' . $this->oauth_token . '&status=live';

		$result = file_get_contents($url);

		return $result;
	}

	/**
	 * Add a new organizer.
	 *
	 * @param array $data
	 * @return stdClass API response
	 */
	private function organizer_new($data = array())
	{
		$url = $this->api_url . 'organizers/?token=' . $this->oauth_token;

		$organizer_array['organizer.name']             = $data['name'];
		$organizer_array['organizer.description.html'] = $data['description'];

		$result = $this->api_call_post_curl($url, $organizer_array);

		return $result;
	}

	/**
	 * Update an existing organizer.
	 *
	 * @param array $data
	 * @return stdClass API response
	 */
	private function organizer_update($data = array())
	{
		$url = $this->api_url . 'organizers/' . $data['id'] . '/?token=' . $this->oauth_token;

		if($data['name'])

		$organizer_array['organizer.name']             = $data['name'];
		$organizer_array['organizer.description.html'] = $data['description'];

		$result = $this->api_call_post_curl($url, $organizer_array);

		return $result;
	}

	/**
	 * Convert a date into UTC formatted date.
	 *
	 * @param string $datetime
	 * @return string UTC date
	 */
	private function make_date_utc($datetime = '')
	{
		$datetime_utc = '';

		if($datetime) {
			$datetime_utc = gmdate('Y-m-d', strtotime($datetime))
					. 'T' . gmdate('H:i:s', strtotime($datetime)) . 'Z';
		}

		return $datetime_utc;
	}

	/**
	 * Rewrite user organizer API call response.
	 *
	 * @param stdClass $api_response
	 * @return stdClass rewritten response
	 */
	private function rewrite_user_organizers(stdClass $api_response = NULL)
	{
		$rewritten_respone = new stdClass();

		$rewritten_respone->organizers = array();

		if($api_response) {
			foreach($api_response->organizers as $key => $organizer) {
				$rewritten_respone->organizers[$key]->organizer->url              = $organizer->url;
				$rewritten_respone->organizers[$key]->organizer->description      = $organizer->description->text;
				$rewritten_respone->organizers[$key]->organizer->long_description = '';
				$rewritten_respone->organizers[$key]->organizer->id               = $organizer->id;
				$rewritten_respone->organizers[$key]->organizer->name             = $organizer->name;
			}
		}

		return $rewritten_respone;
	}

	/**
	 * Rewrite user venue API call response.
	 *
	 * @param stdClass $api_response
	 * @return stdClass rewritten response
	 */
	private function rewrite_user_venues(stdClass $api_response = NULL)
	{
		$rewritten_response = new stdClass();

		$rewritten_response->venues = array();

		if($api_response) {
			foreach($api_response->venues as $key => $venue) {
				$rewritten_response->venues[$key]->venue->city         = $venue->address->city;
				$rewritten_response->venues[$key]->venue->name         = $venue->name;
				$rewritten_response->venues[$key]->venue->country      = $venue->country;
				$rewritten_response->venues[$key]->venue->region       = $venue->address->region;
				$rewritten_response->venues[$key]->venue->latitude     = $venue->latitude;
				$rewritten_response->venues[$key]->venue->longitude    = $venue->longitude;
				$rewritten_response->venues[$key]->venue->postal_code  = $venue->address->postal_code;
				$rewritten_response->venues[$key]->venue->address_2    = $venue->address->address_2;
				$rewritten_response->venues[$key]->venue->address      = $venue->address->address_1;
				$rewritten_response->venues[$key]->venue->country_code = $venue->address->country;
				$rewritten_response->venues[$key]->venue->id           = $venue->id;
			}
		}

		return $rewritten_response;
	}

	/**
	 * Make a POST method API call via cURL.
	 *
	 * @param string $url
	 * @param array $data_array
	 * @return string HTTP response
	 */
	private function api_call_post_curl($url = '', $data_array = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_array));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}