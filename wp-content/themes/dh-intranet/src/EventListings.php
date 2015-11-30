<?php

namespace DHIntranet;

class EventListings
{
    public function __construct()
    {
        add_shortcode('eventbrite_event_list2', [$this, 'doShortcode']);
    }

    public function doShortcode()
    {
        return $this->renderEvents(
            $this->getCurrentEvents(
                \WPMissing\Util::strftime(time(), '%Y-%m-%d %H:%M:%S', 'UNKNOWN', 'Etc/UTC'),
                array_map([$this, 'getEventFromID'], $this->getEventIDs($_GET))
            )
        );
    }

    public function renderEvent($e)
    {
        $e = $this->normalise($e);

        $fmt = '%e %B %Y %H:%M';
        $start = \WPMissing\Util::strftime($e['start'], $fmt, 'UNKNOWN');
        $end = \WPMissing\Util::strftime($e['end'], $fmt, 'UNKNOWN');

        return '
        <article class="rich-text event-item">
            <h2><a href="'.esc_attr($e['link']).'" title="'.esc_attr($e['title']).'">'.esc_html($e['title']).'</a></h2>
            <ul class="event-meta">
                <li><strong>Start time:</strong> '.esc_html($start).'</li>
                <li><strong>End time:</strong> '.esc_html($end).'</li>
                <li><strong>Address:</strong> '.esc_html($e['address']).'</li>
            </ul>
            <div class="article-content">
                <p>'.$e['description']/* unescaped - this comes from the_content */.'</p>
            </div>
            <a href="'.esc_attr($e['link']).'" title="'.esc_attr($e['title']).'" class="button">See more</a>
        </article>
        ';
    }

    public function renderEvents($events)
    {
        return implode('<hr>', array_map([$this, 'renderEvent'], $events));
    }

    public function normalise($e)
    {
        $keys = [
            'address',
            'description',
            'end',
            'link',
            'start',
            'title',
        ];
        foreach ($keys as $key) {
            if (!isset($e[$key])) {
                $e[$key] = '';
            }
        }

        return $e;
    }

    public function getEventIDs($get)
    {
        $events = \Event_Categories::get_categories_and_events();
        if (isset($get['cat']) && isset($events[$get['cat']])) {
            return $events[$get['cat']];
        }

        return [];
    }

    public function getEventFromID($id)
    {
        $p = get_post($id);

        // Convert from timezone stored in post to timezone stored in options
        $eventTz = new \DateTimeZone(get_post_meta($id, 'timezone', true));
        $siteTz = new \DateTimeZone(get_option('timezone_string'));
        $start = new \DateTime(get_post_meta($id, 'start_date', true), $eventTz);
        $end = new \DateTime(get_post_meta($id, 'end_date', true), $eventTz);
        $start->setTimezone($siteTz);
        $end->setTimezone($siteTz);
        $_start = \Missing\Dates::strftime($start->getTimestamp(), '%Y-%m-%d %H:%M:%S UTC', 'ERROR', 'Etc/UTC');
        $_end = \Missing\Dates::strftime($end->getTimestamp(), '%Y-%m-%d %H:%M:%S UTC', 'ERROR', 'Etc/UTC');

        return [
            'title' => get_the_title($id),
            'link' => get_permalink($id),
            'description' => $p->post_content,
            'start' => $_start,
            'end' => $_end,
            'address' => get_post_meta($id, 'venue', true).', '.get_post_meta($id, 'adress', true),
            'post_status' => $p->post_status,
        ];
    }

    public function getCurrentEvents($now, $events)
    {
        return \Missing\Arrays::sortBy(array_filter($events, function ($a) use ($now) {
            return $a['post_status'] === 'publish' && strcmp($now, $a['start']) < 0;
        }), function ($a) { return $a['start']; });
    }
}
