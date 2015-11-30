<?php

// Check if the plugin is loaded.
if (class_exists('Event_Categories')) {
    // Number of events to be displayed per row.
    $display_events = 3;

    // Get jsoned active categories set by user and decode.
    $active_categories = Event_Categories::get_category_states();

    // Get all category names that exist.
    $all_categories = Event_Categories::get_all_categories();

    // Get all category ids and their events.
    $category_events = Event_Categories::get_categories_and_events();

	// Length for truncating the event titles.
	$event_title_length = Event_Categories::get_event_title_length();

    // Loop through each enabled category.
    foreach ($active_categories as $category => $state) {
        $start_dates = array();

        $post_meta_list = array();

        // If category is set to be displayed.
        if ($state == 'display') {
            if (isset($category_events[$category])) {
                foreach ($category_events[$category] as $post_id) {
                    $post_meta = get_post_meta($post_id);
                    $post = get_post($post_id);

                    $post_meta_list[$post_id]['post'] = get_post($post_id);
                    $post_meta_list[$post_id]['meta'] = $post_meta;
                    $current_date = date('Y-m-d H:i:s');

                    // Start time cannot be null and the event end date has to be after the current date.
                    if (!is_null($post_meta['start_date'][0])
                        && $post_meta['end_date'][0] > $current_date
                        && $post->post_status == 'publish'
                    ) {
                        $start_dates[$post_id] = $post_meta['start_date'][0];
                    }
                }
            }
        }

        // If start_dates array is populated,
        // that means we have events to display.
        if (count($start_dates)) {
            // Display the category name.
            ?>

                    <h2>
                        <a href="/events?cat=<?php echo $category; ?>"><?php echo $all_categories[$category]; ?></a>
                    </h2>

            <?php

            // Sort the categories by date ascending.
            asort($start_dates);
            $event_counter = 0;
            // container for all the items
            $items = array();

            foreach ($start_dates as $post_id => $start_date) {
                array_push($items, array(
                    'wp_link' =>  get_post_permalink($post_id),
                    'start_day' => date('j', strtotime($post_meta_list[$post_id]['meta']['start_date'][0])),
                    'start_month' => date('M', strtotime($post_meta_list[$post_id]['meta']['start_date'][0])),
                    'start_formatted' => date('j F Y H:i', strtotime($post_meta_list[$post_id]['meta']['start_date'][0])),
                    'title' => wtg_truncate_string($post_meta_list[$post_id]['post']->post_title, $event_title_length)
                ));
            }

            ?>

            <div class="events-aggregation">
                <div id="event-cat-<?php echo $category ?>"></div>
                <?php wtg_slider("#event-cat-{$category}", $items, array('tpl' => 'events', 'limit' => 6, 'colClass' => '')) ?>
            </div>

            <?php
            if (count($start_dates) > 3) {
                ?>
                <div class="cta">
                    <a href="/events?cat=<?php echo $category; ?>"class="button">More Events</a>
                </div>
            <?php
            }
        }
    }
} else {
    echo 'Event Categories plugin is not activated.';
}

function truncate($str, $width)
{
    return strtok(wordwrap($str, $width, "...\n"), "\n");
}
