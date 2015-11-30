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

if (! class_exists('wtg_strip_links'))
{
	/**
	 * Class WTG Strip Links
	 *
	 * This class takes the output of any page within WordPress and strips out any anchor tags that are in the config
	 * file and replaces them with a nice modal window.
	 *
	 * @author			Adam Lewis <adam.lewis@wtg.co.uk>
	 * @since			0.1
	 * @package			wtg_security
	 * @subpackage		strip_links
	 */
	class wtg_strip_links extends wtg_security
	{
		/**#@+
		 * Class variables
		 *
		 * Just so we know where things are, not in a config file on purpose as these shouldn't ever need to change.
		 * Also, we need these to be available in the links produced so that they degrade when there isn't javascript
		 * enabled.
		 *
		 * @access		private
		 * @since		0.1
		 * @var			string
		 */
		private $modal_url_view					= 'views/url_blocker_modal.php';
		private $modal_mailto_view				= 'views/mailto_blocker_modal.php';

		/**
		 * Modal Container Set
		 * 
		 * @access		private
		 * @since		0.1
		 * @var			bool
		 */
		 private $modal_url_container		= FALSE;
		 private $modal_mailto_container	= FALSE;

		/**
		 * Hook
		 *
		 * Adds the filter to "the_content".
		 *
		 * @access		public
		 * @since		0.1
		 * @return		void
		 * @static
		 */
		public static function hook()
		{
			$that = new self();

			// Is the class enabled in the config and is the user outside of the IP address range.
			if ($that->config['switch']->wtg_strip_links && ! wtg_ip_addresses::is_whitelist_ip_address())
			{
				add_filter('the_content', array($that, 'content_filter'));
			}
		}

		/**
		 * Clean
		 *
		 * Static function allowing cleaning of data without using filters and hooks. Tried all the acf filters and
		 * couldn't get it to do it automatically, so no other option than to do it this way.
		 *
		 * @access		public
		 * @since		0.2
		 * @static
		 * @param		string		$data
		 * @return		string
		 */
		public static function clean($data)
		{
			$that = new self();

			// Is the class enabled in the config and is the user outside of the IP address range.
			if ($that->config['switch']->wtg_strip_links && ! wtg_ip_addresses::is_whitelist_ip_address())
			{
				return $that->content_filter($data);
			}

			return $data;
		}

		/**
		 * Content filter
		 *
		 * Gets the DOM, goes through each 'a' tag and runs our filters on them, then saves the dom and returns the 
		 * content with the required modal containers.
		 *
		 * @access		public
		 * @since		0.1
		 * @param		string		$content
		 * @return		string
		 */
		public function content_filter($content)
		{
			// Get the DOMDocument object.
			$dom = $this->get_dom($content);

			if (! $dom)
			{
				return $content;
			}
			
			foreach ($dom->getElementsByTagName('a') as $node)
			{
				$node = $this->mailto_filter($node);
				$node = $this->anchor_filter($node);
			}
			
			// Turn the $dom back into a string.
			$content = $this->save_dom($dom);

			// Tag on the containers at the end of the content.
			return utf8_encode($content) . $this->modal_url_container . $this->modal_mailto_container;
		}

		/**
		 * Save DOM
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		DOMDocument		$dom
		 * @return		self::remove_html_wrapper
		 */
		private function save_dom(DOMDocument $dom)
		{
			// Return the $dom->saveHTML() after running it through the remove_html_wrapper().  This might look hacky,
			// but PHP doesn't have a flag for DOMDocument to prevent the HTML wrapper getting added by default.
			return $this->remove_html_wrapper($dom->saveHTML());
		}

		/**
		 * Get DOM
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		string		$content
		 * @return		DOMDocument
		 */
		private function get_dom($content)
		{
			if (! $content || empty($content) || $content === '')
			{
				return false;
			}

			// Start up DOMDocument and pull in the string whilst preserving white space.
			$dom = new DOMDocument('1.0', 'UTF-8');

			// Hack the content so that the "loadHTML" method thinks this is UTF-8 content.
			$dom->loadHTML('<?xml encoding="UTF-8">' . $content);

			// Go through all the child nodes
			foreach ($dom->childNodes as $item)
			{
				// If the node type is a DOMProcessingInstruction, then sack it off.
				if($item->nodeType == XML_PI_NODE)
				{
					$dom->removeChild($item);
				}
			}

			// Set the encoding properly here again (already set in constructor).
			$dom->encoding = 'UTF-8';

			// Keep the whitespace.
			$dom->preserveWhiteSpace = TRUE;

			return $dom;
		}

		
		/**
		 * Mailto Filter
		 * 
		 * Finds all "mailto" href's and checks if they are allowed or not.  If they are not, we change the link to be
		 * the modal link and tag on the original mailto reference so it can appear in our window.  Sets up the 
		 * modal_mailto_container to tag on the end of the content later.
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		DOMElement		$node
		 * @return		DOMElement
		 */
		private function mailto_filter(DOMElement $node)
		{
			// Does the $node have the attribute 'href', is it a mailto and does it match one of $this->domains.
			if (
					$node->hasAttribute('href') && 
					strpos(trim($node->getAttribute('href')), 'mailto:') === 0 && 
					$this->domain_matcher($node->getAttribute('href'), 'mailto_removals')
				)
			{
				$node->setAttribute('href', $this->plugin_url . $this->modal_mailto_view . '?mailto=' . 
					$node->getAttribute('href'));
				$node->setAttribute('data-toggle', 'modal');
				$node->setAttribute('data-target', '#mailto_modal');
				
				// Set the modal container set variable so that we don't get more than one container on the page.
				if ( ! $this->modal_mailto_container)
				{
					$this->modal_mailto_container = $this->view('modal_container', array("id" => "mailto_modal",
						"aria_label" => "mailto_modal"));
				}
			}
			return $node;
		}

		/**
		 * Anchor filter
		 * 
		 * Find all anchor tags, then loop through them.  When we find an anchor tag that contains a url that matches 
		 * one of our blocked domains and replace it with $this->url_modal_view link.
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		DOMElement		$dom
		 * @return		DOMElement
		 */
		private function anchor_filter(DOMElement $node)
		{
			// Does the $node have the attribute 'href', is it NOT a mailto and does it match one of the blocked 
			// domains.
			if (
					$node->hasAttribute('href') && 
					strpos(trim($node->getAttribute('href')), 'mailto:') !== 0 && 
					$this->domain_matcher($node->getAttribute('href'), array('domain_removals', 'sharepoint_removals'))
				)
			{
				// Set a share point URL parameter.
				$sharepoint = $this->domain_matcher($node->getAttribute('href'), 'sharepoint_removals') ? 
					'?type=sharepoint' : NULL;
					
				// Set the link to the modal view link and a data toggle attribute.
				$node->setAttribute('href', $this->plugin_url . $this->modal_url_view . $sharepoint);
				$node->setAttribute('data-toggle', 'modal');
				$node->setAttribute('data-target', '#link_modal');

				// Set the modal container set variable so that we don't get more than one container on the page.
				if ( ! $this->modal_url_container)
				{
					$this->modal_url_container = $this->view('modal_container', array("id" => "link_modal", 
						"aria_label" => "link_modal"));
				}
			}
			
			// Return the DOMELement.
			return $node;
		}

		/**
		 * Domain matcher
		 * 
		 * Using the domains in the config file, we check if $matcher is in $text and return TRUE if so, else FALSE.
		 * 
		 * @access		private
		 * @since		0.1
		 * @param		string		$text
		 * @param		string		$config_var
		 * @return		bool
		 */
		private function domain_matcher($text, $config_var)
		{
			// Is the $config_var an array?
			if (is_array($config_var))
			{
				// Go through each array value.
				foreach($config_var as $sub_config_var)
				{
					// Check myself for a value and return TRUE.
					if ($this->domain_matcher($text, $sub_config_var))
					{
						return TRUE;
					}
				}
				
				// No matches found, return FALSE.
				return FALSE;
			}
			
			// Loop through each of the values in the config.
			foreach($this->config[$config_var] as $matcher)
			{
				// If the needle is in the haystack, return TRUE;
				if ($matcher === 'all' || strpos($text, $matcher))
				{
					return TRUE;
				}
			}
			
			// No matches found, return FALSE.
			return FALSE;
		}
	}
}