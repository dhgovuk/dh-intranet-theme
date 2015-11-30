<?php

namespace DHIntranet;

class Search
{
    public function __construct($get)
    {
        $this->get = $get;
    }

    public function register()
    {
        add_action('parse_query', [$this, 'parseQuery']);
    }

    public function parseQuery($query)
    {
        if (is_admin() || !$query->is_search() || !$query->is_main_query()) {
            return;
        }

        if (isset($this->get['exclude-news']) && $this->get['exclude-news'] === 'yes') {
            $query->set('post_type', ['page', 'event']);
        } else {
            $query->set('post_type', ['post', 'page', 'event']);
        }
    }
}
