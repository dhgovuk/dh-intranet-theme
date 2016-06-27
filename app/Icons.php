<?php

namespace DHIntranet;

class Icons
{
    public function __construct(\Dxw\Iguana\Theme\Helpers $helpers)
    {
        $helpers->registerFunction('svgIcon', [$this, 'svgIcon']);
    }

    public function svgIcon($name)
    {
        require(__DIR__.'/../static/img/icon-'.$name.'.svg');
    }
}
