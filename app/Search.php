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

        $types = ['page', 'event', 'todo-item', 'local-news'];



        if (!isset($this->get['exclude-news']) || !$this->get['exclude-news'] === 'yes') {
            $types[] = 'post';
        }

        if (isset($this->get['post_type']) && $this->get['post_type'] === 'policy-kit') {
            $types = ['policy-kit'];
        }


        $query->set('post_type', $types);
    }
}
