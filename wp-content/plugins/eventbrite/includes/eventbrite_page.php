<?php
/**
 * WTG
 *
 * @package			eventbrite
 * @author			Adam Lewis <dadam.lewis@wtg.co.uk>
 * @copyright		(C) 2014 Web Technologies Group Ltd.
 * @link			http://www.wtg.co.uk
 * @since			0.2
 */

/**
 * Class EBP
 *
 * Deals with integrating into the API to show eventbrite details on the site.  There is some reworking to do here if we
 * get the time, however, we are running low on effort for this sprint and it's working well as it is.  Note todo's in
 * the file if there is time later.
 *
 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
 * @package			eventbrite
 * @subpackage		content
 * @since			0.1
 */
class EBP
{
	protected $api			= false;
	protected $options		= false;
	protected $list			= false;

	/**
	 * Class Constructor
	 *
	 * Sets up the global information that the class requires.
	 *
	 * @access		public
	 * @since		0.2
	 * @return		void
	 */
	public function __construct()
	{
		$this->options		= EBO::get_options();

		$this->api			= new EBAPI( $this->options['eventbrite_app_key'], $this->options['eventbrite_user_key'], $this->options['eventbrite_oauth_token'], $this->options['eventbrite_user_id'] );
	}

	/**
	 * Init
	 *
	 * Called in from the main plugin file to set up the eventlist on the event page.
	 *
	 * @access		public
	 * @static
	 * @since		0.2
	 * @return		void
	 */
	static public function init()
	{
		$that = new self;
		add_shortcode('eventbrite_event_list', array($that, 'events_page'));
	}

	/**
	 * Set List
	 *
	 * A method to set the class variable list when we want to rather than in the constructor as this was causing too
	 * many API calls to eventbrite.
	 *
	 * @access		protected
	 * @since		0.2
	 * @return		void
	 */
	protected function set_list()
	{
		$this->list			= $this->api->user_list_events(array('event_statuses' => 'live,started'));
	}

	/**
	 * Widget
	 *
	 * Simple function to get the data from eventbrite api for the widget on the home page.
	 *
	 * @access		public
	 * @static
	 * @param		int			$limit
	 * @return		array
	 * @todo		Doing a very similar thing to self::events_page.  Extract the recursion out of these methods.
	 */
	static public function widget($limit = 4)
	{
		$that = new self;

		$that->set_list();

		$return_list = array();
		$count = 0;

		foreach ($that->list->events as $event)
		{
			$return_list[] = $that->create_event_vars($event, 30);
			$count++;
			if ($count === $limit)
			{
				return $return_list;
			}
		}

		return $return_list;
	}

	/**
	 * Create Event Vars
	 *
	 * Combines both the WP data and the data from the API for use within the events page, widget or the single event
	 * page.
	 *
	 * @access		protected
	 * @since		0.2
	 * @param		$event
	 * @param		int			$desc_limit
	 * @return		array
	 * @todo		Why are we using both WP data and API data here?  Refactor this method and it's dependencies to just
	 * 				one or the other (probably WP data only) and either double check with the API or don't use it at
	 * 				all.
	 */
	protected function create_event_vars($event, $desc_limit = 100)
	{
		$post = $this->get_post_by_event_id($event->id);

		return array(
			'title'			=> $event->name->text,
			'start_date'	=> $event->start->local,
			'end_date'		=> $event->end->local,
			'venue'			=> isset($event->venue) ? $event->venue->name : false,
			'wp_link'		=> $post->guid,
			'register_link'	=> $event->url,
			'description'	=> wp_trim_words($event->description->text, $desc_limit, "&hellip;<a href='{$post->guid}' title='See more about {$event->name->text}'> more </a>"),
			'address'		=> implode(', ',
				array(
					isset($event->venue->address->address_1) ? $event->venue->address->address_1 : false,
					isset($event->venue->address->postal_code) ? $event->venue->address->postal_code : false
				)
			)
		);
	}

	/**
	 * Create List Item
	 *
	 * A simple view method to get the "event_list_item" template and populate it with all the data in the object
	 * buffer and then return it.
	 *
	 * @access		protected
	 * @since		0.2
	 * @param		$event
	 * @return		string
	 * @todo		A little messy, this should be abstracted out into a separate method and dealt with a little more
	 * 				neatly.
	 */
	protected function create_list_item($event)
	{
		// Pull this into the symbol table for use in the include.
		$post = $this->get_post_by_event_id($event->id);

		// Start an object buffer.
		ob_start();

		// Pull all the variables we need into the symbol table.
		extract($this->create_event_vars($event));

		// Pull in the file, not "include_once" on purpose so that the same file can be used more than once.
		include('templates/event_list_item.php');

		// Get the buffer contents and clean it up. Again, clean the buffer so it can be used again.
		$buffer = ob_get_contents();

		@ob_end_clean();

		return $buffer;
	}

	/**
	 * Events Page
	 *
	 * Loops through $this->list->events and creates the HTML to return.
	 *
	 * @access		public
	 * @since		0.2
	 * @return		string
	 * @todo		See todo on self::widget, this shouldn't be two methods.
	 */
	public function events_page()
	{
		$this->set_list();
		
		$html = '';
		$category_name = '';

		// Check if the plugin is loaded and we have a specific
		// category request.
		if(class_exists('Event_Categories') && isset($_GET['cat']))
		{
			if($_GET['cat'] == 'uncategorized')
			{
				$category_id = $_GET['cat'];
			}
			else
			{
				// Sanitize category ID into int.
				$category_id = (int) $_GET['cat'];
			}

			// Get category ids and their event ids.
			$categories_events = Event_Categories::get_categories_and_events();

			// Get category ids and their names.
			$category_names    = Event_Categories::get_all_categories();

			// Check if the requested category exists.
			if(isset($category_names[$category_id]))
			{
				$category_name = $category_names[$category_id];
			}

			$available_events = array();

			if(isset($categories_events[$category_id]))
			{
				foreach($categories_events[$category_id] as $post_id)
				{
					$post_meta = get_post_meta($post_id);

					if( ! empty($post_meta) && isset($post_meta['event_id'][0]))
					{
						$available_events[] = $post_meta['event_id'][0];
					}
				}
			}
		}

		// Hide the original page title. We might want to override it
		// with something like 'Upcoming events in CategoryA'.
		$html .= '<style>#main-content article header {display:none}#main-content article header.header-override {display:inline;}</style>';

		// Reassemble the page title.
		if( ! empty($category_name))
		{
			$html .= '<header class="header-override"><h1 class="last-child">' . $category_name . '</h1></header>';
			$html .= '<br>';
		}
		else
		{
			$html .= '<header class="header-override"><h1 class="last-child">Upcoming events</h1></header>';
			$html .= '<br>';
		}

		foreach($this->list->events as $event)
		{
			// Check if we are getting just one category.
			if(isset($available_events))
			{
				if(in_array($event->id, $available_events))
				{
					$html .= $this->create_list_item($event);
				}
			}
			// Or if we are getting all events.
			else
			{
				$html .= $this->create_list_item($event);
			}
		}

		return $html;
	}

	/**
	 * Get Post By Event Id
	 *
	 * @access		protected
	 * @since		0.2
	 * @param		$id
	 * @return		mixed
	 */
	protected function get_post_by_event_id($id)
	{
		return reset(
			get_posts(
				array(
					'meta_query' => array(
						array(
							'key'		=> 'event_id',
							'value'		=> $id
						)
					),
					'post_type'		=> 'event',
					'post_per_page'	=> -1
				)
			)
		);
	}
}