<?php

/*
Plugin Name: DH Intranet
Description: Adds some extra bells and whistles for the DH Intranet site
Author: dxw
Author URI: https://www.dxw.com/
*/

require __DIR__.'/vendor.phar';

// Autoload
$loader = new \Aura\Autoload\Loader;
$loader->register();
$loader->addPrefix('DHIntranetPlugin', __DIR__.'/src');

\DHIntranetPlugin\Shortcode::register();
\DHIntranetPlugin\EventsTemplate::register();
