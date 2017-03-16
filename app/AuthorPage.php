<?php

namespace DHIntranet;

use Dxw\Iguana\Registerable;

class AuthorPage implements Registerable
{
    public function register()
    {
        add_action('template_redirect', [$this, 'disable']);
        add_filter('author_link', [$this, 'removeAuthorLink']);
    }

    public function disable()
    {
        if (is_author()) {
            $this->_404();
        }
    }

    public function removeAuthorLink($link)
    {
        return '';
    }

    private function _404()
    {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
}
