<?php

namespace DHIntranetPlugin;

class EventsTemplate
{
    public static function register()
    {
        add_filter('template_include', function ($a) {
            if (
                \Missing\Strings::endsWith($a, '/wp-content/themes/dh-intranet/templates/single.php')
                &&
                get_post_type() === 'event'
            ) {
                return __DIR__.'/../templates/single-event.php';
            }

            return $a;
        });
    }
}
